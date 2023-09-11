<?php

namespace App\Controller\backend;

use App\Entity\Agent;
use App\Entity\Croisiere;
use App\Entity\GrilleTarifaire;
use App\Form\croisiere\CroisiereType;
use App\Repository\CroisiereRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\croisiere\InfoGeneralController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/croisiere")
 */
class CroisiereController extends AbstractController
{
    /**
     * @Route("/", name="app_croisiere_index", methods={"GET"})
     */
    public function index(CroisiereRepository $croisiereRepository): Response
    {
        $croisieres = $croisiereRepository->findAll();
        return $this->render('croisiere/index.html.twig', [
            'croisieres' => $croisieres
        ]);
    }

    /**
     * @Route("/new", name="app_croisiere_new", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function new(Request $request, CroisiereRepository $croisiereRepository, SluggerInterface $slugger, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_AGENT') && $user instanceof Agent) {
            $agence = $user->getAgence();
            $croisiere = new Croisiere();
            $croisiere->setAgence($agence);
        } else {
            $croisiere = new Croisiere();
        }
        $form = $this->createForm(CroisiereType::class, $croisiere);
        
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
             $grilleTarifaire->setOffreType('croisiere');
             $grilleTarifaire->setHotel($form->get('hotel')->getData());
             $croisiere->addGrilletarifaire($grilleTarifaire);
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
    
                $croisiere->setImage($newFilename);
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
    
            $croisiere->setImages($uploadedImages);
    
            $croisiereRepository->add($croisiere, true);
    
            return $this->redirectToRoute('app_croisiere_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('croisiere/new.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_croisiere_show", methods={"GET"})
     */
    public function show(Croisiere $croisiere): Response
    {
        return $this->render('croisiere/show.html.twig', [
            'croisiere' => $croisiere,
        ]);
    }             
   
   
    /**
     * @Route("/{id}/edit", name="app_croisiere_edit", methods={"GET", "POST"})
     */
        public function edit(Request $request, Croisiere $croisiere): Response
        {
            return $this->render('croisiere/edit.html.twig', [
                'croisiere' => $croisiere,
            ]);
        }

   


    /**
     * @Route("/{id}", name="app_croisiere_delete", methods={"POST"})
     */
    public function delete(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$croisiere->getId(), $request->request->get('_token'))) {
            $croisiereRepository->remove($croisiere, true);
        }

        return $this->redirectToRoute('app_croisiere_index', [], Response::HTTP_SEE_OTHER);
    }
}
