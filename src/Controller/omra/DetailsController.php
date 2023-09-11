<?php
namespace App\Controller\omra;

use App\Entity\Omra;
use App\Form\omra\DetailsType;
use App\Repository\OmraRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/omra/{id}/edit/details", name="app_omra_edit_details", methods={"GET", "POST"})
     */
    public function editDetails(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        $form = $this->createForm(DetailsType::class, $omra);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données et enregistrement dans la base de données
            $description = $form->get('description')->getData();
            $inclus = $form->get('inclus')->getData();
            $nonInclus = $form->get('nonInclus')->getData();

            // Mettre à jour les données du omra
            $omra->setDescription($description);
            $omra->setInclus($inclus);
            $omra->setNonInclus($nonInclus);
            $omraRepository->add($omra, true);
            return $this->render('omra/edit.html.twig', [
                'omra' => $omra,
            ]);
        }

        return $this->render('omra/details/edit_details.html.twig', [
            'omra' => $omra,
            'form' => $form->createView(),
        ]);
    }
}
