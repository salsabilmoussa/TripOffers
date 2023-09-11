<?php

namespace App\Controller\backend;

use App\Entity\Agent;
use App\Entity\Excursions;
use App\Entity\GrilleTarifaire;
use App\Form\excursions\ExcursionsType;
use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\excursions\InfoGeneralController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/excursions")
 */
class ExcursionsController extends AbstractController
{
    /**
     * @Route("/", name="app_excursions_index", methods={"GET"})
     */
    public function index(ExcursionsRepository $excursionsRepository): Response
    {
        $excursionss = $excursionsRepository->findAll();
        return $this->render('excursions/index.html.twig', [
            'excursionss' => $excursionss
        ]);
    }

    /**
     * @Route("/new", name="app_excursions_new", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function new(Request $request, ExcursionsRepository $excursionsRepository, SluggerInterface $slugger, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_AGENT') && $user instanceof Agent) {
            $agence = $user->getAgence();
            $excursions = new Excursions();
            $excursions->setAgence($agence);
        } else {
            $excursions = new Excursions();
        }
        $form = $this->createForm(ExcursionsType::class, $excursions);
        
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
             $grilleTarifaire->setOffreType('excursions');
             $grilleTarifaire->setHotel($form->get('hotel')->getData());
             $excursions->addGrilletarifaire($grilleTarifaire);
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
    
                $excursions->setImage($newFilename);
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
    
            $excursions->setImages($uploadedImages);
    
            $excursionsRepository->add($excursions, true);
    
            return $this->redirectToRoute('app_excursions_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('excursions/new.html.twig', [
            'excursions' => $excursions,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_excursions_show", methods={"GET"})
     */
    public function show(Excursions $excursions): Response
    {
        return $this->render('excursions/show.html.twig', [
            'excursions' => $excursions,
        ]);
    }             
   
   
    /**
     * @Route("/{id}/edit", name="app_excursions_edit", methods={"GET", "POST"})
     */
        public function edit(Request $request, Excursions $excursions): Response
        {
            return $this->render('excursions/edit.html.twig', [
                'excursions' => $excursions,
            ]);
        }

   


    /**
     * @Route("/{id}", name="app_excursions_delete", methods={"POST"})
     */
    public function delete(Request $request, Excursions $excursions, ExcursionsRepository $excursionsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$excursions->getId(), $request->request->get('_token'))) {
            $excursionsRepository->remove($excursions, true);
        }

        return $this->redirectToRoute('app_excursions_index', [], Response::HTTP_SEE_OTHER);
    }
}
