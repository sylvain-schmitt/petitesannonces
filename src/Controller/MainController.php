<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(AnnoncesRepository $annoncesRepo)
    {
        $annonces = $annoncesRepo->findBy(['active' => true], ['created_at' => 'desc'], 10);


        if (!$annonces) {
            throw new NotFoundHttpException('Aucunes annonces trouvée');
        }

        return $this->render('main/index.html.twig', [
            'annonces' => $annonces,
        ]);
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
