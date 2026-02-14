<?php

namespace App\Http\Controllers\Admin;


use Dompdf\Dompdf;
use Dompdf\Options;

use App\Models\Contrat;

use BaconQrCode\Writer;
use setasign\Fpdi\Fpdi;
use App\Models\TblDocument;
use Illuminate\Http\Request;
use BaconQrCode\Encoder\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd; // Alternative SVG
use BaconQrCode\Renderer\Image\ImagickImageBackEnd; // Utilisez Imagick si disponible

class BulletinController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function demoBulletin(request $request)
     {
        try {

            $contrat = Contrat::where('id', 89)->first();

            // Chargement de la vue avec les données
            $pdf = Pdf::loadView('productions.components.bullettin.ykeBulletin', [
                'contrat' => $contrat
            ]);

            // Option 1 : Retourner directement le PDF pour téléchargement
            return $pdf->stream('bulletin_adhesion.pdf');

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function printBulletin()
    {
        // $prestation = TblPrestation::where('id', $id)->first();
        // Génération de QR Code en base64

        // $pdf = Pdf::loadView('productions.components.bullettin.ykeBulletin');
        // $pdf = Pdf::loadView('productions.components.bullettin.basicBulletin');
        // $pdf = Pdf::loadView('productions.components.bullettin.pfaINDbulletin');
        // $pdf = Pdf::loadView('productions.components.bullettin.Cadencebulletintest');
        // $pdf = Pdf::loadView('productions.components.bullettin.Doihoobulletintest');
        $pdf = Pdf::loadView('bulletin.DoihoobulletinBlanc');

        $fileName = 'doihoo.pdf';
        return $pdf->stream($fileName);
    }
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
        $contrat = Contrat::find($id);
        return view('productions.components.bullettin.basicBulletin', compact('contrat'));
    }


    public function generate(Request $request, $id)
    {
        try {

            $idContrat = $id;
            // Récupérer les données nécessaires au bulletin
            $contrat = Contrat::find($id);
            Log::info("Contrat". $contrat);


            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );



            log::info("Contrat". $idContrat);




            $imageUrl = env('SIGN_API') . "api/get-signature/" . $id . "/E-SOUSCRIPTION";
            
            $imageSrc = null;
            try {
                $response = Http::timeout(5)->get($imageUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    // Vérifie si 'error' existe et est à true
                    if (isset($data['error']) && $data['error'] === true) {
                        Log::info('Signature non trouvée pour le contrat ID: ' . $contrat->id);
                    } else {
                    
                        $imageData = $response->body(); 
                        $base64Image = base64_encode($imageData);
                        $imageSrc = 'data:image/png;base64,' . $base64Image;
                    }
                } else {
                    Log::error('Erreur HTTP lors de l\'appel de l\'API signature. Code de retour : ' , $response->json());
                }
            } catch (\Exception $e) {
                Log::error('Exception lors de la récupération de la signature : ' . $e->getMessage());
            }

            $qrContent = url("production/showQrCode/" . $contrat->id);
            
            $writer = new Writer($renderer);
            $qrCodeImage = $writer->writeString($qrContent);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodeImage);



            // Options pour DomPDF
            $options = new Options();
            $options->set('isRemoteEnabled', true);

            // Génération du bulletin PDF temporaire

            if($contrat->codeproduit == "YKE_2018"){
                $pdf = PDF::loadView('productions.components.bullettin.ykeBulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/cg_yke.pdf');

            }else if($contrat->codeproduit == "PFA_IND"){
                $pdf = PDF::loadView('productions.components.bullettin.pfaINDbulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/cg_yke.pdf');
                
            }else if($contrat->codeproduit == "CADENCE")
            {
                $pdf = PDF::loadView('productions.components.bullettin.Cadencebulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/cadenceCgu.pdf');
                
            }else if($contrat->codeproduit == "DOIHOO"){
                $pdf = PDF::loadView('productions.components.bullettin.Doihoobulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/doihoo_cgu.pdf');

            }else if($contrat->codeproduit == "CAD_EDUCPLUS"){
                $pdf = PDF::loadView('productions.components.bullettin.CadenceEduPlusbulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/CADENCEpLUS.pdf');
                
            }else{
                $pdf = PDF::loadView('productions.components.bullettin.basicBulletin', [
                    'contrat' => $contrat,
                    'qrCodeBase64' => $qrCodeBase64,
                    'imageSrc' => $imageSrc,
                ]);
                $cguFile = public_path('root/cgu/CGPLanggnant.pdf');
            }
            

            $bulletinDir = public_path('documents/bulletin/');
            if (!is_dir($bulletinDir)) {
                mkdir($bulletinDir, 0777, true);
            }

            $tempBulletinPath = $bulletinDir . 'temp_bulletin_' . $contrat->id . '.pdf';
            $pdf->save($tempBulletinPath);

            // Chemin vers le fichier CGU
            $cguFilePath = public_path('root/cgu/cg_yke.pdf');

       

            // Initialiser FPDI pour fusionner les fichiers
            $finalPdf = new Fpdi();

            // Ajouter toutes les pages du bulletin
            $bulletinPageCount = $finalPdf->setSourceFile($tempBulletinPath);
            for ($pageNo = 1; $pageNo <= $bulletinPageCount; $pageNo++) {
                $finalPdf->AddPage();
                $tplIdx = $finalPdf->importPage($pageNo);
                $finalPdf->useTemplate($tplIdx);
            }
        
            // Ajouter toutes les pages du fichier CGU
            $cguPageCount = $finalPdf->setSourceFile($cguFile);
            for ($pageNo = 1; $pageNo <= $cguPageCount; $pageNo++) {
                $finalPdf->AddPage();
                $tplIdx = $finalPdf->importPage($pageNo);
                $finalPdf->useTemplate($tplIdx);
            }

            // Nom final du fichier fusionné
            $finalBulletinPath = $bulletinDir . 'bulletin_' . $contrat->id . '.pdf';
            $finalPdf->Output($finalBulletinPath, 'F');



            // new code 
            $destinationPath = base_path(env('UPLOADS_PATH'));
            $fileName = $idContrat . '-' . now()->timestamp.'-' .'Bulletin_de_souscription' . '.pdf';
            $finalPdf->Output($destinationPath . $fileName, 'F');



            // Supprimer le fichier temporaire du bulletin
            unlink($tempBulletinPath);

            // Définir l'URL publique pour le fichier final
            $fileUrl = asset("documents/bulletin/bulletin_{$contrat->id}.pdf");

             return response()->file($finalBulletinPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($finalBulletinPath) . '"'
                ]);

            // return [
            //     'success' => true,
            //     'file_url' => $fileUrl,
            //     'redirect_url' => route('prod.edit', ['id' => $idContrat]),
            //     'qrCodeBase64' => $qrCodeBase64 ?? null
            // ];
        } catch (\Exception $e) {
            Log::error("Erreur lors de la génération du bulletin : ", ['error' => $e]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }


    public function dowloadYkeBulletinEtCGU($produit)
    {
        try {
            // Options pour DomPDF
            $options = new Options();
            $options->set('isRemoteEnabled', true);

            // Générer le PDF du bulletin

            if($produit == "YKE_2018"){
                $pdf = PDF::loadView('productions.components.bullettin.ykeBulletinBlanc' )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                // Répertoire et fichiers
                $bulletinDir = public_path('documents/bulletin/');
                if (!is_dir($bulletinDir)) {
                    mkdir($bulletinDir, 0777, true);
                }

                $bulletinTempFile = $bulletinDir . 'temp_bulletin.pdf';
                $finalPdfFile = $bulletinDir . 'Bulletin_Blank.pdf';
                $cguFile = public_path('root/cgu/cg_yke.pdf');

            }else if($produit == "CAD_EDUCPLUS"){

                $pdf = PDF::loadView('productions.components.bullettin.CadenceEduPlusbulletinBlanc' )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                // Répertoire et fichiers
                $bulletinDir = public_path('documents/bulletin/');
                if (!is_dir($bulletinDir)) {
                    mkdir($bulletinDir, 0777, true);
                }

                $bulletinTempFile = $bulletinDir . 'temp_bulletin.pdf';
                $finalPdfFile = $bulletinDir . 'Bulletin_Blank.pdf';
                $cguFile = public_path('root/cgu/CADENCEpLUS.pdf');
                
            } else if($produit == "DOIHOO"){
                $pdf = PDF::loadView('productions.components.bullettin.DoihoobulletinBlanc' )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                // Répertoire et fichiers
                $bulletinDir = public_path('documents/bulletin/');
                if (!is_dir($bulletinDir)) {
                    mkdir($bulletinDir, 0777, true);
                }

                $bulletinTempFile = $bulletinDir . 'temp_bulletin.pdf';
                $finalPdfFile = $bulletinDir . 'Bulletin_Blank.pdf';
                $cguFile = public_path('root/cgu/doihoo_cgu.pdf');
            }else {
                 $pdf = PDF::loadView('productions.components.bullettin.ykeBulletinBlanc' )
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

                // Répertoire et fichiers
                $bulletinDir = public_path('documents/bulletin/');
                if (!is_dir($bulletinDir)) {
                    mkdir($bulletinDir, 0777, true);
                }

                $bulletinTempFile = $bulletinDir . 'temp_bulletin.pdf';
                $finalPdfFile = $bulletinDir . 'Bulletin_Blank.pdf';
                $cguFile = public_path('root/cgu/cg_yke.pdf');
            }

            
            

            // Sauvegarde du bulletin
            $pdf->save($bulletinTempFile);

            // Fusion avec FPDI
            $fpdi = new Fpdi();

            $bulletinPageCount = $fpdi->setSourceFile($bulletinTempFile);
            for ($pageNo = 1; $pageNo <= $bulletinPageCount; $pageNo++) {
                $fpdi->AddPage();
                $tplIdx = $fpdi->importPage($pageNo);
                $fpdi->useTemplate($tplIdx);
            }

            // Ajouter bulletin
            // $fpdi->AddPage();
            // $fpdi->setSourceFile($bulletinTempFile);
            // $tplIdx = $fpdi->importPage(1);
            // $fpdi->useTemplate($tplIdx);


            // Ajouter pages CGU
            $pageCount = $fpdi->setSourceFile($cguFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $fpdi->AddPage();
                $tplIdx = $fpdi->importPage($pageNo);
                $fpdi->useTemplate($tplIdx);
            }

            // Sauvegarde finale
            $fpdi->Output($finalPdfFile, 'F');

            // Supprimer temporaire
            if (file_exists($bulletinTempFile)) {
                unlink($bulletinTempFile);
            }

            // Retourner le PDF final
            return response()->file($finalPdfFile, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($finalPdfFile) . '"'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'type' => 'error',
                'message' => 'Erreur lors de la génération du PDF : ' . $th->getMessage(),
                'code' => 500,
            ]);
        }
    }
    

 


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
