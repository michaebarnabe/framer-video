<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes da Conversão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('conversions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    &larr; Voltar para Conversões
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informações da Conversão</h3>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Arquivo Original</p>
                                <p class="font-medium">{{ $conversion->original_filename }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Data</p>
                                <p class="font-medium">{{ $conversion->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="font-medium">
                                    @if($conversion->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Concluído
                                        </span>
                                    @elseif($conversion->status === 'processing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Processando
                                        </span>
                                    @elseif($conversion->status === 'failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Falhou
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($conversion->status) }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Intervalo de Frames</p>
                                <p class="font-medium">A cada {{ $conversion->frame_rate }} frames</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Qualidade</p>
                                <p class="font-medium">
                                    @if($conversion->quality === 'low')
                                        Baixa
                                    @elseif($conversion->quality === 'medium')
                                        Média
                                    @elseif($conversion->quality === 'high')
                                        Alta
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tamanho do Arquivo</p>
                                <p class="font-medium">{{ number_format($conversion->file_size / 1048576, 2) }} MB</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($conversion->status === 'completed')
                        <div class="mb-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Frames Extraídos ({{ $conversion->frame_count }})</h3>
                                <a href="{{ route('conversions.download', $conversion) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Baixar Todos (ZIP)
                                </a>
                            </div>
                            
                            @if($frames->count() > 0)
                                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach($frames as $frame)
                                        <div class="relative group">
                                            <img src="{{ Storage::disk('public')->url($frame->file_path) }}" alt="Frame {{ $frame->frame_number }}" class="w-full h-24 object-cover rounded-md shadow-sm">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                <a href="{{ route('frames.download', [$conversion, $frame]) }}" class="text-white bg-blue-600 hover:bg-blue-700 p-1 rounded-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            </div>
                                            <p class="text-xs mt-1 text-center text-gray-500">Frame {{ $frame->frame_number }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-4 bg-gray-100 p-4 rounded">
                                    <p>Nenhum frame encontrado.</p>
                                </div>
                            @endif
                        </div>
                    @elseif($conversion->status === 'processing')
                        <div class="mt-4 bg-yellow-50 p-4 rounded">
                            <p>Sua conversão está sendo processada. Por favor, atualize a página em alguns instantes.</p>
                            <div class="relative pt-1 mt-2">
                                <div class="overflow-hidden h-4 mb-2 text-xs flex rounded bg-yellow-200">
                                    <div class="animate-pulse w-full shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                                </div>
                            </div>
                        </div>
                    @elseif($conversion->status === 'failed')
                        <div class="mt-4 bg-red-50 p-4 rounded">
                            <p>Ocorreu um erro durante o processamento desta conversão. Por favor, tente novamente ou entre em contato com o suporte.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>