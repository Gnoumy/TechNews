<?php

namespace App\Article;


use App\Controller\TechNews\HelperTrait;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleRequestHandler
{
    use HelperTrait;

    private $em;
    private $articleFactory;
    private $articleAssetsDirectory;

    /**
     * ArticleRequestHandler constructor.
     * @param EntityManagerInterface $em
     * @param ArticleFactory $articleFactory
     * @param String $articleAssetsDirectory
     */
    public function __construct(EntityManagerInterface $em, ArticleFactory $articleFactory, string $articleAssetsDirectory)
    {
        $this->em = $em;
        $this->articleFactory = $articleFactory;
        $this->articleAssetsDirectory = $articleAssetsDirectory;
    }

    public function handle(ArticleRequest $request): ?Article
    {
        # Traitement de l'upload de l'image
        /** @var UploadedFile $image */
        $image = $request->getFeaturedImage();

        # Nom du fichier
        $fileName = $this->slugify($request->getTitre().'.'.$image->guessExtension());

        try {
            $image->move($this->articleAssetsDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        # Mise à jour de l'image
        $request->setFeaturedImage($fileName);

        # Mise à jour du Slug
        $request->setSlug($this->slugify($request->getTitre()));

        # Appel de la factory
        $article = $this->articleFactory->createFromArticleRequest($request);

        # Sauvegarde Doctrine
        $this->em->persist($article);
        $this->em->flush();

        return $article;
    }

}