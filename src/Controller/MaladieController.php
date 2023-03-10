<?php

namespace App\Controller;


use Knp\Component\Pager\PaginatorInterface;
use App\Form\MaladieType;
use App\Entity\Maladie;
use App\Repository\MaladieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;


class MaladieController extends AbstractController
{
    #[Route('/maladie', name: 'app_maladie')]
    public function index(): Response
    {
        return $this->render('maladie/index.html.twig', [
            'controller_name' => 'MaladieController',
        ]);
    }

    #[Route('/listM', name: 'list_Maladie')]
    public function afficher(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Maladie::class); 

        $Maladie=$repository->findall();
        return $this->render('maladie/list.html.twig', [
            'maladie' => $Maladie,
        ]);

    }


    #[Route('/listF', name: 'list_MaladieF')]
    public function afficherF(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Maladie::class); 

        $Maladie=$repository->findall();
        return $this->render('maladie/listF.html.twig', [
            'maladie' => $Maladie,
        ]);


    }



    #[Route('/supp/{id}', name: 's')]
   
    public function supprimer($id,request $request ): Response
    {
        
        $Maladie=$this->getDoctrine()->getRepository(Maladie::class)->find($id);
        $em= $this->getDoctrine()->getManager(); 
       $em->remove($Maladie);
       $em->flush();
        return $this->redirectToRoute('list_Maladie');


    }
    #[Route('/addM', name: 'a')]

    public function ajouter(Request $request)
    {
        $Maladie= new Maladie();
        $form=$this->createForm(MaladieType::class,$Maladie);
        $form->add('Ajouter', SubmitType::class);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())
        {
         $em=$this->getdoctrine()->getManager();
         $em->persist($Maladie);
         $em->flush();

         return $this->redirectToRoute('list_Maladie');

        }
        return $this->render('maladie/Add.html.twig',[

            'form'=>$form->createView()
        ]);


    }
    
    #[Route('/up/{id}', name: 'u')]

    public function update(MaladieRepository $repository,Request $request ,$id)
    {
        $Maladie=$repository->find($id);
        $form=$this->createForm(MaladieType::class,$Maladie);
        $form->add('modifier', SubmitType::class);
        $form->handleRequest($request);


        if( $form->isSubmitted() && $form->isValid())
        {
         $em=$this->getdoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('list_Maladie');

        }
        return $this->render('maladie/update.html.twig',
        [
            'f'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/utilisateur/searchuser", name="utilsearchuser")
     */
    public function searchPlan(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Maladie::class);
        $requestString = $request->get('searchValue');
        $plan = $repository->findPlanBySujet($requestString);
        return $this->render('maladie/MaladieAjax.html.twig', [
            'maladies' => $plan,
        ]);
    }

    public function imprimer(MaladieRepository $Repository)
    {
        require_once 'vendor/autoload.php';

        //$commande=$commandeRepository->find($id);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('maladie/imprime.html.twig', [
            'maladie' => $Repository->findAll()
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("myOrder.pdf", [
            "Attachment" => false
        ]);
    }
    





    #[Route('/listmaladienom', name: 'listproduitsNom')]

    public function order_By_Nom(Request $request,MaladieRepository $maladieRepository): Response
    {
        $MaladieByNom = $maladieRepository->order_By_Nom();

        return $this->render('maladie/list.html.twig', [
            'maladie' => $MaladieByNom,
        ]);

    }



    #[Route('/listPaginate', name: 'listPaginate')]
    public function Paginator(MaladieRepository $maladieRepository, Request $request,PaginatorInterface $paginator): Response
    {
        $Maladie = $maladieRepository->findAll();
        
        $pagination = $paginator->paginate(
            $maladieRepository->paginationQuery(),
            $request->query->get('page', 1),
            4
        );
        
        return $this->render('maladie/listPaginate.html.twig', [
            "maladie" => $Maladie,
            'pagination'=> $pagination

        ]);


    }












}
