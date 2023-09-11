<?php

namespace App\Controller\voyage;

use App\Entity\GrilleTarifaire;
use App\Form\GrilleTarifaireType1;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Form\voyage\GrilleTarifaireType;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("voyage/grille")
 */
class GrilleController extends AbstractController
{
    /**
     * @Route("/", name="app_voyage_grille_tarifaire_index", methods={"GET"})
     */
    public function index(GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('grille_tarifaire/index.html.twig', [
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_voyage_grille_tarifaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $grilleTarifaire = new GrilleTarifaire();
        $form = $this->createForm(GrilleTarifaireType1::class, $grilleTarifaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $grilleTarifaireRepository->add($grilleTarifaire, true);

            return $this->redirectToRoute('app_voyage_grille_tarifaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('grille_tarifaire/new.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_voyage_grille_tarifaire_show", methods={"GET"})
     */
    public function show(GrilleTarifaire $grilleTarifaire): Response
    {
        return $this->render('grille_tarifaire/show.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_voyage_grille_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire, [
        'data' => $grilleTarifaire, // Ajoutez l'option data avec la valeur de l'entité GrilleTarifaire
    ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $voyage= $grilleTarifaire->getOffre();
            $grilleTarifaireRepository->add($grilleTarifaire, true);

           return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
         ]);
        }

        return $this->renderForm('voyage/grille_tarifaire/edit.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_voyage_grille_tarifaire_delete", methods={"POST"})
     */
    public function delete(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$grilleTarifaire->getId(), $request->request->get('_token'))) {
            $grilleTarifaireRepository->remove($grilleTarifaire, true);
        }

        return $this->redirectToRoute('app_voyage_grille_tarifaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
