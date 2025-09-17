<?php

namespace App\Enum;

enum EtatEnum: string {
    case EnCreation = "En création";
    case Ouvert = "Ouvert";
    case EnCours = "En cours";
    case Ferme = "Fermé";
}