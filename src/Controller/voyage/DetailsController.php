<?php
namespace App\Controller\voyage;

use App\Entity\Voyage;
use App\Form\voyage\DetailsType;
use App\Repository\VoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/voyage/{id}/edit/details", name="app_voyage_edit_details", methods={"GET", "POST"})
     */
    public function editDetails(Request $request, Voyage $voyage, VoyageRepository $voyageRepository): Response
    {
        $form = $this->createForm(DetailsType::class, $voyage);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données et enregistrement dans la base de données
            $description = $form->get('description')->getData();
            $inclus = $form->get('inclus')->getData();
            $nonInclus = $form->get('nonInclus')->getData();

            // Mettre à jour les données du voyage
            $voyage->setDescription($description);
            $voyage->setInclus($inclus);
            $voyage->setNonInclus($nonInclus);
            $voyageRepository->add($voyage, true);
            return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
            ]);
        }

        return $this->render('voyage/details/edit_details.html.twig', [
            'voyage' => $voyage,
            'form' => $form->createView(),
        ]);
    }
}
