<?php

namespace App\Controller\TechNews;


use App\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MembreController extends AbstractController
{

    /**
     * @Route("/inscription", name="membre_inscription")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function inscription(Request $request){

        # Creation d'un utilisateur
        $inscription = new Membre();

        # Création du formulaire
        $form = $this->createForm('App\form\MembreFormType', $inscription)

            ->handleRequest($request);
        ;


        # Soumission du formulaire
        if($form->isSubmitted() && $form->isValid()) {

            # Sauvgarde en DBB
            $em = $this->getDoctrine()->getManager();
            $em->persist($inscription);
            $em->flush();

            # Notification
            $this->addFlash("notice", "Votre compte à bien été ajouter");

            # Redirection
            return $this->redirectToRoute("front_article");

        }

        # Affichage dans la vue
        return $this->render('membre/inscription.html.twig', [
            'form' => $form->createView()
        ]);


    }


}