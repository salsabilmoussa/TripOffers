<?php

namespace App\Controller\voyage;

use App\Entity\Voyage;
use App\Entity\Excursions;
use App\Form\voyage\ExcursionsType;
use App\Repository\VoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcursionsController extends AbstractController
{
    /**
     * @Route("/voyage/{id}/edit/excursions", name="app_voyage_edit_excursions", methods={"GET", "POST"})
     */

     public function editExcursions(Request $request, Voyage $voyage, VoyageRepository $voyageRepository): Response
     {
         $form = $this->createForm(ExcursionsType::class, $voyage);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
            $selectedExcursions = $form->get('excursion')->getData();

            foreach ($selectedExcursions as $excursion) {
                $voyage->addExcursion($excursion);
            }
            $voyageRepository->add($voyage, true);

            return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
         ]);
            }
 
            return $this->render('voyage/excursions/edit_excursions.html.twig', [
                'voyage' => $voyage,
                'form' => $form->createView(),
         ]);
     }
 
}
