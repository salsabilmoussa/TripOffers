<?php

namespace App\Entity;

use App\Repository\ExcursionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExcursionsRepository::class)
 */
class Excursions extends Offre
{
   
}
