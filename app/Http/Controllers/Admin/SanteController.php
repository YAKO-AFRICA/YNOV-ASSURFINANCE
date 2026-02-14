<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\DeclarationSante;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            // Récupération de l'enregistrement
            $dossier = DeclarationSante::where('codeContrat', $id)->first();

            // Mise à jour des champs du formulaire
            $dossier->taille = $request->input('taille');
            $dossier->poids = $request->input('poids');
            $dossier->smoking = $request->input('smoking');
            $dossier->alcohol = $request->input('alcohol');
            // $dossier->infirmete = $request->input('infirmete');
            // $dossier->ArretTravail = $request->input('ArretTravail');
            $dossier->accident = $request->input('accident');
            // $dossier->distractions = $request->input('distractions');
            $dossier->treatment = $request->input('treatment');
            $dossier->transSang = $request->input('transSang');
            $dossier->interChirugiale = $request->input('interChirugiale');
            $dossier->prochaineInterChirugiale = $request->input('prochaineInterChirugiale');
            $dossier->diabetes = $request->input('diabetes');
            $dossier->hypertension = $request->input('hypertension');
            $dossier->sickleCell = $request->input('sickleCell');
            $dossier->liverCirrhosis = $request->input('liverCirrhosis');
            $dossier->lungDisease = $request->input('lungDisease');
            $dossier->cancer = $request->input('cancer');
            $dossier->anemia = $request->input('anemia');
            $dossier->kidneyFailure = $request->input('kidneyFailure');
            $dossier->stroke = $request->input('stroke');

            // Sauvegarde
            $dossier->save();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'urlback' => "back",
                'message' => "Enregistré avec succès!",
                'code' => 200,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'urlback' => '',
                'message' => "Erreur système! $th",
                'code' => 500,
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
