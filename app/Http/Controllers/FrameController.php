<?php

namespace App\Http\Controllers;

use App\Models\Conversion;
use App\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download(Conversion $conversion, Frame $frame)
    {
        $this->authorize('view', $conversion);
        
        if (!$frame->conversion_id === $conversion->id) {
            abort(404);
        }
        
        if (!Storage::disk('public')->exists($frame->file_path)) {
            return redirect()->route('conversions.show', $conversion)
                ->with('error', 'Frame não encontrado.');
        }
        
        return response()->download(
            Storage::disk('public')->path($frame->file_path),
            $frame->filename
        );
    }
}