<?php

namespace App\Controller\backend;

use App\Entity\BonAdresse;
use App\Form\BonAdresseType;
use App\Repository\BonAdresseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/bonadresse"),
 * IsGranted('ROLE_USER')
 */
class BonAdresseController extends AbstractController
{
    /**
     * @Route("/", name="app_bonadresse_index", methods={"GET"})
     */
    public function index(BonAdresseRepository $bonadresseRepository): Response
    {
        return $this->render('bonadresse/index.html.twig', [
            'bonadresses' => $bonadresseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_bonadresse_new", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */  
    public function new(Request $request, BonAdresseRepository $bonadresseRepository, SluggerInterface $slugger): Response
    {
        $bonadresse = new BonAdresse();
        $form = $this->createForm(BonAdresseType::class, $bonadresse);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les fichiers téléchargés
            $photo = $form->get('photo')->getData();
            $images = $form->get('images')->getData();
    
            // Traitement de l'image principale
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
    
                try {
                    $photo->move(
                        $this->getParameter('bonadresse_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
    
                $bonadresse->setImage($newFilename);
            }
    
            // Traitement des images multiples
            $uploadedImages = [];
            foreach ($images as $image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
    
                try {
                    $image->move(
                        $this->getParameter('bonadresse_directory'),
                        $newFilename
                    );
                    $uploadedImages[] = $newFilename;
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
            }
    
            $bonadresse->setImages($uploadedImages);
    
            $bonadresseRepository->add($bonadresse, true);
    
            return $this->redirectToRoute('app_bonadresse_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('bonadresse/new.html.twig', [
            'bonadresse' => $bonadresse,
            'form' => $form,
        ]);
    }
    

    /**
     * @Route("/{id}", name="app_bonadresse_show", methods={"GET"})
     */
    public function show(BonAdresse $bonadresse): Response
    {
        return $this->render('bonadresse/show.html.twig', [
            'bonadresse' => $bonadresse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_bonadresse_edit", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function edit(Request $request, BonAdresse $bonadresse, BonAdresseRepository $bonadresseRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BonAdresseType::class, $bonadresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les fichiers téléchargés
            $photo = $form->get('photo')->getData();
            $images = $form->get('images')->getData();

            // Traitement de l'image principale
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

                try {
                    $photo->move(
                        $this->getParameter('agence_directory'),
                        $newFilename
                    );
                    
                    $bonadresse->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
            }

            // Traitement des images multiples
            $uploadedImages = [];
            foreach ($images as $image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('agence_directory'),
                        $newFilename
                    );
                    
                    $uploadedImages[] = $newFilename;
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
            }
            $bonadresse->setImages($uploadedImages);

            $bonadresseRepository->add($bonadresse, true);

            return $this->redirectToRoute('app_bonadresse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bonadresse/edit.html.twig', [
            'bonadresse' => $bonadresse,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}", name="app_bonadresse_delete", methods={"POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function delete(Request $request, BonAdresse $bonadresse, BonAdresseRepository $bonadresseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bonadresse->getId(), $request->request->get('_token'))) {
            $bonadresseRepository->remove($bonadresse, true);
        }

        return $this->redirectToRoute('app_bonadresse_index', [], Response::HTTP_SEE_OTHER);
    }
}
