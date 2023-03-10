<?php

namespace App\Controller;

use App\Entity\Paticipation;
use App\Form\PaticipationType;
use App\Repository\EvenementRepository;
use App\Repository\PaticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

#[Route('/paticipation')]
class PaticipationController extends AbstractController
{
    #[Route('/', name: 'app_paticipation_index', methods: ['GET'])]
    public function index(PaticipationRepository $paticipationRepository): Response
    {
        return $this->render('paticipation/index.html.twig', [
            'paticipations' => $paticipationRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_paticipation_new', methods: ['GET', 'POST'])]
    public function new(Request $request,$id, PaticipationRepository $paticipationRepository,EvenementRepository $evenementRepository): Response
    {   
        $sid='AC267939e00568d2c20a29d92d0b8705e5';
        $token='a44aab44730a11e9ad6592bce89f5022';
        $from = '+15673473785';
        $twilio = new Client($sid, $token); 
 
        $message = $twilio->messages 
                          ->create("+21622752628", // to  
                                   array(  
                                       "messagingServiceSid" => "MGc55d387310a420153f7aee0e8ff7c814",      
                                       "body" => "Participé" 
                                   ) 
                          );
            $paticipation = new Paticipation();
            $evenement =$evenementRepository->find($id);
            
            $paticipation->setIdEvent($evenement);
            $paticipation->setNomuser("User 1");
            $paticipationRepository->save($paticipation, true);
            



            return $this->redirectToRoute('front', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}', name: 'app_paticipation_show', methods: ['GET'])]
    public function show(Paticipation $paticipation): Response
    {
        return $this->render('paticipation/show.html.twig', [
            'paticipation' => $paticipation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_paticipation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paticipation $paticipation, PaticipationRepository $paticipationRepository): Response
    {
        $form = $this->createForm(PaticipationType::class, $paticipation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paticipationRepository->save($paticipation, true);

            return $this->redirectToRoute('app_paticipation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paticipation/edit.html.twig', [
            'paticipation' => $paticipation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_paticipation_delete', methods: ['POST'])]
    public function delete(Request $request, Paticipation $paticipation, PaticipationRepository $paticipationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paticipation->getId(), $request->request->get('_token'))) {
            $paticipationRepository->remove($paticipation, true);
        }

        return $this->redirectToRoute('app_paticipation_index', [], Response::HTTP_SEE_OTHER);
    }
}
