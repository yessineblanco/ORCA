<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
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
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class CommmandeController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/verify/email2', name: 'app_verify_email2')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
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
    public function ValidCom(UserRepository $usrRep,SessionInterface $session, ProductsRepository $productRepository, AuthenticationUtils $authenticationUtils, \Swift_Mailer $mailer): Response
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
        $user=$this->getUser();
        $this->emailVerifier->sendEmailConfirmation('app_verify_email2', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply.cyberark@gmail.com', 'ORCA mail Bot'))
                ->to($user->getEmail())
                ->subject('Your Commande Has Been confirmed')
                ->htmlTemplate('registration/val.html.twig')
        );
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
