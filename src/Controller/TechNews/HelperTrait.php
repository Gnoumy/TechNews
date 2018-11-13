<?php

namespace App\Controller\TechNews;


use Behat\Transliterator\Transliterator;

trait HelperTrait
{
    /**
     * Permet de générer un slug à partir d'un String
     * @param $text
     * @return String
     */
    public function slugify( String $text): String
    {
        return Transliterator::transliterate($text);
    }
}