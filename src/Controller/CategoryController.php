<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }



    
    #[Route('/api/categorieAPI', name: 'display_categorie')]
    public function allCatApi(Request $request,NormalizerInterface $normalizer): Response
    {

        $em = $this->getDoctrine()->getManager()->getRepository(Category::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $categorie = $em->findAll(); // Select * from categorie;
        $jsonContent =$normalizer->normalize($categorie, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/api/addCategorieAPI', name: 'addcategoriejson', methods: ['GET', 'POST'])]
 
    public function addcatjson(NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {
        $cat = new Category();
        $em = $this->getDoctrine()->getManager();
        $cat->setCategoryName($request->get('nom_categorie'));
        $cat->setCategoryType($request->get('type_categorie'));
        $em->persist($cat);
        $em->flush();
            $jsonContent = $Normalizer->normalize($cat, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));

    }
       /**
     * @Route("/api/editCatAPI/{id}", name="editCatJson")
     */
    public function editCatAPI ($id,Request $request, Category $cat, EntityManagerInterface $entityManager , NormalizerInterface $normalizer ): Response
    {   
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Category::class)->find($id);
        $cat->setCategoryName($request->get('nom_categorie'));
        $cat->setCategoryType($request->get('type_categorie'));
        
        $entityManager->persist($cat);
        $entityManager->flush();
        $jsonContent =$normalizer->normalize($cat, 'json' ,['groups'=>'post:read']);
        return new Response("information updated successfully". json_encode($jsonContent));

    }
         /**
     * @Route("/api/deleteCatApi/{id}", name="delete_cat_json")
     */
    public function deleteCatApi(Request $request,NormalizerInterface $normalizer,$id): Response
    {

        $em = $this->getDoctrine()->getManager(); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $cat = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

            $em->remove($cat);
            $em->flush();
            $jsonContent =$normalizer->normalize($cat, 'json' ,['groups'=>'post:read']);
            return new Response("information deleted successfully".json_encode($jsonContent));
    }
}
