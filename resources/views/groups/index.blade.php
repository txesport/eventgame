{{-- File: resources/views/groups/index.blade.php --}}
<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0">
                    <i class="bi bi-people text-primary"></i>
                    Mes Groupes
                </h1>
                <a href="{{ route('groups.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nouveau Groupe
                </a>
            </div>
        </div>
    </div>

    @if($groups->count() > 0)
        <div class="row">
            @foreach($groups as $group)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $group->name }}</h5>
                            @if($group->created_by === Auth::id())
                                <span class="badge bg-primary">Créateur</span>
                            @else
                                <span class="badge bg-secondary">Membre</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($group->description)
                                <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                            @endif
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h6 class="text-primary mb-0">{{ $group->users->count() }}</h6>
                                        <small class="text-muted">Membre(s)</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-success mb-0">{{ $group->events->count() }}</h6>
                                    <small class="text-muted">Événement(s)</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Code d'invitation:</small>
                                <div class="d-flex align-items-center">
                                    <code class="me-2">{{ $group->invitation_code }}</code>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{ $group->invitation_code }}')">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-primary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                
                                <div class="btn-group">
                                    @if($group->created_by === Auth::id())
                                        <a href="{{ route('groups.edit', $group) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete('{{ $group->name }}', '{{ route('groups.destroy', $group) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('groups.leave', $group) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir quitter ce groupe ?')">
                                                <i class="bi bi-box-arrow-left"></i> Quitter
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-people-fill text-muted" style="font-size: 4rem;"></i>
            <h3 class="text-muted mt-3">Aucun groupe pour l'instant</h3>
            <p class="text-muted">Créez votre premier groupe ou rejoignez-en un avec un code d'invitation.</p>
            <div class="mt-4">
                <a href="{{ route('groups.create') }}" class="btn btn-primary me-3">
                    <i class="bi bi-plus-circle"></i> Créer un groupe
                </a>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#joinGroupModal">
                    <i class="bi bi-box-arrow-in-right"></i> Rejoindre un groupe
                </button>
            </div>
        </div>
    @endif

    <!-- Modal pour rejoindre un groupe -->
    <div class="modal fade" id="joinGroupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejoindre un groupe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('groups.join') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="invitation_code" class="form-label">Code d'invitation</label>
                            <input type="text" class="form-control" id="invitation_code" 
                                   name="invitation_code" placeholder="Entrez le code d'invitation" required>
                            <div class="form-text">
                                Demandez le code d'invitation au créateur du groupe.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Rejoindre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Feedback visuel
                const button = event.target.closest('button');
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 1000);
            });
        }

        function confirmDelete(groupName, deleteUrl) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le groupe "${groupName}" ? Cette action est irréversible.`)) {
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
