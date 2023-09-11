<?php

namespace App\Controller\voyage;

use App\Entity\Client;
use App\Entity\Voyage;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Entity\GrilleTarifaire;
use App\Repository\ClientRepository;
use App\Form\voyage\GrilleTarifaireType;
use App\Repository\ReservationRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("voyage/grille/tarifaire")
 */
class GrilleTarifaireController extends AbstractController
{
    
    /**
     * @Route("/{id}/edit", name="app_voyage_grilletarifaire_index", methods={"GET", "POST"})
     */
    public function indexGrilletarifaire(Voyage $voyage): Response
    {
        $grillesTarifaires = $voyage->getGrilletarifaire();
        $forms = [];

        foreach ($grillesTarifaires as $grilleTarifaire) {
            $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire);
            $forms[$grilleTarifaire->getId()] = $form->createView();
        }
         $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire, [
            'data' => $grilleTarifaire, 
        ]);

        return $this->render('voyage/grille_tarifaire/index.html.twig', [
            'grilletarifaires' => $grillesTarifaires,
            'forms' => $forms,
            'form' => $form->createView(),
            'voyage' => $voyage,
        ]);
    }


     /**
     * @Route("/reservation/{id}", name="app_voyage_reservation_new",  methods={"GET", "POST"})
     */
    public function reservation(Request $request,GrilleTarifaire $grilletarifaire,ClientRepository $clientRepository, ReservationRepository $reservationRepository): Response
    {
        $client = $this->getUser();
        $reservation = new Reservation();
        $voyage= $grilletarifaire->getOffre();
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

                return $this->render('voyage/edit.html.twig', [
                    'grilletarifaire' => $grilletarifaire,
                    'voyage' => $voyage,
    
                ]);
        }}

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }



    /**
     * @Route("/new/{id}", name="app_voyage_grille_new",  methods={"POST"})
     */
    public function new(Request $request,Voyage $voyage, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $grilletarifaire = new GrilleTarifaire();
        $form = $this->createForm(GrilleTarifaireType::class, $grilletarifaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $grilletarifaire->setOffre($voyage);
            $grilletarifaire->setOffreType('voyage');

            $grilleTarifaireRepository->add($grilletarifaire, true);

            return $this->render('voyage/edit.html.twig', [
                            'grilletarifaire' => $grilletarifaire,
                            'voyage' => $voyage,
            
                        ]);
        }

        return $this->render('voyage/grille_tarifaire/index.html.twig', [
                        'grilletarifaire' => $grilletarifaire,
                        'form' => $form->createView(),
                        'voyage' => $voyage,
                    ]);
    }



     /**
     * @Route("/{id}", name="app_voyage_grille_delete", methods={"POST"})
     */
    public function delete(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $voyage= $grilleTarifaire->getOffre();
        if ($this->isCsrfTokenValid('delete'.$grilleTarifaire->getId(), $request->request->get('_token'))) {
            $grilleTarifaireRepository->remove($grilleTarifaire, true);
            return $this->render('voyage/edit.html.twig', [
                'voyage' => $voyage,
         ]);
        }

        return $this->redirectToRoute('app_voyage_grilletarifaire_index', [], Response::HTTP_SEE_OTHER);
    }

}
