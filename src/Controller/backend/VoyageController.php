<?php

namespace App\Controller\backend;

use App\Entity\Agent;
use App\Entity\Voyage;
use App\Entity\GrilleTarifaire;
use App\Form\voyage\VoyageType;
use App\Repository\VoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\voyage\InfoGeneralController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/voyage")
 */
class VoyageController extends AbstractController
{
    /**
     * @Route("/", name="app_voyage_index", methods={"GET"})
     */
    public function index(VoyageRepository $voyageRepository): Response
    {
        $voyages = $voyageRepository->findAllWithExcursions();
        return $this->render('voyage/index.html.twig', [
            'voyages' => $voyages
        ]);
    }

    /**
     * @Route("/new", name="app_voyage_new", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function new(Request $request, VoyageRepository $voyageRepository, SluggerInterface $slugger, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_AGENT') && $user instanceof Agent) {
            $agence = $user->getAgence();
            $voyage = new Voyage();
            $voyage->setAgence($agence);
        } else {
            $voyage = new Voyage();
        }
        $form = $this->createForm(VoyageType::class, $voyage);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les fichiers téléchargés
            $photo = $form->get('photo')->getData();
            $images = $form->get('images')->getData();
            
            $grilleTarifaireTitle = $form->get('grilletarifaire_title')->getData();
            $grilleTarifaireDateDebut = $form->get('grilletarifaire_date_debut')->getData();
            $grilleTarifaireDateFin = $form->get('grilletarifaire_date_fin')->getData();
            $grilleTarifairePrix = $form->get('grilletarifaire_prix')->getData();
             // Créer une nouvelle instance de GrilleTarifaire avec les valeurs du formulaire
             if ($grilleTarifaireTitle && $grilleTarifaireDateDebut && $grilleTarifaireDateFin && $grilleTarifairePrix) {
             $grilleTarifaire = new GrilleTarifaire();
             $grilleTarifaire->setTitle($form->get('grilletarifaire_title')->getData());
             $grilleTarifaire->setDateDebut($form->get('grilletarifaire_date_debut')->getData());
             $grilleTarifaire->setDateFin($form->get('grilletarifaire_date_fin')->getData());
             $grilleTarifaire->setPrix($form->get('grilletarifaire_prix')->getData());
             $grilleTarifaire->setOffreType('voyage');
             $grilleTarifaire->setHotel($form->get('hotel')->getData());
             $voyage->addGrilletarifaire($grilleTarifaire);
             }
    
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
                } catch (FileException $e) {
                    // Gérer les exceptions en cas d'échec du téléchargement du fichier
                }
    
                $voyage->setImage($newFilename);
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
    
            $voyage->setImages($uploadedImages);
    
            $voyageRepository->add($voyage, true);
    
            return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('voyage/new.html.twig', [
            'voyage' => $voyage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_voyage_show", methods={"GET"})
     */
    public function show(Voyage $voyage): Response
    {
        return $this->render('voyage/show.html.twig', [
            'voyage' => $voyage,
        ]);
    }

    // /**
    //  * @Route("/{id}/edit", name="app_voyage_edit", methods={"GET", "POST"})
    //  * IsGranted('ROLE_ADMIN')
    //  */
    // public function edit(Request $request, Voyage $voyage, VoyageRepository $voyageRepository, SluggerInterface $slugger): Response
    // {
    //     $form = $this->createForm(VoyageType::class, $voyage);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Récupérer les fichiers téléchargés
    //         $photo = $form->get('photo')->getData();
    //         $images = $form->get('images')->getData();

    //         // Traitement de l'image principale
    //         if ($photo) {
    //             $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
    //             $safeFilename = $slugger->slug($originalFilename);
    //             $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();

    //             try {
    //                 $photo->move(
    //                     $this->getParameter('agence_directory'),
    //                     $newFilename
    //                 );
    //                 // Supprimer l'ancienne image si nécessaire
    //                 // ...
    //                 $voyage->setImage($newFilename);
    //             } catch (FileException $e) {
    //                 // Gérer les exceptions en cas d'échec du téléchargement du fichier
    //             }
    //         }

    //         // Traitement des images multiples
    //         $uploadedImages = [];
    //         foreach ($images as $image) {
    //             $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
    //             $safeFilename = $slugger->slug($originalFilename);
    //             $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

    //             try {
    //                 $image->move(
    //                     $this->getParameter('agence_directory'),
    //                     $newFilename
    //                 );
                   
    //                 $uploadedImages[] = $newFilename;
    //             } catch (FileException $e) {
    //                 // Gérer les exceptions en cas d'échec du téléchargement du fichier
    //             }
    //         }

            
    //         $voyage->setImages($uploadedImages);

    //         $voyageRepository->add($voyage, true);

    //         return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('voyage/edit.html.twig', [
    //         'voyage' => $voyage,
    //         'form' => $form,
    //     ]);
    // }

   
    /**
     * @Route("/{id}/edit", name="app_voyage_edit", methods={"GET", "POST"})
     */
        public function edit(Request $request, Voyage $voyage): Response
        {
            return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
            ]);
        }

   


    /**
     * @Route("/{id}", name="app_voyage_delete", methods={"POST"})
     */
    public function delete(Request $request, Voyage $voyage, VoyageRepository $voyageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyage->getId(), $request->request->get('_token'))) {
            $voyageRepository->remove($voyage, true);
        }

        return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
    }
}
