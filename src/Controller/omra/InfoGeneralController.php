<?php
namespace App\Controller\omra;

use App\Entity\Omra;
use App\Form\omra\InfoGeneralType;
use App\Repository\OmraRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class InfoGeneralController extends AbstractController
{
   
    
    /**
     * @Route("/omra/{id}/edit/info-general", name="app_omra_edit_info_general", methods={"GET", "POST"})
     */
    public function editInfoGeneral(Request $request, Omra $omra, OmraRepository $omraRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(InfoGeneralType::class, $omra);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $title = $form->get('title')->getData();
            $agence = $form->get('agence')->getData();
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
                    
                    $omra->setImage($newFilename);
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

            // Mettre à jour les données du omra
            $omra->setTitle($title);
            $omra->setAgence($agence);
            $omra->setImages($uploadedImages);
            $omraRepository->add($omra, true);
 

            return $this->render('omra/edit.html.twig', [
                'omra' => $omra,
            ]);
        }

        return $this->render('omra/info_general/edit_info_general.html.twig', [
            'omra' => $omra,
            'form' => $form->createView(),
        ]);
    }
}
