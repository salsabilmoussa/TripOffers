<?php
namespace App\Controller\croisiere;

use App\Entity\Croisiere;
use App\Form\croisiere\InfoGeneralType;
use App\Repository\CroisiereRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InfoGeneralController extends AbstractController
{
   
    
    /**
     * @Route("/croisiere/{id}/edit/info-general", name="app_croisiere_edit_info_general", methods={"GET", "POST"})
     */
    public function editInfoGeneral(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(InfoGeneralType::class, $croisiere);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $title = $form->get('title')->getData();
            $destination = $form->get('destination')->getData();
            $agence = $form->get('agence')->getData();
            $categorie = $form->get('categorie')->getData();
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
                    
                    $croisiere->setImage($newFilename);
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

            // Mettre à jour les données du croisiere
            $croisiere->setTitle($title);
            $croisiere->setDestination($destination);
            $croisiere->setAgence($agence);
            $croisiere->setCategorie($categorie);
            $croisiere->setImages($uploadedImages);
            $croisiereRepository->add($croisiere, true);
 

            return $this->render('croisiere/edit.html.twig', [
                'croisiere' => $croisiere,
            ]);
        }

        return $this->render('croisiere/info_general/edit_info_general.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form->createView(),
        ]);
    }
}
