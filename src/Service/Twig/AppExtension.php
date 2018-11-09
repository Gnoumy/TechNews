<?php

namespace App\Service\Twig;


use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    private $em;

    /**
     * AppExtension constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        #Récupération du EntityManagerInterface de Doctrine
        $this->em = $manager;
    }

    public function getFunctions()
    {
        return [
          new \Twig_Function('getCategories', function (){
                return $this->em->getRepository(Categorie::class)->findCategoriesHavingArticles();
          })
        ];
    }

}