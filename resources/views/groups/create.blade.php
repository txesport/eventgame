{{-- File: resources/views/groups/create.blade.php --}}
<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-people text-primary"></i>
                        Créer un nouveau groupe
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('groups.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label">Nom du groupe *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Exemple: Amis de la fac" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Choisissez un nom descriptif pour votre groupe d'amis.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description (optionnelle)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Décrivez brièvement votre groupe...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Une description courte pour aider les membres à comprendre l'objectif du groupe.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>À savoir :</strong>
                            <ul class="mb-0 mt-2">
                                <li>Vous serez automatiquement ajouté comme membre du groupe</li>
                                <li>Un code d'invitation unique sera généré pour inviter d'autres membres</li>
                                <li>En tant que créateur, vous pourrez gérer les membres et supprimer le groupe</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('groups.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Créer le groupe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
