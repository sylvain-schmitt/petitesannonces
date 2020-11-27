<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
}
