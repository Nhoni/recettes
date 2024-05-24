<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('min_to_hour', [$this, 'minutesToHours']),
        ];
    }

    public function minutesToHours($value) // 60 min = 1h
    {

        if ($value < 60 || !$value ) { //si le temps est inférieur à 60min ou nul
            return $value; //on retourne la valeur
        }

        $hours = floor($value / 60); //on calcule le nombre d'heures
        $minutes = $value % 60; //on calcule le nombre de minutes


        if ($minutes < 10) { //si le nombre de minutes est inférieur à 10min
            $minutes = '0' . $minutes; //on ajoute un 0 devant le nombre de minutes
        }

        $time = sprintf('%sh%s', $hours, $minutes); //on concatène les heures et les minutes

        return $time; //on retourne le temps 
    }
}
