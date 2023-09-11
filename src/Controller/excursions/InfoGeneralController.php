<?php
namespace App\Controller\excursions;

use App\Entity\Excursions;
use App\Form\excursions\InfoGeneralType;
use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InfoGeneralController extends AbstractController
{
   
    
    /**
     * @Route("/excursions/{id}/edit/info-general", name="app_excursions_edit_info_general", methods={"GET", "POST"})
     */
    public function editInfoGeneral(Request $request, Excursions $excursions, ExcursionsRepository $excursionsRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(InfoGeneralType::class, $excursions);
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
                    
                    $excursions->setImage($newFilename);
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

            // Mettre à jour les données du excursions
            $excursions->setTitle($title);
            $excursions->setDestination($destination);
            $excursions->setAgence($agence);
            $excursions->setCategorie($categorie);
            $excursions->setImages($uploadedImages);
            $excursionsRepository->add($excursions, true);
 

            return $this->render('excursions/edit.html.twig', [
                'excursions' => $excursions,
            ]);
        }

        return $this->render('excursions/info_general/edit_info_general.html.twig', [
            'excursions' => $excursions,
            'form' => $form->createView(),
        ]);
    }
}
