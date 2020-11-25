<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        return $this->render('users/index.html.twig');
    }

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request, MailerInterface $mailer)
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            
            $this->addFlash('message', 'Votre annonce à bien été prise en compte un émail pour la valider vient de vous ètre envoyer');
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            
            $email = (new TemplatedEmail())
                ->from('no-reply@petitesannonces.test')
                ->to($annonce->getUsers()->getEmail())
                ->subject('Annonce depuis le site Petites Annonces')
                ->htmlTemplate('emails/annonce.html.twig')
                ->context([
                    'id' => $annonce->getId(),
                    'mail' => $annonce->getUsers('email'),
                    'sujet' => ('nouvelle annonce'),
                    ])
                ;
                    
            $mailer->send($email);
            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/users/annonces/edit/{id}", name="users_annonces_edit")
     */
    public function editAnnonce(Annonces $annonce, Request $request, MailerInterface $mailer)
    {
        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setActive(false);

            $email = (new TemplatedEmail())
                ->from('no-reply@petitesannonces.test')
                ->to($annonce->getUsers()->getEmail())
                ->subject('Annonce depuis le site Récif Annonces')
                ->htmlTemplate('emails/annonce.html.twig')
                ->context([
                    'id' => $annonce->getId('id'),
                    'mail' => $annonce->getUsers('email'),
                    'sujet' => ('Modification d\'annonce'),
                ])
            ;

            $mailer->send($email);

            $this->addFlash('message', 'Votre annonce à bien été modifier un émail pour la valider vient de vous ètre envoyer');
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    /**
     * @Route("users/activer/{id}", name="users_activer")
     */
    public function activer(Annonces $annonce)
    {
        $annonce->setActive(($annonce->getActive()) ? false : true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        $this->addFlash('message', 'Annonce activer avec succès');
        return $this->redirectToRoute('users');    
    }


     /**
     * @Route("/users/annonces/supprimer/{id}", name="users_annonces_supprimer")
     */
    public function supprimer(Annonces $annonce)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();

        $this->addFlash('message', 'Annonce supprimée avec succès');
        return $this->redirectToRoute('users');
    }
}