<?php

namespace App\Http\Controllers;

use App\Models\Conversion;
use App\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConversionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conversions = auth()->user()->conversions()->latest()->paginate(10);
        return view('conversions.index', compact('conversions'));
    }

    public function create()
    {
        $remainingConversions = auth()->user()->remaining_daily_conversions;
        
        if ($remainingConversions <= 0) {
            return redirect()->route('conversions.index')
                ->with('error', 'Você atingiu o limite diário de conversões do plano gratuito.');
        }
        
        return view('conversions.create', compact('remainingConversions'));
    }

    public function store(Request $request)
    {
        $remainingConversions = auth()->user()->remaining_daily_conversions;
        
        if ($remainingConversions <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Você atingiu o limite diário de conversões do plano gratuito.'
            ], 403);
        }
        
        $request->validate([
            'videoFile' => 'required|file|mimes:mp4,avi,mov,wmv|max:102400', // 100MB
            'frameRate' => 'required|string',
            'customRate' => 'nullable|integer|min:1',
            'quality' => 'required|in:low,medium,high',
        ]);
        
        // Get the actual frame rate value
        $frameRate = $request->frameRate;
        if ($frameRate === 'custom') {
            $frameRate = $request->customRate;
        }
        
        $video = $request->file('videoFile');
        $originalFilename = $video->getClientOriginalName();
        $fileSize = $video->getSize();
        
        // Generate unique job ID
        $jobId = Str::uuid()->toString();
        
        // Create conversion record
        $conversion = auth()->user()->conversions()->create([
            'original_filename' => $originalFilename,
            'file_size' => $fileSize,
            'status' => 'uploading',
            'frame_rate' => $frameRate,
            'quality' => $request->quality,
            'job_id' => $jobId,
        ]);
        
        // Store the video file
        $videoPath = $video->storeAs(
            'videos/' . auth()->id(),
            $jobId . '.' . $video->getClientOriginalExtension(),
            'public'
        );
        
        // Update status to processing
        $conversion->update(['status' => 'processing']);
        
        // Process the video (in a real app, this would be a queued job)
        try {
            $this->processVideo($conversion, $videoPath);
            return response()->json([
                'success' => true,
                'jobId' => $jobId,
                'redirectUrl' => route('conversions.show', $conversion)
            ]);
        } catch (\Exception $e) {
            $conversion->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o vídeo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Conversion $conversion)
    {
        $this->authorize('view', $conversion);
        
        $frames = $conversion->frames()->orderBy('frame_number')->get();
        
        return view('conversions.show', compact('conversion', 'frames'));
    }

    protected function processVideo(Conversion $conversion, $videoPath)
    {
        // In a real application, this would be a queued job
        // For simplicity, we'll do it synchronously here
        
        // Create frames directory
        $framesDir = 'frames/' . auth()->id() . '/' . $conversion->job_id;
        Storage::disk('public')->makeDirectory($framesDir);
        
        // Full path to video file
        $videoFullPath = Storage::disk('public')->path($videoPath);
        
        // Configure quality
        switch ($conversion->quality) {
            case 'low':
                $jpegQuality = 70;
                break;
            case 'high':
                $jpegQuality = 95;
                break;
            case 'medium':
            default:
                $jpegQuality = 85;
                break;
        }
        
        // Extract frames using FFmpeg
        $outputPattern = Storage::disk('public')->path($framesDir) . '/frame_%04d.jpg';
        $frameRateCmd = ($conversion->frame_rate == 1) ? '' : "-vf \"select=not(mod(n\\,{$conversion->frame_rate}))\"";
        $qualityParam = 10 - (int)($jpegQuality / 10);
        
        $command = "ffmpeg -i \"{$videoFullPath}\" {$frameRateCmd} -q:v {$qualityParam} \"{$outputPattern}\" 2>&1";
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception(implode("\n", $output));
        }
        
        // Count extracted frames and save them to the database
        $frameFiles = glob(Storage::disk('public')->path($framesDir) . '/frame_*.jpg');
        $frameCount = count($frameFiles);
        
        if ($frameCount === 0) {
            throw new \Exception('Nenhum frame foi extraído do vídeo.');
        }
        
        // Save frame records
        foreach ($frameFiles as $index => $filePath) {
            $filename = basename($filePath);
            $frameNumber = $index + 1;
            
            $conversion->frames()->create([
                'filename' => $filename,
                'frame_number' => $frameNumber,
                'file_path' => $framesDir . '/' . $filename,
            ]);
        }
        
        // Create ZIP file
        $zipFileName = $conversion->job_id . '_frames.zip';
        $zipPath = 'zips/' . auth()->id() . '/' . $zipFileName;
        
        Storage::disk('public')->makeDirectory('zips/' . auth()->id());
        
        $zip = new \ZipArchive();
        $zipFullPath = Storage::disk('public')->path($zipPath);
        
        if ($zip->open($zipFullPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($frameFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        } else {
            throw new \Exception('Erro ao criar arquivo ZIP.');
        }
        
        // Update conversion record
        $conversion->update([
            'status' => 'completed',
            'frame_count' => $frameCount,
            'completed_at' => now(),
        ]);
        
        // Clean up the original video to save space
        Storage::disk('public')->delete($videoPath);
        
        return true;
    }

    public function downloadZip(Conversion $conversion)
    {
        $this->authorize('view', $conversion);
        
        if ($conversion->status !== 'completed') {
            return redirect()->route('conversions.show', $conversion)
                ->with('error', 'A conversão ainda não foi concluída.');
        }
        
        $zipPath = 'zips/' . auth()->id() . '/' . $conversion->job_id . '_frames.zip';
        
        if (!Storage::disk('public')->exists($zipPath)) {
            return redirect()->route('conversions.show', $conversion)
                ->with('error', 'Arquivo ZIP não encontrado.');
        }
        
        return response()->download(
            Storage::disk('public')->path($zipPath),
            'frames_' . Str::slug($conversion->original_filename, '_') . '.zip'
        );
    }
}