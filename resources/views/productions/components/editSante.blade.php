<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                Informations générales de santé
            </h5>
        </div>
        <div class="card-body">

            <form action="{{ route('prod.update.sante', $contrat->id) }}" method="post" class="submitForm">
                @csrf
                <div class="row g-3">
                    {{-- Taille --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <label class="form-label mb-0">Quelle est votre taille ?</label>
                                <div class="input-group w-50">
                                    <input type="number" class="form-control" name="taille" value="{{ old('taille', $contrat->santes->taille ?? '') }}" placeholder="170">
                                    <span class="input-group-text">CM</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Poids --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <label class="form-label mb-0">Quel est votre poids ?</label>
                                <div class="input-group w-50">
                                    <input type="number" class="form-control" name="poids" value="{{ old('poids', $contrat->santes->poids ?? '') }}" placeholder="70">
                                    <span class="input-group-text">KG</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fumeur --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <label class="form-label">Fumez-vous ?</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="smoking" value="Oui" {{ old('smoking', $contrat->santes->smoking ?? '') == 'Oui' ? 'checked' : '' }}>
                                        <label class="form-check-label">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="smoking" value="Non" {{ old('smoking', $contrat->santes->smoking ?? '') == 'Non' ? 'checked' : '' }}>
                                        <label class="form-check-label">Non</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alcool --}}
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <label class="form-label">Buvez-vous de l’alcool ?</label>
                                <div>
                                    @foreach (['Non' => 'Pas du tout', 'Partiel' => "À l'occasion", 'Oui' => 'Régulièrement'] as $val => $txt)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="alcohol" value="{{ $val }}" {{ old('alcohol', $contrat->santes->alcohol ?? '') == $val ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $txt }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Exemple pour le reste (accident, traitement, maladies, etc.) --}}
                    @php
                        $questions = [
                            // 'infirmete' => "Êtes-vous atteint d'une infirmité ?",
                            'ArretTravail' => "Êtes-vous en arrêt de travail ?",
                            'accident' => "Avez-vous déjà été victime d'un accident ?",
                            'treatment' => "Traitement médical ces 6 derniers mois ?",
                            'transSang' => "Avez-vous déjà subi une transfusion de sang ?",
                            'interChirugiale' => "Avez-vous déjà subi des interventions chirurgicales ?",
                            'prochaineInterChirugiale' => "Devez-vous subir une intervention chirurgicale ?",
                            'diabetes' => "Diabète",
                            'hypertension' => "Hypertension artérielle",
                            'sickleCell' => "Drépanocytose",
                            'liverCirrhosis' => "Cirrhose du foie",
                            'lungDisease' => "Affections pulmonaires",
                            'cancer' => "Cancer",
                            'anemia' => "Anémie",
                            'kidneyFailure' => "Insuffisance rénale",
                            'stroke' => "Avez-vous déjà été victime d’un AVC ?",
                        ];
                    @endphp

                    @foreach ($questions as $field => $label)
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <label class="form-label">{{ $label }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{ $field }}" value="Oui" {{ old($field, $contrat->santes->$field ?? '') == 'Oui' ? 'checked' : '' }}>
                                            <label class="form-check-label">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{ $field }}" value="Non" {{ old($field, $contrat->santes->$field ?? '') == 'Non' ? 'checked' : '' }}>
                                            <label class="form-check-label">Non</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Distractions --}}
                    <div class="col-lg-12">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <label class="form-label mb-0">Quelles sont vos distractions ?</label>
                                <input type="text" class="form-control w-50" name="distractions" placeholder="séparer par une virgule" value="{{ old('distractions', $contrat->santes->distractions ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>


                <button class="btn btn-primary my-4"  type="submit">Enregistrer</button>
                
            </form>
        </div>
    </div> 
</div>
