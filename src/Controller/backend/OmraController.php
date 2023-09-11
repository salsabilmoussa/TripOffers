<?php

namespace App\Controller\backend;

use App\Entity\Agent;
use App\Entity\Omra;
use App\Entity\GrilleTarifaire;
use App\Form\omra\OmraType;
use App\Repository\OmraRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\omra\InfoGeneralController;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/omra")
 */
class OmraController extends AbstractController
{
    /**
     * @Route("/", name="app_omra_index", methods={"GET"})
     */
    public function index(OmraRepository $omraRepository): Response
    {
        $omras = $omraRepository->findAll();
        return $this->render('omra/index.html.twig', [
            'omras' => $omras
        ]);
    }

    /**
     * @Route("/new", name="app_omra_new", methods={"GET", "POST"}),
     * IsGranted('ROLE_ADMIN')
     */
    public function new(Request $request, OmraRepository $omraRepository, SluggerInterface $slugger, Security $security): Response
    {
        $user = $security->getUser();

        if ($this->isGranted('ROLE_AGENT') && $user instanceof Agent) {
            $agence = $user->getAgence();
            $omra = new Omra();
            $omra->setAgence($agence);
        } else {
            $omra = new Omra();
        }
        $form = $this->createForm(OmraType::class, $omra);
        
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
             $grilleTarifaire->setOffreType('omra');
             $grilleTarifaire->setHotel($form->get('hotel')->getData());
             $omra->addGrilletarifaire($grilleTarifaire);
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
    
                $omra->setImage($newFilename);
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
    
            $omra->setImages($uploadedImages);
    
            $omraRepository->add($omra, true);
    
            return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('omra/new.html.twig', [
            'omra' => $omra,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_omra_show", methods={"GET"})
     */
    public function show(Omra $omra): Response
    {
        return $this->render('omra/show.html.twig', [
            'omra' => $omra,
        ]);
    }

   
   

   
    /**
     * @Route("/{id}/edit", name="app_omra_edit", methods={"GET", "POST"})
     */
        public function edit(Request $request, Omra $omra): Response
        {
            return $this->render('omra/edit.html.twig', [
                'omra' => $omra,
            ]);
        }

   


    /**
     * @Route("/{id}", name="app_omra_delete", methods={"POST"})
     */
    public function delete(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$omra->getId(), $request->request->get('_token'))) {
            $omraRepository->remove($omra, true);
        }

        return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
    }
}
