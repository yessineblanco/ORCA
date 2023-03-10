<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/evenement')]
class EvenementController extends AbstractController
{

    #[Route('/back', name: 'back', methods: ['GET'])]
    public function backPage(EvenementRepository $evenementRepository): Response
    {
        return $this->render('back.html.twig', [
          //  'evenements' => $evenementRepository->findAll(),
        ]);
    }




    #[Route('/front', name: 'front', methods: ['GET'])]
    public function frontPage(EvenementRepository $evenementRepository): Response
    {
        return $this->render('front.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }



    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository, PaginatorInterface $paginator , Request $request): Response
    {
        $query = $this->getDoctrine()->getRepository(Evenement::class)->createQueryBuilder('u');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            1 // items per page
        );
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setImage("3.jpg");
            $evenement->getUploadFile();
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement->setImage($evenement->getImage());
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }


    #[Route('delete/{id}', name: 'app_event_delete')]
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository(Evenement::class)->find($id);
        $em->remove($res);
        $em->flush();
        return $this->redirectToRoute('app_evenement_index');
    }
}
