<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator,ProductRepository $productRepository,Request $request): Response
    {
        $data=$productRepository->findAll();
        $Produits=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5

        );
        return $this->render('product/cards.html.twig', [
            'products' => $Produits

        ]);
    }
    #[Route('/prods/pdf', name: 'pdfp', methods: ['GET'])]
    public function pdfd (ProductRepository $productRepository,Request $request): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
//        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

//        $produit = $produitRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $data=$productRepository->findAll();
        $html = $this->renderView('product/pdf.html.twig',[
            'products' => $data,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Product.pdf", [
            "Attachment" => true
        ]);
        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    #[Route('/prod/searchp', name: 'search_product', methods: ['GET'])]
    public function searchprod(Request $request, NormalizerInterface $Normalizer, PaginatorInterface $paginator)
    {
        $Produits=$paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findBy(['ProductName' => $request->get('search')]),
            $request->query->getInt('page',1),
            4

        );
        dump($request->get('search'));
        if (null != $request->get('search')) {
            return $this->render('/product/index.html.twig', [
                'products' => $Produits,'user'=>$this->getUser(),
            ]);
        }
        return  $this->redirectToRoute('produit_index');
    }
    #[Route('/back', name: 'app_product_index_back', methods: ['GET'])]
    public function indexback(PaginatorInterface $paginator,ProductRepository $productRepository,Request $request): Response
    {
        $data=$productRepository->findAll();
        $Produits=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            5

        );
        return $this->render('product/index.html.twig', [
            'products' => $Produits

        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('product')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $product->setImage($filename);
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    #[Route('back/{id}', name: 'app_product_show_back', methods: ['GET'])]
    public function showback(Product $product): Response
    {
        return $this->render('product/showback.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('product')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $product->setImage($filename);
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index_back', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/produitAPI', name: 'display_prod_json')]

    public function produitAPI(NormalizerInterface $normalizer): Response
    {
     
        $em = $this->getDoctrine()->getManager()->getRepository(Product::class); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $prods = $em->findAll(); // Select * from produits;
        $jsonContent =$normalizer->normalize($prods, 'json' ,['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/api/deleteProduitApi', name: 'display_prod_json')]

    public function deleteProdApi(Request $request,NormalizerInterface $normalizer,$id): Response
    {

        $em = $this->getDoctrine()->getManager(); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

        $prod = $this->getDoctrine()->getManager()->getRepository(Product::class)->find($id); // ENTITY MANAGER ELY FIH FONCTIONS PREDIFINES

            $em->remove($prod);
            $em->flush();
            $jsonContent =$normalizer->normalize($prod, 'json' ,['groups'=>'post:read']);
            return new Response("information deleted successfully".json_encode($jsonContent));
    }
    #[Route('/api/editProduitAPI/{id}', name: 'editProdJson')]

       public function editProdAPI ($id,Request $request, Product $produit, EntityManagerInterface $entityManager , NormalizerInterface $normalizer ): Response
    {   
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository(Product::class)->find($id);

        $cat = $em->getRepository(Category::class)->find($request->get('cat'));
        $produit->setProductName($request->get('ProductName'));
        $produit->setProductPrice($request->get('ProductPrice'));
        $produit->setProductDescription($request->get('ProductDescription'));
        $produit->setProductQuantity($request->get('ProductQuantity'));
        $produit->setCategory($cat);
        $produit->setUpdateDate(new \DateTime());
        $produit->setImage("img.png");
        $entityManager->persist($produit);
        $entityManager->flush();
        $jsonContent =$normalizer->normalize($produit, 'json' ,['groups'=>'post:read']);
        return new Response("information updated successfully". json_encode($jsonContent));

    }
    #[Route('/api/addProduitAPI', name: 'addproduitjson', methods: ['GET','POST'])]


    public function addproduitjson(NormalizerInterface $Normalizer,Request $request,EntityManagerInterface $entityManager): Response
    {
        $produit = new Product();
        $produit->setUpdateDate(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Category::class)->find($request->get('cat'));
        $produit->setProductName($request->get('ProductName'));
        $produit->setProductPrice($request->get('ProductPrice'));
        $produit->setProductDescription($request->get('ProductDescription'));
        $produit->setProductQuantity($request->get('ProductQuantity'));
        $produit->setCategory($cat);
        $produit->setImage("img.png");
        $em->persist($produit);
        $em->flush();
            $jsonContent = $Normalizer->normalize($produit, 'json',['groups'=>'post:read']);
            return new Response(json_encode($jsonContent));



    }
}
