<x-app-layout>
    <div class="row">
        <div class="col-lg-8">
            <!-- Informations de l'événement -->
            <div class="card hover-lift fade-in-up mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-event text-primary"></i>
                        {{ $event->name }}
                    </h4>
                    @if($event->creator_id === auth()->id())
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Groupe :</strong></div>
                        <div class="col-sm-9">{{ $event->group->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Créé par :</strong></div>
                        <div class="col-sm-9">
                            <img src="{{ $event->creator->avatar_url }}" class="rounded-circle me-2" width="24" height="24">
                            {{ $event->creator->name }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Statut :</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ $event->status === 'planned' ? 'success' : 'warning' }}">
                                {{ $event->status === 'planned' ? 'Planifié' : 'En préparation' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Photos -->
            <div class="card hover-lift fade-in-up">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-images text-success"></i>
                        Souvenirs ({{ $event->photos->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Formulaire d'upload -->
                    @if(auth()->user()->groups->contains($event->group))
                        <form action="{{ route('events.photos.store', $event) }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              class="mb-4"
                              x-data="photoUpload()">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <input type="file" 
                                           name="photos[]" 
                                           class="form-control @error('photos') is-invalid @enderror" 
                                           multiple 
                                           accept="image/*"
                                           x-on:change="handleFiles($event)"
                                           max="10">
                                    @error('photos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Sélectionnez jusqu'à 10 photos (JPG, PNG, GIF). Max 5MB par photo.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" 
                                            class="btn btn-success w-100"
                                            :disabled="files.length === 0">
                                        <i class="bi bi-cloud-upload"></i>
                                        <span x-text="files.length > 0 ? `Ajouter (${files.length})` : 'Ajouter des photos'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <!-- Galerie de photos -->
                    @if($event->photos->isNotEmpty())
                        <div class="row g-3">
                            @foreach($event->photos as $photo)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="position-relative">
                                        <a href="{{ $photo->url }}" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#photoModal"
                                           data-photo-url="{{ $photo->url }}"
                                           data-photo-caption="{{ $photo->caption }}"
                                           data-photo-user="{{ $photo->user->name }}"
                                           data-photo-date="{{ $photo->created_at->format('d/m/Y H:i') }}">
                                            <img src="{{ $photo->thumbnail_url }}" 
                                                 class="img-fluid rounded shadow-sm hover-grow" 
                                                 style="aspect-ratio: 1; object-fit: cover;">
                                        </a>
                                        
                                        @if($photo->user_id === auth()->id() || $event->creator_id === auth()->id())
                                            <form action="{{ route('event-photos.destroy', $photo) }}" 
                                                  method="POST" 
                                                  class="position-absolute top-0 end-0 m-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm rounded-circle"
                                                        style="width: 30px; height: 30px; padding: 0;"
                                                        onclick="return confirm('Supprimer cette photo ?')">
                                                    <i class="bi bi-trash" style="font-size: 12px;"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($photo->caption)
                                            <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-2 rounded-bottom">
                                                <small>{{ $photo->caption }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            Par {{ $photo->user->name }}
                                            <br>{{ $photo->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-camera display-1 text-muted"></i>
                            <p class="text-muted mt-3">Aucune photo pour cet événement.</p>
                            @if(auth()->user()->groups->contains($event->group))
                                <p class="text-muted">Soyez le premier à partager vos souvenirs !</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Dates et activités (contenu existant) -->
            <!-- ... -->
        </div>
    </div>

    <!-- Modal pour affichage des photos -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalTitle">Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalPhoto" src="" class="img-fluid w-100">
                </div>
                <div class="modal-footer">
                    <div class="text-start w-100">
                        <p id="modalCaption" class="mb-1"></p>
                        <small class="text-muted">
                            Par <span id="modalUser"></span> le <span id="modalDate"></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Alpine.js component pour l'upload
        function photoUpload() {
            return {
                files: [],
                handleFiles(event) {
                    this.files = Array.from(event.target.files);
                }
            }
        }

        // Modal pour les photos
        document.addEventListener('DOMContentLoaded', function() {
            const photoModal = document.getElementById('photoModal');
            
            photoModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                const photoUrl = trigger.getAttribute('data-photo-url');
                const caption = trigger.getAttribute('data-photo-caption');
                const user = trigger.getAttribute('data-photo-user');
                const date = trigger.getAttribute('data-photo-date');

                document.getElementById('modalPhoto').src = photoUrl;
                document.getElementById('modalCaption').textContent = caption || '';
                document.getElementById('modalUser').textContent = user;
                document.getElementById('modalDate').textContent = date;
            });
        });
    </script>
</x-app-layout>
