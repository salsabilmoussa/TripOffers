<?php

namespace App\Controller\omra;

use App\Entity\Omra;
use App\Entity\Client;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Entity\GrilleTarifaire;
use App\Repository\ClientRepository;
use App\Form\omra\GrilleTarifaireType;
use App\Repository\ReservationRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("omra/grille/tarifaire")
 */
class GrilleTarifaireController extends AbstractController
{
    
    /**
     * @Route("/{id}/edit", name="app_omra_grilletarifaire_index", methods={"GET", "POST"})
     */
    public function indexGrilletarifaire(Omra $omra): Response
    {
        $grillesTarifaires = $omra->getGrilletarifaire();
        $forms = [];
        $reservation = new Reservation();
        $form1 = $this->createForm(ReservationType::class, $reservation);

        foreach ($grillesTarifaires as $grilleTarifaire) {
            $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire);
            $forms[$grilleTarifaire->getId()] = $form->createView();
        }
        $form = $this->createForm(GrilleTarifaireType::class);

        return $this->render('omra/grille_tarifaire/index.html.twig', [
            'grilletarifaires' => $grillesTarifaires,
            'forms' => $forms,
            'form' => $form->createView(),
            'omra' => $omra,
            'form1' =>  $form1->createView(),
        ]);
    }


    /**
     * @Route("/new/{id}", name="app_omra_grille_new",  methods={"POST"})
     */
    public function new(Request $request,Omra $omra, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $grilletarifaire = new GrilleTarifaire();
        $form = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $grilletarifaire->setOffre($omra);
            $grilletarifaire->setOffreType('omra');

            $grilleTarifaireRepository->add($grilletarifaire, true);

            return $this->render('omra/edit.html.twig', [
                            'grilletarifaire' => $grilletarifaire,
                            'omra' => $omra,
            
                        ]);
        }

        return $this->render('omra/grille_tarifaire/index.html.twig', [
                        'grilletarifaire' => $grilletarifaire,
                        'form' => $form->createView(),
                        'omra' => $omra,
                    ]);
    }

     /**
     * @Route("/reservation/{id}", name="app_omra_reservation_new",  methods={"GET", "POST"})
     */
    public function reservation(Request $request,GrilleTarifaire $grilletarifaire,ClientRepository $clientRepository, ReservationRepository $reservationRepository): Response
    {
        $client = $this->getUser();
        $reservation = new Reservation();
        $omra= $grilletarifaire->getOffre();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        $grillesTarifaires = $grilletarifaire->getOffre()->getGrilletarifaire();
        $forms=[];
        foreach ($grillesTarifaires as $grilleTarifaire) {
            $f = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire);
            $forms[$grilleTarifaire->getId()] = $f->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($client instanceof Client) {
                $client->setNom($form->get('nom')->getData());
                $client->setPrenom($form->get('prenom')->getData());
                $client->setAdresse($form->get('adresse')->getData());
                $client->setPhoneNumber($form->get('numeroDeTelephone')->getData());
                $clientRepository->add($client,true);
                $reservation->setClient($client);
                $reservation->setMessage($form->get('message')->getData());
                $reservation->setOffre($grilletarifaire->getOffre());
                    
                $reservationRepository->add($reservation, true);

                return $this->render('omra/edit.html.twig', [
                    'grilletarifaire' => $grilletarifaire,
                    'omra' => $omra,
    
                ]);
        }}

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }



     /**
     * @Route("/{id}", name="app_omra_grille_delete", methods={"POST"})
     */
    public function delete(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $omra= $grilleTarifaire->getOffre();
        if ($this->isCsrfTokenValid('delete'.$grilleTarifaire->getId(), $request->request->get('_token'))) {
            $grilleTarifaireRepository->remove($grilleTarifaire, true);
            return $this->render('omra/edit.html.twig', [
                'omra' => $omra,
         ]);
        }

        return $this->redirectToRoute('app_grilletarifaire_index', [], Response::HTTP_SEE_OTHER);
    }

}
