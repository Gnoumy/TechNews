<?php

namespace App\Controller\TechNews;


use App\Article\ArticleRequest;
use App\Article\ArticleRequestHandler;
use App\Article\ArticleType;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    use HelperTrait;
    /**
     * Démonstration de l'ajout d'une article avec Doctrine. (Data Mapper)
     * @Route("test/article/add", name="article_test")
     */
    public function test() {

        # Création d'une catégorie
        $categorie = new Categorie();
        $categorie->setNom('Economie');
        $categorie->setSlug('economie');

        # Création du Membre (Auteur de l'article)
        $membre = new Membre();
        $membre->setPrenom('Fabien');
        $membre->setNom('BRIVE');
        $membre->setEmail('f.brive@tech.news');
        $membre->setPassword('monchatfaitdescalins');
        $membre->setRoles(['ROLE_AUTEUR']);

        # Création de l'article
        $article = new Article();
        $article->setTitre('WF3 rachète un vidéo-projecteur');
        $article->setSlug('wf3-rachete-un-video-projecteur');
        $article->setContenu("<p>Il était enfin temps d'acheter un nouveau vidéo projecteur pour la formation de Nicolas.</p>");
        $article->setFeaturedImage('7.jpg');
        $article->setSpecial(0);
        $article->setSpotlight(1);

        # On associe une catégorie et un auteur à l'article
        $article->setCategorie($categorie);
        $article->setMembre($membre);

        # On sauvegarde le tout avec Doctrine
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorie);
        $em->persist($membre);
        $em->persist($article);
        $em->flush();

        return new Response(
            'Nouvel Article ID : '
            . $article->getId()
            . ' dans la catégorie : '
            . $categorie->getNom()
            . ' de l\'auteur '
            . $membre->getPrenom()
        );
    }

    /**
     * Formulaire pour ajouter un article
     * @Route("creer-un-article", name="article_new")
     * @param Request $request
     * @param ArticleRequestHandler $articleRequestHandler
     * @return Response
     */
    public function newArticle(Request $request, ArticleRequestHandler $articleRequestHandler): Response
    {
        //Récupération de l'Auteur | ou en session
        $membre = $this->getDoctrine()
            ->getRepository(Membre::class)
            ->find(1);

        /**
         * Une fois le formulaire soumit et validé
         * on passe nos données directement au service
         * qui se charge du traitement de l'article
         */
        $article = new ArticleRequest($membre);

        $form = $this->createForm(ArticleType::class, $article)
                ->handleRequest($request);

        #Vérif des données du Formulaire
        if ($form->isSubmitted() && $form->isValid()){

            # Récupération des données ($article devient un objet)
            $article = $articleRequestHandler->handle($article);

            # Flash Messages
            $this->addFlash('notice',
                'Félicitations, votre article est en ligne');

            # Redirection vers l'article
            return $this->redirectToRoute('index_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug'      => $article->getSlug(),
                'id'        => $article->getId()
            ]);
        }

        return $this->render("article/form.html.twig", [
            'form' => $form->createView()
        ]);
    }

}