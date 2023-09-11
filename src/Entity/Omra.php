<?php

namespace App\Entity;

use App\Repository\OmraRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * @Exclude({"destination"})
 */

/**
 * @ORM\Entity(repositoryClass=OmraRepository::class)
 */
class Omra extends Offre
{
   
}
