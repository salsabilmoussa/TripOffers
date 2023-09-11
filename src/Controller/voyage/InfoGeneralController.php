<?php
namespace App\Controller\voyage;

use App\Entity\Voyage;
use App\Form\voyage\InfoGeneralType;
use App\Repository\VoyageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InfoGeneralController extends AbstractController
{
   
    
    /**
     * @Route("/voyage/{id}/edit/info-general", name="app_voyage_edit_info_general", methods={"GET", "POST"})
     */
    public function editInfoGeneral(Request $request, Voyage $voyage, VoyageRepository $voyageRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(InfoGeneralType::class, $voyage);
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
                    
                    $voyage->setImage($newFilename);
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

            // Mettre à jour les données du voyage
            $voyage->setTitle($title);
            $voyage->setDestination($destination);
            $voyage->setAgence($agence);
            $voyage->setCategorie($categorie);
            $voyage->setImages($uploadedImages);
            $voyageRepository->add($voyage, true);
 

            return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
            ]);
        }

        return $this->render('voyage/info_general/edit_info_general.html.twig', [
            'voyage' => $voyage,
            'form' => $form->createView(),
        ]);
    }
}
