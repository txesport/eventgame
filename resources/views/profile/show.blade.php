<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card hover-lift fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-person-circle text-primary"></i>
                        Mon profil
                    </h4>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <img src="{{ $user->avatar_url }}" 
                                 class="rounded-circle border shadow-sm mb-3" 
                                 width="150" height="150" 
                                 style="object-fit: cover;">
                            <h5>{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                        
                        <div class="col-md-8">
                            <h6 class="text-uppercase text-muted mb-3">Statistiques</h6>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <i class="bi bi-people display-6 text-primary"></i>
                                            <h4 class="stat-number text-primary mt-2">{{ $user->groups->count() }}</h4>
                                            <small class="text-muted">Groupes</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <i class="bi bi-calendar-event display-6 text-success"></i>
                                            <h4 class="stat-number text-success mt-2">{{ $user->events->count() }}</h4>
                                            <small class="text-muted">Événements créés</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <i class="bi bi-images display-6 text-info"></i>
                                            <h4 class="stat-number text-info mt-2">{{ $user->eventPhotos->count() }}</h4>
                                            <small class="text-muted">Photos partagées</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <i class="bi bi-clock display-6 text-warning"></i>
                                            <h4 class="stat-number text-warning mt-2">{{ $user->created_at->diffForHumans() }}</h4>
                                            <small class="text-muted">Membre depuis</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Groupes de l'utilisateur -->
            @if($user->groups->isNotEmpty())
                <div class="card hover-lift fade-in-up mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people text-success"></i>
                            Mes groupes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($user->groups as $group)
                                <div class="col-md-6">
                                    <div class="card border-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $group->name }}</h6>
                                            <p class="card-text text-muted small">
                                                {{ $group->users->count() }} membres
                                            </p>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                Voir le groupe
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Événements récents -->
            @if($user->events->isNotEmpty())
                <div class="card hover-lift fade-in-up mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar3 text-info"></i>
                            Mes événements récents
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($user->events->take(5) as $event)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $event->name }}</h6>
                                        <small class="text-muted">{{ $event->group->name }} • {{ $event->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                        Voir
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
