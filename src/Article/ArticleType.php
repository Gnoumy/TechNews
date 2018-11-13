<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 12/11/2018
 * Time: 14:04
 */

namespace App\Article;


use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return Void
     */
    public function configureOptions(OptionsResolver $resolver): Void
    {
        $resolver->setDefaults([
//           'data_class' => Article::class
           'data_class' => ArticleRequest::class
        ]);
    }


}