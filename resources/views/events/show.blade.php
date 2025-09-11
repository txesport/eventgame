<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-event text-primary"></i>
                        {{ $event->name }}
                    </h4>
                    @if($event->creator_id === auth()->id())
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Informations de base -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Groupe :</strong>
                                {{ $event->group->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Créé par :</strong>
                                <img src="{{ $event->creator->avatar_url }}" class="rounded-circle me-2" width="24" height="24">
                                {{ $event->creator->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Statut :</strong>
                                <span class="badge bg-{{ $event->status === 'planned' ? 'success' : 'warning' }}">
                                    {{ $event->status === 'planned' ? 'Planifié' : 'En préparation' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Contenu principal en deux colonnes -->
                    <div class="row">
                        <!-- Colonne gauche : Dates et Activités -->
                        <div class="col-lg-6 mb-4">
                            <!-- Dates proposées -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-calendar3 text-primary"></i>
                                        Dates proposées
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($event->dates as $date)
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <span>{{ $date->date_time->format('d/m/Y à H:i') }}</span>
                                            <div>
                                                <span class="badge bg-light text-dark">
                                                    {{ $date->votes->where('vote', true)->count() }} vote(s)
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Activités proposées -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-list-task text-success"></i>
                                        Activités proposées
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($event->activities as $activity)
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <div>
                                                <strong>{{ $activity->name }}</strong>
                                                @if($activity->category)
                                                    <span class="badge bg-secondary ms-2">{{ $activity->category }}</span>
                                                @endif
                                                @if($activity->description)
                                                    <br><small class="text-muted">{{ $activity->description }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-dark">
                                                {{ $activity->votes->where('vote_type', 'yes')->count() }} vote(s)
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite : Dépenses -->
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-receipt text-warning"></i>
                                        Dépenses
                                    </h5>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                        <i class="bi bi-plus"></i> Ajouter une dépense
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($event->expenses->count() > 0)
                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <strong>Total des dépenses : {{ number_format($event->total_expenses, 2, ',', ' ') }} €</strong>
                                            </div>
                                        </div>

                                        <!-- Liste des dépenses -->
                                        @foreach($event->expenses as $expense)
                                            <div class="border p-3 mb-2 rounded">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $expense->description }}</h6>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-person-check me-1"></i>
                                                            Payé par <strong>{{ $expense->payer->name }}</strong>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-people me-1"></i>
                                                            Partagé par {{ count($expense->participants) }} personne(s)
                                                        </div>
                                                        <div class="text-success small">
                                                            <i class="bi bi-calculator me-1"></i>
                                                            {{ number_format($expense->amount_per_person, 2, ',', ' ') }} € par personne
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="h5 mb-1 text-primary">{{ number_format($expense->amount, 2, ',', ' ') }} €</div>
                                                        @if($expense->paid_by === Auth::id())
                                                            <div class="btn-group-vertical">
                                                                <button class="btn btn-outline-secondary btn-sm" onclick="editExpense({{ $expense->id }})">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette dépense ?')">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Résumé des comptes -->
                                        <div class="mt-4">
                                            <h6 class="border-bottom pb-2">Résumé des comptes</h6>
                                            @foreach($event->getExpenseSummary() as $summary)
                                                <div class="d-flex justify-content-between py-2 {{ $summary['balance'] > 0 ? 'text-success' : ($summary['balance'] < 0 ? 'text-danger' : 'text-muted') }}">
                                                    <span>
                                                        <img src="{{ $summary['user']->avatar_url }}" class="rounded-circle me-2" width="24" height="24">
                                                        {{ $summary['user']->name }}
                                                    </span>
                                                    <span class="fw-bold">
                                                        @if($summary['balance'] > 0)
                                                            +{{ number_format($summary['balance'], 2, ',', ' ') }} €
                                                        @elseif($summary['balance'] < 0)
                                                            {{ number_format($summary['balance'], 2, ',', ' ') }} €
                                                        @else
                                                            0,00 €
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">Aucune dépense pour cet événement.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Photos -->
                        <!-- Section Photos -->
    <!-- Section Photos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-camera text-info"></i>
                    Souvenirs ({{ $event->photos->count() }})
                </h5>
            </div>
            <div class="card-body">
                @php
                    $selectedDate = $event->dates->firstWhere('is_selected', true)?->date_time;
                    $canUpload = $event->status === 'planned' && (! $selectedDate || now()->gte($selectedDate));
                @endphp

                @if(! $canUpload)
                    <div class="alert alert-warning">
                        📅 Impossible d'ajouter des photos tant que l'événement n'est pas planifié et commencé.
                    </div>
                @else
                    @if(auth()->user()->groups->contains($event->group))
                        <form action="{{ route('events.photos.store', $event) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="mb-4"
                              data-validate="true">
                            @csrf
                            <div class="mb-3">
                                <input type="file"
                                       name="photos[]"
                                       class="form-control"
                                       multiple
                                       accept="image/jpeg,image/png,image/gif"
                                       data-error-message="Format invalide (JPG, PNG, GIF)."
                                       required>
                                @error('photos')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Sélectionnez jusqu'à 10 photos (max 5 MB chacune).
                            </small>
                            <button type="submit" class="btn btn-primary mt-2">
                                <i class="bi bi-upload me-1"></i> Uploader
                            </button>
                        </form>
                    @endif
                @endif

                @if($event->photos->isNotEmpty())
                    <div class="row g-3">
                        @foreach($event->photos as $photo)
                            <div class="col-md-4">
                                <div class="card">
                                    <!-- Lien pour ouvrir la modale -->
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#photoModal{{ $photo->id }}">
                                        <img src="{{ asset('storage/' . $photo->path) }}"
                                             alt="Photo {{ $loop->iteration }}"
                                             class="card-img-top"
                                             style="height:200px;object-fit:cover;"
                                             loading="lazy">
                                    </a>
                                    <div class="card-body p-2">
                                        @if($photo->user_id === auth()->id() || $event->creator_id === auth()->id())
                                            <form action="{{ route('event-photos.destroy', $photo) }}"
                                                  method="POST"
                                                  class="float-end">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Supprimer cette photo ?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($photo->caption)
                                            <p class="small mb-1">{{ $photo->caption }}</p>
                                        @endif

                                        <div class="d-flex align-items-center">
                                            <img src="{{ $photo->user->avatar_url }}"
                                                 class="rounded-circle me-2"
                                                 width="20" height="20"
                                                 alt="{{ $photo->user->name }}">
                                            <small class="text-muted">
                                                Par {{ $photo->user->name }}
                                                le {{ $photo->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modale Bootstrap pour agrandir la photo -->
                            <div class="modal fade" id="photoModal{{ $photo->id }}" tabindex="-1" aria-labelledby="photoModalLabel{{ $photo->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content bg-transparent border-0">
                                        <div class="modal-body p-0">
                                            <img src="{{ asset('storage/' . $photo->path) }}"
                                                 alt="Photo agrandie {{ $loop->iteration }}"
                                                 class="img-fluid w-100 rounded">
                                        </div>
                                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-camera text-muted" style="font-size:3rem;"></i>
                        <p class="text-muted mt-2">Aucune photo pour cet événement.</p>
                        @if(auth()->user()->groups->contains($event->group) && $canUpload)
                            <p class="text-muted">Soyez le premier à partager vos souvenirs !</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


        </div>
    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une dépense -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('expenses.store', $event) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une dépense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <input type="text" class="form-control" id="description" name="description" 
                                   placeholder="Ex: Restaurant, Cinéma, Transport..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant (€) *</label>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0.01" placeholder="0,00" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Participants qui partagent cette dépense *</label>
                            <div class="row">
                                @foreach($event->group->users as $user)
                                    <div class="col-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="participants[]" value="{{ $user->id }}" 
                                                   id="participant_{{ $user->id }}" 
                                                   {{ $user->id === Auth::id() ? 'checked' : '' }}>
                                            <label class="form-check-label" for="participant_{{ $user->id }}">
                                                <img src="{{ $user->avatar_url }}" class="rounded-circle me-2" width="20" height="20">
                                                {{ $user->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter la dépense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editExpense(expenseId) {
            alert('Fonctionnalité d\'édition à implémenter si nécessaire');
        }
    </script>
</x-app-layout>