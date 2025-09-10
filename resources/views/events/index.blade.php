<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">
                    <i class="bi bi-calendar3 text-primary"></i>
                    Mes Événements
                </h1>
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Nouvel Événement
                </a>
            </div>
        </div>
    </div>

    @if($events->count() > 0)
        <div class="row">
            @foreach($events as $event)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">{{ Str::limit($event->name, 25) }}</h6>
                            @if($event->created_by === Auth::id())
                                <span class="badge bg-primary">Créateur</span>
                            @else
                                <span class="badge bg-secondary">Invité</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($event->description)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($event->description, 80) }}
                                </p>
                            @endif

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people text-primary me-2"></i>
                                    <strong>{{ $event->group->name }}</strong>
                                </div>
                                
                                @if($event->location)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt text-info me-2"></i>
                                        <small class="text-muted">{{ $event->location }}</small>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person text-secondary me-2"></i>
                                    <small class="text-muted">Créé par {{ $event->creator->name }}</small>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock text-warning me-2"></i>
                                    <small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <!-- Statistiques -->
                            <div class="row text-center mb-3">
                                <div class="col-3">
                                    <div class="border-end">
                                        <h6 class="text-primary mb-0">{{ $event->dates->count() }}</h6>
                                        <small class="text-muted">Date(s)</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-end">
                                        <h6 class="text-success mb-0">{{ $event->activities->count() }}</h6>
                                        <small class="text-muted">Activité(s)</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-end">
                                        <h6 class="text-warning mb-0">{{ $event->expenses->count() }}</h6>
                                        <small class="text-muted">Dépense(s)</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <h6 class="text-info mb-0">{{ $event->group->users->count() }}</h6>
                                    <small class="text-muted">Invité(s)</small>
                                </div>
                            </div>
                            
                            <!-- Aperçu des votes -->
                            @php 
                                $totalDateVotes = $event->dates->sum(function($date) {
                                    return $date->votes()->count();
                                });
                                $totalActivityVotes = $event->activities->sum(function($activity) {
                                    return $activity->votes()->count();
                                });
                            @endphp
                            
                            @if($totalDateVotes > 0 || $totalActivityVotes > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Activité de vote :</small>
                                    <div class="d-flex gap-3">
                                        @if($totalDateVotes > 0)
                                            <div class="badge bg-light text-dark">
                                                <i class="bi bi-calendar-check"></i>
                                                {{ $totalDateVotes }} vote(s) dates
                                            </div>
                                        @endif
                                        @if($totalActivityVotes > 0)
                                            <div class="badge bg-light text-dark">
                                                <i class="bi bi-list-check"></i>
                                                {{ $totalActivityVotes }} vote(s) activités
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Total des dépenses -->
                            @if($event->total_expenses > 0)
                                <div class="mb-3">
                                    <div class="badge bg-warning text-dark">
                                        <i class="bi bi-receipt"></i>
                                        Total : {{ number_format($event->total_expenses, 2, ',', ' ') }} €
                                    </div>
                                </div>
                            @endif
                            
                            @if($event->dates->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Prochaines dates proposées :</small>
                                    @foreach($event->dates->take(2) as $date)
                                        <div class="badge bg-light text-dark me-1 mb-1">
                                            <i class="bi bi-calendar-event"></i>
                                            {{ $date->proposed_date->format('d/m/Y H:i') }}
                                            @if($date->total_votes > 0)
                                                <span class="text-success">({{ $date->yes_votes }}/{{ $date->total_votes }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($event->dates->count() > 2)
                                        <div class="badge bg-light text-dark">
                                            +{{ $event->dates->count() - 2 }} autre(s)
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-primary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                
                                @if($event->created_by === Auth::id())
                                    <div class="btn-group">
                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $event->name }}', '{{ route('events.destroy', $event) }}')">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
            <h3 class="text-muted mt-3">Aucun événement pour l'instant</h3>
            <p class="text-muted">Créez votre premier événement pour commencer à organiser vos sorties !</p>
            <div class="mt-4">
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="bi btn-calendar-plus"></i> Créer un événement
                </a>
            </div>
        </div>
    @endif

    <script>
        function confirmDelete(eventName, deleteUrl) {
            if (confirm(`Êtes-vous sûr de vouloir désactiver l'événement "${eventName}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
