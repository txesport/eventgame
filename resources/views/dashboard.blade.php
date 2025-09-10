<x-app-layout>
    <div class="row">
        <div class="col-12">
            <h2 class="text-gradient mb-4">
                <i class="bi bi-speedometer2"></i> 
                Bonjour {{ $user->name }}, bienvenue sur votre tableau de bord
            </h2>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-primary hover-lift">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-primary mb-3"></i>
                    <h3 class="stat-number text-primary">{{ $stats['groups'] }}</h3>
                    <p class="text-muted mb-0">Groupes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success hover-lift">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event display-4 text-success mb-3"></i>
                    <h3 class="stat-number text-success">{{ $stats['events'] }}</h3>
                    <p class="text-muted mb-0">Événements créés</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info hover-lift">
                <div class="card-body text-center">
                    <i class="bi bi-images display-4 text-info mb-3"></i>
                    <h3 class="stat-number text-info">{{ $stats['photos'] }}</h3>
                    <p class="text-muted mb-0">Photos partagées</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements récents -->
        <div class="col-lg-8">
            <div class="card hover-lift fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 text-primary"></i> Mes événements récents
                    </h5>
                    <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Nouveau
                    </a>
                </div>
                <div class="card-body">
                    @if($recentEvents->count() > 0)
                        @foreach($recentEvents as $event)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">{{ $event->name }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-people me-1"></i>{{ $event->group->name }}
                                        <i class="bi bi-clock ms-2 me-1"></i>{{ $event->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">
                                    Voir
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
                            <i class="bi bi-calendar-x display-6 text-muted"></i>
                            <p class="text-muted mt-3">Vous n'avez pas encore créé d'événement.</p>
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                Créer mon premier événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Groupes -->
        <div class="col-lg-4">
            <div class="card hover-lift fade-in-up">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people text-success"></i> Mes groupes
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentGroups->count() > 0)
                        @foreach($recentGroups as $group)
                            <div class="py-2 border-bottom">
                                <h6 class="mb-1">{{ $group->name }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>{{ $group->users->count() }} membres
                                </small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people display-6 text-muted"></i>
                            <p class="text-muted mt-3">Aucun groupe pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
