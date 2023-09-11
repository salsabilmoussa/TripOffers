<?php

namespace App\Controller\backend;

use App\Entity\Excursion;
use App\Form\ExcursionType;
use App\Repository\ExcursionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/excursion")
 */
class ExcursionController extends AbstractController
{
    // /**
    //  * @Route("/", name="app_excursion_index", methods={"GET"})
    //  */
    // public function index(ExcursionRepository $excursionRepository): Response
    // {
    //     return $this->render('excursion/index.html.twig', [
    //         'excursions' => $excursionRepository->findAll(),
    //     ]);
    // }

    /**
     * @Route("/", name="app_excursion_index", methods={"GET", "POST"})
     */
    public function index(Request $request, ExcursionRepository $excursionRepository): Response
    {
        $excursion = new Excursion();
        $form = $this->createForm(ExcursionType::class, $excursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $excursionRepository->add($excursion, true);

            return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('excursion/index.html.twig', [
            'excursions' => $excursionRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_excursion_show", methods={"GET"})
     */
    public function show(Excursion $excursion): Response
    {
        return $this->render('excursion/show.html.twig', [
            'excursion' => $excursion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_excursion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Excursion $excursion, ExcursionRepository $excursionRepository): Response
    {
        $form = $this->createForm(ExcursionType::class, $excursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $excursionRepository->add($excursion, true);

            return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('excursion/edit.html.twig', [
            'excursion' => $excursion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_excursion_delete", methods={"POST"})
     */
    public function delete(Request $request, Excursion $excursion, ExcursionRepository $excursionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$excursion->getId(), $request->request->get('_token'))) {
            $excursionRepository->remove($excursion, true);
        }

        return $this->redirectToRoute('app_excursion_index', [], Response::HTTP_SEE_OTHER);
    }
}
