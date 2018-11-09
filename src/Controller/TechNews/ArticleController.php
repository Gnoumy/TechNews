<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;


use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
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
     */
    public function newArticle()
    {
        //Récupération de l'Auteur | ou en session
        $membre = $this->getDoctrine()
            ->getRepository(Membre::class)
            ->find(1);
        //Création d'un Article
        $article = new Article($membre);

        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class, [
                'required'  => true,
                'label'     => "Titre de l'article",
                'attr'      => [
                    'placeholder' => "Titre de l'Article"
                ]])
            ->add('categorie', EntityType::class, array(
                'class'    => Categorie::class,
                'choice_label'  => function (Categorie $categorie) {
                    return $categorie->getNom();
                }))
            ->add('membre', EntityType::class, array(
                'class'    => Membre::class,
                'choice_label'  => function (Membre $categorie) {
                    return $categorie->getNom();
                }))
            ->add('contenu', CKEditorType::class, [
                'required'  => true,
                'label'  => false,
                'config' => [
                    'toolbar' => 'standard'
                ]
            ])
            ->add('featuredimage', FileType::class, [
                'required'  => true,
                'label'  => 'Choisir une image',
                'attr' => [
                    'class' => 'dropify'
                ]])
            ->add('special', CheckboxType::class, [
                'required'  => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non',
//                    'checked' => true
                ]])
            ->add('spotlight', CheckboxType::class, [
                'required'  => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non',
                ]])
            ->add('submit', SubmitType::class, [
                'label'  => 'Ajouter l\'article',
                'attr'  => [
                    'class' => 'btn btn-primary'
                    ]
            ])
            ->getForm();

        return $this->render("article/form.html.twig", [
            'form' => $form->createView()
        ]);
    }

}