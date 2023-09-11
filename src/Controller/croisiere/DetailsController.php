<?php
namespace App\Controller\croisiere;

use App\Entity\Croisiere;
use App\Form\croisiere\DetailsType;
use App\Repository\CroisiereRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/croisiere/{id}/edit/details", name="app_croisiere_edit_details", methods={"GET", "POST"})
     */
    public function editDetails(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        $form = $this->createForm(DetailsType::class, $croisiere);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données et enregistrement dans la base de données
            $description = $form->get('description')->getData();
            $inclus = $form->get('inclus')->getData();
            $nonInclus = $form->get('nonInclus')->getData();

            // Mettre à jour les données du croisiere
            $croisiere->setDescription($description);
            $croisiere->setInclus($inclus);
            $croisiere->setNonInclus($nonInclus);
            $croisiereRepository->add($croisiere, true);
            return $this->render('croisiere/edit.html.twig', [
                'croisiere' => $croisiere,
            ]);
        }

        return $this->render('croisiere/details/edit_details.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form->createView(),
        ]);
    }
}
