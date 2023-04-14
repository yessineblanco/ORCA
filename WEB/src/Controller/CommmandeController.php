<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\ProductsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class CommmandeController extends AbstractController
{
    #[Route('/affichecommmande', name: 'affichecommmande')]
    public function index(LigneCommandeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->getDoctrine()->getRepository(LigneCommande::class)->createQueryBuilder('u');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            2 // items per page
        );
        return $this->render('commande/index.html.twig', [
            'commandes' => $repository->findAll(),
            'pagination' => $pagination,

        ]);

    }
    #[Route('/commande', name: 'commande')]
    public function ValidCom(UserRepository $usrRep,SessionInterface $session, ProductsRepository $productRepository, AuthenticationUtils $authenticationUtils): Response
    {
        $panier = $session->get("panier", []);

        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;

        foreach($panier as $id => $quantite){
            $product = $productRepository->find($id);
            $dataPanier[] = [
                "produit" => $product,
                "quantite" => $quantite
            ];
            $total += $product->getPrice() * $quantite;
        }
        $order=new Commande();
        $user=$this->getUser()->getId();
        $currentuser=$usrRep->findOneBy(array('id'=>$user));
        $order->setUser($currentuser);
        $order->setDateCommande(new \DateTime());
        $order->setMontantCommande($total);
        $entityManager = $this->getDoctrine()->getManager(); //orm = OBJECT RELATIONAL MAPPING 
        $entityManager->persist($order); // allocation de memoire et envoie du requete vers bd 
        $entityManager->flush(); // suppression de espace memoire allouee
        foreach ($dataPanier as $item) {
            $productOrder=new LigneCommande();
            $productOrder->setCommande($order);
            $productOrder->setQuantite($item['quantite']);
            $productOrder->setPrice($productRepository->find($item['produit']->getId())->getPrice()*$item['quantite']);
            $productOrder->setProduit($productRepository->find($item['produit']->getId()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($productOrder);
            $entityManager->flush();

        }
        $session->remove("panier");
        $this->addFlash('success', 'Votre commande a ete valider veuillez consulter votre boite mail');
        return $this->redirectToRoute('home');
        }  

        #[Route('/imprimer', name: 'imprimer')]
        public function imprimer(LigneCommandeRepository $Repository)
    {
        //$commande=$commandeRepository->find($id);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/imprime.html.twig', [
            'commandes' => $Repository->findAll()
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
    #[Route('/stats', name: 'stats')]
    public function statistiques(CommandeRepository  $commandeRepository){
        // On va chercher toutes les catégories

        $commande = $commandeRepository->countByDate();
        $dates = [];
        $commandeCount = [];
        $categColor = [];
        foreach($commande as $com){
            $dates[] = $com['dateCommande'];
            $commandeCount[] = $com['count'];
        }


        return $this->render('commande/stats.html.twig', [
            'dates' => json_encode($dates),
            'commandeCount' => json_encode($commandeCount),
        ]);


    }
        
}
