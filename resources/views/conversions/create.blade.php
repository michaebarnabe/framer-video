<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nova Conversão') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="mb-4">Você tem <strong>{{ $remainingConversions }}</strong> conversões restantes hoje (plano gratuito).</p>

                    <form id="videoForm" action="{{ route('conversions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4 p-6 border-2 border-dashed border-gray-300 rounded-lg text-center bg-gray-50" id="upload-container">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <h5 class="mt-2 text-sm font-medium text-gray-900">Arraste e solte seu vídeo aqui</h5>
                            <p class="mt-1 text-xs text-gray-500">ou</p>
                            <div class="mt-2">
                                <input type="file" id="videoFile" name="videoFile" class="hidden" accept="video/*" required>
                                <label for="videoFile" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                                    Selecionar Vídeo
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">MP4, AVI, MOV, WMV até 100MB</p>
                            <div id="file-info" class="mt-2 hidden">
                                <p class="text-sm text-gray-700">Arquivo: <span id="file-name"></span></p>
                                <p class="text-sm text-gray-700">Tamanho: <span id="file-size"></span></p>
                            </div>
                        </div>

                        <div class="mb-4 bg-white rounded-lg shadow overflow-hidden">
                            <div class="px-4 py-5 bg-gray-50 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Configurações de Extração
                                </h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <div class="mb-4">
                                    <label for="frameRate" class="block text-sm font-medium text-gray-700">Intervalo de Frames</label>
                                    <select id="frameRate" name="frameRate" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="1">Cada frame</option>
                                        <option value="5" selected>A cada 5 frames</option>
                                        <option value="10">A cada 10 frames</option>
                                        <option value="30">A cada segundo (30 fps)</option>
                                        <option value="60">A cada 2 segundos</option>
                                        <option value="custom">Personalizado</option>
                                    </select>
                                </div>
                                <div id="customFrameRate" class="mb-4 hidden">
                                    <label for="customRate" class="block text-sm font-medium text-gray-700">Intervalo personalizado (frames)</label>
                                    <input type="number" id="customRate" name="customRate" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="5" min="1">
                                </div>
                                <div class="mb-4">
                                    <label for="quality" class="block text-sm font-medium text-gray-700">Qualidade da Imagem</label>
                                    <select id="quality" name="quality" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="low">Baixa (mais rápido)</option>
                                        <option value="medium" selected>Média</option>
                                        <option value="high">Alta (mais lento)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="convert-btn" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Converter Vídeo
                        </button>
                    </form>

                    <div id="progress-container" class="mt-4 hidden">
                        <h5 class="text-lg font-medium text-gray-900">Processando vídeo...</h5>
                        <div class="relative pt-1">
                            <div class="overflow-hidden h-6 mb-2 text-xs flex rounded bg-blue-200">
                                <div id="progress-bar" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500" style="width: 0%"></div>
                            </div>
                            <p class="text-center text-sm font-medium text-gray-700" id="progress-text">0%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Custom frame rate toggle
            const frameRateSelect = document.getElementById('frameRate');
            const customFrameRate = document.getElementById('customFrameRate');
            
            frameRateSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customFrameRate.classList.remove('hidden');
                } else {
                    customFrameRate.classList.add('hidden');
                }
            });

            // File upload handling
            const form = document.getElementById('videoForm');
            const fileInput = document.getElementById('videoFile');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const convertBtn = document.getElementById('convert-btn');
            
            // Show file info when file is selected
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    fileInfo.classList.remove('hidden');
                } else {
                    fileInfo.classList.add('hidden');
                }
            });
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check if file is selected
                if (fileInput.files.length === 0) {
                    alert('Por favor, selecione um arquivo de vídeo.');
                    return;
                }
                
                // Show progress
                progressContainer.classList.remove('hidden');
                convertBtn.disabled = true;
                
                // Create FormData object
                const formData = new FormData(form);
                
                // Send AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                // Setup progress tracking
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        updateProgress(percent);
                    }
                };
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                updateProgress(100);
                                setTimeout(function() {
                                    window.location.href = response.redirectUrl;
                                }, 500);
                            } else {
                                showError(response.message || 'Erro desconhecido');
                            }
                        } catch (e) {
                            showError('Erro ao processar resposta do servidor');
                        }
                    } else {
                        showError('Erro no servidor: ' + xhr.status);
                    }
                };
                
                xhr.onerror = function() {
                    showError('Erro de rede');
                };
                
                xhr.send(formData);
            });
            
            function updateProgress(percent) {
                progressBar.style.width = percent + '%';
                progressText.textContent = percent + '%';
            }
            
            function showError(message) {
                progressContainer.classList.add('hidden');
                convertBtn.disabled = false;
                alert('Erro: ' + message);
            }
            
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            // Drag and drop functionality
            const uploadContainer = document.getElementById('upload-container');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                uploadContainer.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                uploadContainer.classList.add('border-blue-500');
                uploadContainer.classList.add('bg-blue-50');
            }
            
            function unhighlight() {
                uploadContainer.classList.remove('border-blue-500');
                uploadContainer.classList.remove('bg-blue-50');
            }
            
            uploadContainer.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                
                // Trigger change event
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
    @endpush
</x-app-layout>