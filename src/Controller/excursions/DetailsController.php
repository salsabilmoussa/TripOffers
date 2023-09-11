<?php
namespace App\Controller\excursions;

use App\Entity\Excursions;
use App\Form\excursions\DetailsType;
use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/excursions/{id}/edit/details", name="app_excursions_edit_details", methods={"GET", "POST"})
     */
    public function editDetails(Request $request, Excursions $excursions, ExcursionsRepository $excursionsRepository): Response
    {
        $form = $this->createForm(DetailsType::class, $excursions);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données et enregistrement dans la base de données
            $description = $form->get('description')->getData();
            $inclus = $form->get('inclus')->getData();
            $nonInclus = $form->get('nonInclus')->getData();

            // Mettre à jour les données du excursions
            $excursions->setDescription($description);
            $excursions->setInclus($inclus);
            $excursions->setNonInclus($nonInclus);
            $excursionsRepository->add($excursions, true);
            return $this->render('excursions/edit.html.twig', [
                'excursions' => $excursions,
            ]);
        }

        return $this->render('excursions/details/edit_details.html.twig', [
            'excursions' => $excursions,
            'form' => $form->createView(),
        ]);
    }
}
