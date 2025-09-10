<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="bi bi-file-text text-primary"></i>
                        Résumé : {{ $event->name }}
                        @if($event->isFinalized())
                            <span class="badge bg-success ms-2">Finalisé</span>
                        @else
                            <span class="badge bg-warning ms-2">En préparation</span>
                        @endif
                    </h1>
                    @if($event->description)
                        <p class="text-muted">{{ $event->description }}</p>
                    @endif
                </div>
                <div class="btn-group">
                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à l'événement
                    </a>
                    @if($event->summary && $event->isFinalized())
                        <a href="{{ route('events.export', $event) }}" class="btn btn-info">
                            <i class="bi bi-download"></i> Exporter
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Résumé des votes -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary"></i>
                        Résultats des votes
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Date la plus votée -->
                    @if($mostVotedDate)
                        <div class="mb-4">
                            <h6 class="text-success">
                                <i class="bi bi-trophy"></i> Date la plus populaire
                            </h6>
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $mostVotedDate->proposed_date->format('d/m/Y à H:i') }}</strong>
                                        <div class="small text-muted">
                                            {{ $mostVotedDate->proposed_date->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">{{ $mostVotedDate->yes_votes }} votes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Activités les plus votées -->
                    @if($mostVotedActivities->isNotEmpty())
                        <div class="mb-4">
                            <h6 class="text-success">
                                <i class="bi bi-star"></i> Activités populaires
                            </h6>
                            @foreach($mostVotedActivities as $activity)
                                <div class="border rounded p-3 mb-2 bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $activity->activity_name }}</strong>
                                            @if($activity->category)
                                                <span class="badge bg-secondary ms-2">{{ $activity->category }}</span>
                                            @endif
                                            @if($activity->description)
                                                <div class="small text-muted">{{ $activity->description }}</div>
                                            @endif
                                        </div>
                                        <span class="badge bg-success">{{ $activity->yes_votes }} votes</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!$mostVotedDate && $mostVotedActivities->isEmpty())
                        <div class="text-center py-3">
                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Aucun vote enregistré pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator text-warning"></i>
                        Résumé financier
                    </h5>
                </div>
                <div class="card-body">
                    @if($event->expenses->count() > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <strong>Total des dépenses :</strong>
                                <span class="h5 text-primary mb-0">{{ number_format($event->total_expenses, 2, ',', ' ') }} €</span>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-info mb-3">
                            <i class="bi bi-people"></i> Soldes par participant
                        </h6>
                        
                        @foreach($expenseSummary as $userId => $summary)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $summary['user']->name }}</strong>
                                    <small class="text-muted d-block">{{ ucfirst($summary['status']) }}</small>
                                </div>
                                <div>
                                    @if($summary['balance'] > 0)
                                        <span class="text-success fw-bold">
                                            +{{ number_format($summary['balance'], 2, ',', ' ') }} €
                                        </span>
                                        <div class="small text-muted">à recevoir</div>
                                    @elseif($summary['balance'] < 0)
                                        <span class="text-danger fw-bold">
                                            {{ number_format($summary['balance'], 2, ',', ' ') }} €
                                        </span>
                                        <div class="small text-muted">à payer</div>
                                    @else
                                        <span class="text-muted">équilibré</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-wallet2 text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">Aucune dépense enregistrée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Finalisation de l'événement -->
    @if($event->created_by === Auth::id())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-square text-success"></i>
                            Finalisation de l'événement
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($event->isFinalized())
                            <!-- Événement finalisé -->
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i>
                                <strong>Événement finalisé !</strong>
                                @if($event->summary->finalDate)
                                    Date confirmée : <strong>{{ $event->summary->finalDate->proposed_date->format('d/m/Y à H:i') }}</strong>
                                @endif
                            </div>

                            @if($event->summary->final_activities_models->isNotEmpty())
                                <div class="mb-3">
                                    <strong>Activités retenues :</strong>
                                    <ul class="list-unstyled mt-2">
                                        @foreach($event->summary->final_activities_models as $activity)
                                            <li>
                                                <i class="bi bi-check text-success"></i>
                                                {{ $activity->activity_name }}
                                                @if($activity->category)
                                                    <span class="badge bg-secondary ms-1">{{ $activity->category }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($event->summary->summary_notes)
                                <div class="mb-3">
                                    <strong>Notes :</strong>
                                    <p class="mt-1">{{ $event->summary->summary_notes }}</p>
                                </div>
                            @endif

                            <div class="d-flex gap-2">
                                @if($event->summary->finalDate && !$event->summary->reminder_sent_at)
                                    <form action="{{ route('events.reminders', $event) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="bi bi-bell"></i> Envoyer des rappels
                                        </button>
                                    </form>
                                @endif

                                @if($event->summary->reminder_sent_at)
                                    <span class="text-success">
                                        <i class="bi bi-check-circle"></i>
                                        Rappels envoyés le {{ $event->summary->reminder_sent_at->format('d/m/Y à H:i') }}
                                    </span>
                                @endif

                                <form action="{{ route('events.unfinalize', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary" 
                                            onclick="return confirm('Annuler la finalisation ?')">
                                        <i class="bi bi-arrow-clockwise"></i> Modifier
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- Formulaire de finalisation -->
                            <form action="{{ route('events.finalize', $event) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="final_date_id" class="form-label">Date définitive</label>
                                        <select class="form-select" id="final_date_id" name="final_date_id">
                                            <option value="">Sélectionnez une date</option>
                                            @foreach($event->dates as $date)
                                                <option value="{{ $date->id }}" 
                                                        {{ $date->id === $mostVotedDate?->id ? 'selected' : '' }}>
                                                    {{ $date->proposed_date->format('d/m/Y à H:i') }}
                                                    ({{ $date->yes_votes }} votes)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            La date la plus votée est présélectionnée
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Activités retenues</label>
                                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($event->activities as $activity)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="final_activities[]" value="{{ $activity->id }}" 
                                                           id="activity_{{ $activity->id }}"
                                                           {{ in_array($activity->id, $mostVotedActivities->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="activity_{{ $activity->id }}">
                                                        {{ $activity->activity_name }}
                                                        <small class="text-muted">({{ $activity->yes_votes }} votes)</small>
                                                        @if($activity->category)
                                                            <span class="badge bg-secondary ms-1">{{ $activity->category }}</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">
                                            Les activités les plus votées sont présélectionnées
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="summary_notes" class="form-label">Notes de synthèse (optionnel)</label>
                                    <textarea class="form-control" id="summary_notes" name="summary_notes" rows="3" 
                                              placeholder="Ajoutez des informations complémentaires, instructions spéciales, etc."></textarea>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Finaliser l'événement permettra :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>De fixer définitivement la date et les activités</li>
                                        <li>D'envoyer des rappels aux participants</li>
                                        <li>D'exporter un résumé complet</li>
                                        <li>De clôturer les votes</li>
                                    </ul>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg"></i> Finaliser l'événement
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Aperçu du résumé -->
    @if($event->summary && $event->isFinalized())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark-text text-info"></i>
                            Aperçu du résumé exportable
                        </h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded" style="white-space: pre-wrap; font-size: 0.9em;">{{ $event->summary->generateSummaryText() }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
