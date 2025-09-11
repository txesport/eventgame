<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EventMate - Organisez vos événements entre amis</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <div class="hero-bg d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">
                        Bienvenue sur <span class="text-warning">EventMate</span>
                    </h1>
                    <p class="lead mb-4">
                        Votre compagnon idéal pour organiser des événements inoubliables entre amis. 
                        Créez, planifiez et partagez vos moments ensemble !
                    </p>
                    <div class="d-flex gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-house-door"></i> Mon Tableau de Bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Se Connecter
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-person-plus"></i> S'Inscrire
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('images/eventmate-logo.png') }}" alt="EventMate Logo" class="img-fluid" style="max-width: 400px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold">Pourquoi choisir EventMate ?</h2>
                    <p class="lead text-muted">Tout ce qu'il faut pour organiser vos événements</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h4>Créez vos groupes</h4>
                        <p class="text-muted">Rassemblez vos amis dans des groupes pour organiser vos événements ensemble.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-calendar3 text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h4>Planifiez facilement</h4>
                        <p class="text-muted">Proposez des dates et activités, et laissez vos amis voter pour leurs préférences.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="bi bi-camera-fill text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h4>Partagez vos souvenirs</h4>
                        <p class="text-muted">Uploadez vos photos d'événements et gardez vos souvenirs en commun.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} EventMate. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>