<x-app-layout>
    @section('title', 'Tableau de bord')
    
    <!-- Hero Section -->
    <div class="hero-section text-center mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">
                Bonjour {{ $user->name }} ! 👋
            </h1>
            <p class="lead">Bienvenue sur EventMate, votre compagnon pour organiser des événements inoubliables</p>
        </div>
    </div>

    <div class="container">
        <!-- Statistiques -->
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold text-primary mt-2">{{ $stats['groups'] }}</h3>
                        <p class="text-muted mb-0">Groupes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-calendar3 text-success" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold text-success mt-2">{{ $stats['events'] }}</h3>
                        <p class="text-muted mb-0">Événements créés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="bi bi-camera-fill text-warning" style="font-size: 3rem;"></i>
                        <h3 class="fw-bold text-warning mt-2">{{ $stats['photos'] }}</h3>
                        <p class="text-muted mb-0">Photos partagées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités populaires -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="h4 mb-4">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Idées d'activités populaires
                </h2>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-primary activity-card">
                    <div class="activity-card-image">
                        <img src="{{ asset('images/activity-restaurant.png') }}" alt="Restaurant">
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title">🍽️ Restaurant</h6>
                        <p class="card-text small text-muted">Découvrez de nouveaux restaurants entre amis</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-info activity-card">
                    <div class="activity-card-image">
                        <img src="{{ asset('images/activity-cinema.png') }}" alt="Cinéma">
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title">🎬 Cinéma</h6>
                        <p class="card-text small text-muted">Partagez vos films préférés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-success activity-card">
                    <div class="activity-card-image">
                        <img src="{{ asset('images/activity-sport.png') }}" alt="Sport">
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title">⚽ Sport</h6>
                        <p class="card-text small text-muted">Bougez ensemble et amusez-vous</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100 border-warning activity-card">
                    <div class="activity-card-image">
                        <img src="{{ asset('images/activity-party.png') }}" alt="Fête">
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title">🎉 Fête</h6>
                        <p class="card-text small text-muted">Célébrez les moments importants</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mes événements récents & Mes groupes -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card h-100 event-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar3 text-primary me-2"></i>
                            Mes événements récents
                        </h5>
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Nouveau
                        </a>
                    </div>
                    <div class="card-body">
                        @if($recentEvents->count() > 0)
                            @foreach($recentEvents as $event)
                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <div>
                                        <h6 class="mb-1">{{ $event->name }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>{{ $event->group->name }}
                                            <i class="bi bi-clock ms-2 me-1"></i>{{ $event->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </div>
                            @endforeach
                            <div class="text-center mt-3">
                                <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                                    Voir tous mes événements
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Vous n'avez pas encore créé d'événement.</p>
                                <a href="{{ route('events.create') }}" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus"></i> Créer mon premier événement
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card h-100 group-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people text-success me-2"></i>
                            Mes groupes
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentGroups->count() > 0)
                            @foreach($recentGroups as $group)
                                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                    <div>
                                        <h6 class="mb-0">{{ $group->name }}</h6>
                                        <small class="text-muted">{{ $group->users->count() }} membres</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 small">Aucun groupe pour le moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>