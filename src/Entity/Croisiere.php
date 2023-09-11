<?php

namespace App\Entity;

use App\Repository\CroisiereRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CroisiereRepository::class)
 */
class Croisiere extends Offre
{
    
}
