<?php

namespace App\Controller;
use App\Entity\Traitement;
use App\Form\TraitementType;
use App\Repository\TraitementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Routing\Annotation\Route;

class TraitementController extends AbstractController
{
    #[Route('/traitement', name: 'app_traitement')]
    public function index(): Response
    {
        return $this->render('traitement/index.html.twig', [
            'controller_name' => 'TraitementController',
        ]);
    }



    #[Route('/listS', name: 'list_Traitement')]
    public function afficher(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Traitement::class); 

        $Traitement=$repository->findall();
        return $this->render('traitement/listS.html.twig', [
            'traitement' => $Traitement,
        ]);


    }

    #[Route('/listSF', name: 'list_TraitementF')]
    public function afficherSF(ManagerRegistry $doctrine): Response
    {
        $repository= $doctrine->getRepository(Traitement::class); 

        $Traitement=$repository->findall();
        return $this->render('traitement/listSF.html.twig', [
            'traitement' => $Traitement,
        ]);


    }



    #[Route('/suppS/{id}', name: 'ss')]
   
    public function supprimer($id,request $request ): Response
    {
        
        $Traitement=$this->getDoctrine()->getRepository(Traitement::class)->find($id);
        $em= $this->getDoctrine()->getManager(); 
       $em->remove($Traitement);
       $em->flush();
        return $this->redirectToRoute('list_Traitement');


    }

    #[Route('/addS', name: 'as')]

    public function ajouter(Request $request)
    {
        $Traitement= new Traitement();
        $form=$this->createForm(TraitementType::class,$Traitement);
        $form->add('Ajouter', SubmitType::class);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid())
        {
         $em=$this->getdoctrine()->getManager();
         $em->persist($Traitement);
         $em->flush();

         return $this->redirectToRoute('list_Traitement');

        }
        return $this->render('traitement/AddS.html.twig',[

            'form2'=>$form->createView()
        ]);


    }


    #[Route('/upp/{id}', name: 'up')]

    public function update(TraitementRepository $repository,Request $request ,$id)
    {
        $Traitement=$repository->find($id);
        $form=$this->createForm(TraitementType::class,$Traitement);
        $form->add('modifier', SubmitType::class);
        $form->handleRequest($request);


        if( $form->isSubmitted() && $form->isValid())
        {
         $em=$this->getdoctrine()->getManager();
        $em->flush();
        return $this->redirectToRoute('list_Traitement');

        }
        return $this->render('traitement/updates.html.twig',
        [
            'form2'=>$form->createView()
        ]);
    }

    /**
     * @Route("/admin/utilisateur/searchuserT", name="utilsearchuserT")
     */
    public function searchPlan(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Traitement::class);
        $requestString = $request->get('searchValue');
        $plan = $repository->findPlanBySujet($requestString);
        return $this->render('traitement/TraitementAjax.html.twig', [
            'traitements' => $plan,
        ]);
    }

    
    #[Route('/listTraitementNom', name: 'listcategorieNom')]

    public function order_By_Nom2(Request $request,TraitementRepository $traitementRepository): Response
    {
        $TraitementByNom = $traitementRepository->order_By_Nom2();

        return $this->render('traitement/listS.html.twig', [
            'traitement' => $TraitementByNom,
        ]);

    }

    #[Route('/listTraitementType', name: 'listcategorieType')]

    public function order_By_Nom3(Request $request,TraitementRepository $traitementRepository): Response
    {
        $TraitementByType = $traitementRepository->order_By_Nom3();

        return $this->render('traitement/listS.html.twig', [
            'traitement' => $TraitementByType,
        ]);

    }
    


}
