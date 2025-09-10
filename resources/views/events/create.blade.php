<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card hover-lift fade-in-up">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-plus text-primary"></i>
                        Créer un nouvel événement
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Alpine.js Component Wrapper -->
                    <div x-data="eventForm" x-init="console.log('EventForm initialized:', $data)">
                        <form action="{{ route('events.store') }}" method="POST">
                            @csrf

                            <!-- Informations de base -->
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <label for="name" class="form-label">Nom de l'événement *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}"
                                           placeholder="Ex: Soirée cinéma" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-lg-6 mb-4">
                                    <label for="group_id" class="form-label">Groupe *</label>
                                    <select class="form-select @error('group_id') is-invalid @enderror"
                                            id="group_id" name="group_id" required>
                                        <option value="">Sélectionnez un groupe</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}"
                                                    {{ old('group_id', request('group')) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }} ({{ $group->users->count() }} membres)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('group_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Section Dates -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0 fw-bold">Dates proposées *</label>
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm" 
                                            @click="addDate()"
                                            x-text="'Ajouter une date (' + dates.length + ')'">
                                        <i class="bi bi-plus"></i> Ajouter une date
                                    </button>
                                </div>

                                <div class="dates-container">
                                    <template x-for="(date, index) in dates" :key="index">
                                        <div class="row align-items-center mb-3">
                                            <div class="col-10">
                                                <input type="datetime-local"
                                                       class="form-control"
                                                       :name="'dates[' + index + ']'"
                                                       x-model="dates[index]"
                                                       required>
                                            </div>
                                            <div class="col-2">
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm"
                                                        @click="removeDate(index)"
                                                        x-show="dates.length > 1"
                                                        :disabled="dates.length <= 1">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                @error('dates')<div class="text-danger small">{{ $message }}</div>@enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i>
                                    Proposez plusieurs créneaux. Les membres du groupe voteront pour leurs préférés.
                                </div>
                            </div>

                            <!-- Section Activités -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0 fw-bold">Activités proposées *</label>
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm" 
                                            @click="addActivity()"
                                            x-text="'Ajouter une activité (' + activities.length + ')'">
                                        <i class="bi bi-plus"></i> Ajouter une activité
                                    </button>
                                </div>

                                <div class="activities-container">
                                    <template x-for="(activity, index) in activities" :key="index">
                                        <div class="card mb-3 border-light">
                                            <div class="card-body py-3">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-4">
                                                        <input type="text"
                                                               class="form-control"
                                                               :name="'activities[' + index + '][name]'"
                                                               x-model="activities[index].name"
                                                               placeholder="Nom de l'activité *"
                                                               required>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <select class="form-select"
                                                                :name="'activities[' + index + '][category]'"
                                                                x-model="activities[index].category">
                                                            <option value="">Catégorie (optionnelle)</option>
                                                            <option value="Restaurant">🍽️ Restaurant</option>
                                                            <option value="Cinéma">🎬 Cinéma</option>
                                                            <option value="Sport">⚽ Sport</option>
                                                            <option value="Culture">🎨 Culture</option>
                                                            <option value="Sortie">🎉 Sortie</option>
                                                            <option value="Autre">🔹 Autre</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <input type="text"
                                                               class="form-control"
                                                               :name="'activities[' + index + '][description]'"
                                                               x-model="activities[index].description"
                                                               placeholder="Description courte">
                                                    </div>
                                                    <div class="col-lg-1 text-center">
                                                        <button type="button"
                                                                class="btn btn-outline-danger btn-sm"
                                                                @click="removeActivity(index)"
                                                                x-show="activities.length > 1"
                                                                :disabled="activities.length <= 1">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                @error('activities')<div class="text-danger small">{{ $message }}</div>@enderror
                                <div class="form-text">
                                    <i class="bi bi-lightbulb"></i>
                                    Proposez différentes activités. Les membres voteront pour celles qu'ils préfèrent.
                                </div>
                            </div>

                            <!-- Information d'aide -->
                            <div class="alert alert-info border-0">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-info-circle fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="alert-heading mb-2">Comment ça marche :</h6>
                                        <ul class="mb-0 small">
                                            <li>Tous les membres du groupe seront automatiquement invités à cet événement</li>
                                            <li>Ils pourront voter pour leurs dates et activités préférées</li>
                                            <li>Vous obtiendrez les résultats en temps réel pour prendre vos décisions</li>
                                            <li>Une fois organisé, vous pourrez gérer les dépenses partagées</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between pt-3">
                                <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour aux événements
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-plus"></i> Créer l'événement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
