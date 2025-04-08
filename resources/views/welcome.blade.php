<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VideoFrames - Conversor de Vídeo para JPG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 60px 0;
        }
        .features-section {
            padding: 50px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">VideoFrames</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Recursos</a>
                    </li>
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link btn btn-primary text-white" href="{{ route('register') }}">Registrar</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Extraia frames JPG de qualquer vídeo</h1>
                    <p class="lead">Converta seus vídeos em sequências de imagens JPG com apenas alguns cliques. Ideal para análise de vídeo, criação de thumbnails e muito mais.</p>
                    @auth
                        <a href="{{ route('conversions.create') }}" class="btn btn-primary btn-lg">Começar agora</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Registre-se gratuitamente</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg ms-2">Login</a>
                    @endauth
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('images/hero-image.svg') }}" alt="Video to Frames Illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="text-center mb-5">Recursos</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Conversão Rápida</h5>
                            <p class="card-text">Processamento otimizado para extrair frames de vídeos de qualquer tamanho em segundos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Alta Qualidade</h5>
                            <p class="card-text">Mantenha a qualidade original do seu vídeo nos frames extraídos, com opções de resolução personalizáveis.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Download Fácil</h5>
                            <p class="card-text">Baixe frames individuais ou todos de uma vez em um arquivo ZIP organizado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Free Plan Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h2 class="mb-4">Comece Gratuitamente</h2>
                    <p class="lead mb-4">Nosso plano gratuito oferece 5 conversões por dia, ideal para uso casual e testes.</p>
                    @auth
                        <a href="{{ route('conversions.create') }}" class="btn btn-primary btn-lg">Começar a converter</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Criar conta gratuita</a>
                        <div class="mt-3">
                            <a href="{{ route('login.google') }}" class="btn btn-outline-dark">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                                    <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                                </svg>
                                Entrar com Google
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>VideoFrames</h5>
                    <p>Solução simples e eficiente para extração de frames de vídeos.</p>
                </div>
                <div class="col-md-4">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-white">Recursos</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-white">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-white">Login</a></li>
                            <li><a href="{{ route('register') }}" class="text-white">Registrar</a></li>
                        @endauth
                        <li><a href="#" class="text-white">Termos de Uso</a></li>
                        <li><a href="#" class="text-white">Política de Privacidade</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <p>contato@videoframes.com.br<br>+55 (11) 1234-5678</p>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center">© {{ date('Y') }} VideoFrames. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>