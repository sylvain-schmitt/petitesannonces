<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\AnnoncesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AnnoncesRepository $annoncesRepo, Request $request)
    {
        
        $limit = 10;
        $page = (int)$request->query->get("page", 1);
        $annonces = $annoncesRepo->getPaginatedAnnonces($page, $limit);
        $total = $annoncesRepo->getTotalAnnonces();

        if (!$annonces) {
            throw new NotFoundHttpException('Aucunes annonces trouvée');
        }

        return $this->render('main/index.html.twig', compact('annonces', 'total', 'limit', 'page'));
    }

    /**
     * @Route("/details/{slug}", name="annonces_details")
     */
    public function details($slug, AnnoncesRepository $annoncesRepo)
    {
        $annonce = $annoncesRepo->findOneBy(['slug' => $slug]);

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }



        return $this->render('main/details.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * @Route("/download/{slug}", name="annonces_download")
     */
    public function download($slug, AnnoncesRepository $annoncesRepo)
    {
        $annonce = $annoncesRepo->findOneBy(['slug' => $slug]);

        if (!$annonce) {
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }

               // On définit les options du PDF
               $pdfOptions = new Options();
               // Police par défaut
               $pdfOptions->set('defaultFont', 'Arial');
               $pdfOptions->setIsRemoteEnabled(true);
       
               // On instancie Dompdf
               $dompdf = new Dompdf($pdfOptions);
               $context = stream_context_create([
                   'ssl' => [
                       'verify_peer' => FALSE,
                       'verify_peer_name' => FALSE,
                       'allow_self_signed' => TRUE
                   ]
               ]);
               $dompdf->setHttpContext($context);
       
               // On génère le html
               $html = $this->renderView('main/download.html.twig', [
                'annonce' => $annonce,
            ]);
       
               $dompdf->loadHtml($html);
               $dompdf->setPaper('A4', 'portrait');
               $dompdf->render();
        // On génère un nom de fichier
        $fichier = 'annonce-' . $slug .'.pdf';

        // On envoie le PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);               return new Response();
    }
}
