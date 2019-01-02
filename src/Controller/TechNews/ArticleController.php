<?php

namespace App\Controller\TechNews;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

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
        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => "Titre de l'Article",
                'attr' => [
                    'placeholder' => "Titre de l'Article"
                ]])


            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
                'label' => false
            ])


            ->add('contenu', TextareaType::class, [
                'required' => true,
                'label' => false,
            ])



            ->add('featuredimage', FileType::class, [
                'attr' => [
                    'class' => "dropify"
                ]
            ])


            ->add('special', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])



            ->add('spotlight', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])


            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon Article'
            ])
            ->getForm()
        ;

        # traitement des donnees POST
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dump($article);
        }

        # Affichage dans la vue
        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}