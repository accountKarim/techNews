<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    public function index() {

        $repository = $this->getDoctrine()
            ->getRepository(Article::class);

        $articles = $repository->findBy([], ['id' => 'DESC']);
        $spotlight = $repository->FindBySpotlight();

        // return new Response("<html><body><h1>PAGE D'ACCUEIL</h1></body></html>");

        return $this->render('front/index.html.twig', [
            'articles' => $articles,
            'spotlight' => $spotlight
        ]);

    }

    public function contact() {

        return new Response("<html><body><h1>PAGE DE CONTACT</h1></body></html>");

    }

    /**
     * Page permettant d'afficher les articles
     * d'une categorie
     *
     * @Route("/categorie/{slug<[a-zA-Z1-9-_/]+>?news}",
     *     methods={"GET"},
     *     name="front_categorie")
     * $Params $slug
     * @return Response
     */

    public function categorie($slug, Categorie $categorie = null) {

        // Methode 1
       // $categorie = $this->getDoctrine()
       //     ->getRepository(Categorie::class)
       //     ->findOneBy(['slug' => $slug]);

       // $articles = $categorie->getArticles();

        // methode 2
        // $articles = $this->getDoctrine()
        //     ->getRepository(Categorie::class)
        //     ->findOneBySlug($slug)
        //     ->getArticles();


        if(null === $categorie) {
            return $this->redirectToRoute('index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        // methode 3
       // return new Response("<html><body><h1>CATEGORIE : $slug</h1></body></html>");
        return $this->render('front/categorie.html.twig', [
            'categorie' => $categorie,
            'articles' => $categorie->getArticles()
        ]);

    }

    /**
     * @Route("/{categorie<[a-zA-Z1-9-/]+>}/{slug<[a-zA-Z1-9-/]+>}_{id<\d+>}.html",
     *     name="front_article")
     * @param $id
     * @param $slug
     * @param $categorie
     * @return Response
     */
    public function article($categorie, $slug, article $article = null) {

        # Exemple d'URL
        # /politique/vinci-autoroutes-va-envoyer-une-facture-aux-automobilistes_9841.html
        // return new Response("<html><body><h1>PAGE ARTICLE : $id</h1></body></html>");

        if(null === $article) {
            return $this->redirectToRoute('index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        // Vérification du SLUG
        if ($article->getSlug() !== $slug || $article->getCategorie()->getSlug() !== $categorie) {
            return $this->redirectToRoute('front_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $article->getId()
            ]);
        }

        return $this->render('front/article.html.twig', [
                'article' => $article
            ]
        );

    }

}