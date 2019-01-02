<?php

namespace App\Controller\TechNews;

use App\Controller\HelperTrait;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    use HelperTrait;

    /**
     * Démonstration de l'ajout d'un Article
     * avec Doctrine !
     *
     * @Route("/demo/article", name="article_demo")
     */
    public function demo() {

        // Création de la Categorie

        $categorie = new Categorie();

        $categorie->setNom("Politique")
            ->setSlug("politique");

        // création d'un membre

        $membre = new Membre();

        $membre->setPrenom("Karim")
            ->setNom("Abdellati")
            ->setEmail("mail@mail.com")
            ->setPassword("test123")
            ->setRoles(["ROLE_AUTEUR"]);


        // Créationde l'article

        $article = new Article();

        $article->setTitre("La cour d'appel de Paris valide le licenciement de Kerviel")
            ->setSlug("La-cour-dappel-de-Paris-valide-le-licenciement-de-Kerviel")
            ->setContenu("Le licenciement pour faute grave de l'ex-trader Jérôme Kerviel par la Société générale en 2008 était \"fondé\", a jugé mercredi 19 décembre la cour d'appel de Paris, qui a annulé la condamnation de la banque aux prud'hommes. Jérôme Kerviel, qui est débouté de ses demandes, ne touchera pas son bonus de 300 000 euros pour l'année 2007.
                                   En 2016, le conseil de prud'hommes avait estimé qu'il avait été licencié \"sans cause réelle ni sérieuse\", et dans des conditions \"vexatoires\". La banque avait aussitôt fait appel de cette décision qui signait le premier succès judiciaire de Jérôme Kerviel.")
            ->setFeaturedImage("5.jpg")
            ->setSpotlight(1)
            ->setSpecial(0)
            ->setMembre($membre)
            ->setCategorie($categorie);



        /**
         * Recuperation du manager de doctrine.
         */
        $em = $this->getDoctrine()->getManager();

        $em->persist($categorie);
        $em->persist($membre);
        $em->persist($article);
        $em->flush();

        // return une reponseet la vue

        return new Response("Nouvel article ajouté avec l'id");



    }

    /**
     * Formulaire pour ajouter un Article
     * @Route("/creer-un-article",
     * name="article_new")
     * @param Request $request
     * @return Response
     */
    public function newArticle(Request $request)
    {
        # Recuperation d'un Membre
        $membre = $this-> getDoctrine()
            ->getRepository(Membre::class)
            ->find(1);


        # Création d'un nouvel Article
        $article = new Article();
        $article->setMembre($membre);



        # Creation du Formulaire
        $form = $this->createForm('App\form\ArticleFormType', $article)

            ->handleRequest($request);
        ;

        if($form->isSubmitted() && $form->isValid()) {

            // $file stores the uploaded PDF file
            /** @var UploadedFile $featuredImage */
            $featuredImage = $article->getFeaturedImage();

            $fileName = $this->slugify($article->getTitre())
                . '.' . $featuredImage->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $featuredImage->move(
                    $this->getParameter('articles.assets_dir'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $article->setFeaturedImage($fileName);

            # Mise à jour du slug
            $article->setSlug($this->slugify($article->getTitre()));

            // ... persist the $product variable or any other work

            # Sauvgarde en DBB
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            # Notification
            $this->addFlash("notice", "Votre article à bien été ajouter");

            # Redirection
            return $this->redirectToRoute("front_article", [
               'categories' => $article->getCategorie()->getSlug(),
               'slug' => $article->getSlug(),
               'id' => $article->getId()
            ]);

        }

        # Affichage dans la vue
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}