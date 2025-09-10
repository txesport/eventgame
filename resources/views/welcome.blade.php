<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EventFun') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .hero-content {
            animation: fadeInUp 1s ease-out;
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 20px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .hero-buttons {
            animation: fadeInUp 1s ease-out 0.6s both;
        }
        
        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary-hero {
            background: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 20px 40px rgba(255, 107, 107, 0.6);
            color: white;
        }
        
        .btn-outline-hero {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
        }
        
        .btn-outline-hero:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-3px) scale(1.05);
            color: white;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            font-size: 3rem;
        }
        
        .floating-element:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
            font-size: 2rem;
        }
        
        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
            font-size: 2.5rem;
        }
        
        .floating-element:nth-child(4) {
            bottom: 30%;
            right: 20%;
            animation-delay: 1s;
            font-size: 1.5rem;
        }
        
        .features-preview {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.8);
            text-align: center;
            animation: fadeInUp 1s ease-out 1s both;
        }
        
        .feature-badge {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            margin: 0.25rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
            }
            25% { 
                transform: translateY(-20px) rotate(5deg); 
            }
            50% { 
                transform: translateY(-10px) rotate(-5deg); 
            }
            75% { 
                transform: translateY(-15px) rotate(3deg); 
            }
        }
        
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .particle {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: particle-float 8s infinite linear;
        }
        
        @keyframes particle-float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .btn-hero {
                padding: 0.8rem 2rem;
                font-size: 1rem;
                display: block;
                margin-bottom: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <!-- Particules flottantes -->
        <div class="particles" id="particles"></div>
        
        <!-- Éléments flottants -->
        <div class="floating-elements">
            <i class="bi bi-calendar-event floating-element"></i>
            <i class="bi bi-people floating-element"></i>
            <i class="bi bi-chat-heart floating-element"></i>
            <i class="bi bi-emoji-smile floating-element"></i>
        </div>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="hero-content">
                        <h1 class="hero-title">{{ config('app.name', 'EventFun') }}</h1>
                        <p class="hero-subtitle">
                            Organisez vos événements, votez ensemble et partagez vos dépenses en toute simplicité.
                        </p>
                        <div class="hero-buttons">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn-hero btn-primary-hero me-3">
                                        <i class="bi bi-speedometer2"></i>
                                        Tableau de bord
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-hero btn-primary-hero me-3">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        Connexion
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn-hero btn-outline-hero">
                                            <i class="bi bi-person-plus"></i>
                                            Inscription
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Aperçu des fonctionnalités -->
        <div class="features-preview">
            <div class="mb-3">
                <small>Fonctionnalités incluses :</small>
            </div>
            <div>
                <span class="feature-badge">
                    <i class="bi bi-people"></i> Groupes d'amis
                </span>
                <span class="feature-badge">
                    <i class="bi bi-calendar-event"></i> Événements
                </span>
                <span class="feature-badge">
                    <i class="bi bi-hand-thumbs-up"></i> Votes
                </span>
                <span class="feature-badge">
                    <i class="bi bi-wallet2"></i> Dépenses partagées
                </span>
            </div>
        </div>
    </div>

    
</body>
</html>
