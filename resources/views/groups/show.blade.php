{{-- File: resources/views/groups/show.blade.php --}}
<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-people text-primary"></i>
                        {{ $group->name }}
                        @if($group->created_by === Auth::id())
                            <span class="badge bg-primary ms-2">Créateur</span>
                        @endif
                    </h1>
                    @if($group->description)
                        <p class="text-muted">{{ $group->description }}</p>
                    @endif
                </div>
                <div class="btn-group">
                    @if($group->created_by === Auth::id())
                        <a href="{{ route('groups.edit', $group) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    @endif
                    <a href="{{ route('events.create') }}?group={{ $group->id }}" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> Nouvel Événement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations du groupe -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-info"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Créé par :</strong>
                        <div class="text-muted">{{ $group->creator->name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Créé le :</strong>
                        <div class="text-muted">{{ $group->created_at->format('d/m/Y à H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <strong>Code d'invitation :</strong>
                        <div class="d-flex align-items-center mt-1">
                            <code class="me-2">{{ $group->invitation_code }}</code>
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{ $group->invitation_code }}')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            Partagez ce code pour inviter de nouveaux membres
                        </small>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary mb-0">{{ $group->users->count() }}</h4>
                            <small class="text-muted">Membre(s)</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">{{ $group->events->count() }}</h4>
                            <small class="text-muted">Événement(s)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($group->created_by !== Auth::id())
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <form action="{{ route('groups.leave', $group) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-warning" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir quitter ce groupe ?')">
                                <i class="bi bi-box-arrow-left"></i> Quitter le groupe
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Membres du groupe -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people text-primary"></i>
                        Membres ({{ $group->users->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($group->users as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                    @if($user->id === $group->created_by)
                                        <span class="badge bg-primary ms-2">Créateur</span>
                                    @endif
                                </div>
                                @if($group->created_by === Auth::id() && $user->id !== Auth::id())
                                    <form action="{{ route('groups.remove-member', [$group, $user]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Retirer {{ $user->name }} du groupe ?')"
                                                title="Retirer du groupe">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Événements du groupe -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 text-success"></i>
                        Événements
                    </h5>
                    <a href="{{ route('events.create') }}?group={{ $group->id }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($group->events->count() > 0)
                        @foreach($group->events as $event)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $event->name }}</h6>
                                        @if($event->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($event->description, 80) }}</p>
                                        @endif
                                        <div class="small text-muted">
                                            <i class="bi bi-person"></i> {{ $event->creator->name }}<br>
                                            <i class="bi bi-calendar"></i> {{ $event->created_at->diffForHumans() }}
                                            @if($event->dates->count() > 0)
                                                <br><i class="bi bi-clock"></i> {{ $event->dates->count() }} date(s) proposée(s)
                                            @endif
                                            @if($event->activities->count() > 0)
                                                <br><i class="bi bi-list-task"></i> {{ $event->activities->count() }} activité(s)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                    @if($event->created_by === Auth::id())
                                        <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-3">Aucun événement créé</p>
                            <a href="{{ route('events.create') }}?group={{ $group->id }}" class="btn btn-sm btn-success">
                                <i class="bi bi-calendar-plus"></i> Créer le premier événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                const button = event.target.closest('button');
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 1000);
            });
        }
    </script>
</x-app-layout>
