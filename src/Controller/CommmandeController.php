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

class CommmandeController extends AbstractController
{
    #[Route('/affichecommmande', name: 'affichecommmande')]
    public function index(LigneCommandeRepository $repository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $repository->findAll(),
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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($order);
        $entityManager->flush();
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
        #[Route('/deletecommande/{id}', name: 'app_commande_delete')]
         public function delete(Request $request, Commande $commande, LigneCommandeRepository $repository): Response
        {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $userRepository->remove($commande, true);
        }

        return $this->redirectToRoute('affichecommmande', [], Response::HTTP_SEE_OTHER);
         }
        
}
