<?php

namespace App\Controller\backend;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/agence")
 */
class AgenceController extends AbstractController
{
    /**
     * @Route("/", name="app_agence_index", methods={"GET"})
     */
    public function index(AgenceRepository $agenceRepository): Response
    {
        return $this->render('agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_agence_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgenceRepository $agenceRepository, SluggerInterface $slugger): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();
            if ($logo) {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logo->guessExtension();
    
                try {
                    $logo->move(
                        $this->getParameter('agence_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
    
                $agence->setLogo($newFilename);
            }
            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agence_show", methods={"GET"})
     */
    public function show(Agence $agence): Response
    {
        return $this->render('agence/show.html.twig', [
            'agence' => $agence,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_agence_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Agence $agence, AgenceRepository $agenceRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les fichiers téléchargés
            $logo = $form->get('logo')->getData();

            // Traitement de l'image principale
            if ($logo) {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logo->guessExtension();

                try {
                    $logo->move(
                        $this->getParameter('agence_directory'),
                        $newFilename
                    );
                    // Supprimer l'ancienne image si nécessaire
                    // ...
                    $agence->setLogo($newFilename);
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
            }


            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agence_delete", methods={"POST"})
     */
    public function delete(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
            $agenceRepository->remove($agence, true);
        }

        return $this->redirectToRoute('app_agence_index', [], Response::HTTP_SEE_OTHER);
    }
}
