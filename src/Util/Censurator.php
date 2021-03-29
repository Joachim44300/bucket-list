<?php

namespace App\Util;

class Censurator
{
    const CENSOR_TERMS = ['Bonjour', 'Salut', 'merci'];

    public function purify(string $text):string
    {
        foreach (self::CENSOR_TERMS as $censorTerm){
            // str_len permet de compter le nombre de caractère dans une chaîne
            // str_repeat permet d'afficher un caractère un certain nombre de fois
            // str_ireplace permet de remplacer un caractère ou une chaîne de caractère par une autre
            $text = str_ireplace($censorTerm, str_repeat('*', strlen($censorTerm)), $text);
        }

        return $text;
    }
}