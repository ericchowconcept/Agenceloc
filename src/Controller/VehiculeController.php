<?php

namespace App\Controller;


use App\Entity\Membre;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


// !App Controller
class VehiculeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(VehiculeRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {
        $vehicules = $repo->findAll($repo);

        return $this->render('vehicule/index.html.twig', [
            
            'vehicules' => $vehicules
        ]);

        
    }
        #[Route('/view/{id}', name:'view')]
        public function view(Vehicule $vehicule, Request $request, EntityManagerInterface $manager): Response
        {
            if($vehicule == null)
        {
            return $this->redirectToRoute('home');
        }

        if($this->getUser())
        {
            $membre = $this->getUser();
        }
        

        $commande = new Commande;

        $formCommande = $this->createForm(CommandeType::class, $commande);
        $formCommande->handleRequest($request);

        if($formCommande->isSubmitted() && $formCommande->isValid())
        {
            
            $dateDepart = $commande->getDateHeureDepart();
            $dateFin    = $commande->getDateHeureFin();
            $nombreJour = date_diff($dateDepart,$dateFin);

            $prixtotal  = $vehicule->getPrixJournalier()*$nombreJour->d;

            $commande   ->setDateEnregistrement(new \DateTime())
                        ->setMembre($membre)
                        ->setVehicule($vehicule)
                        ->setPrixTotal($prixtotal);

            $manager->persist($commande);
            $manager->flush();
        
            $this->addFlash('success', 'Votre commande a été bien enregistré');

            return $this->redirectToRoute('recap', [
                'id' => $commande->getId()
            ]);      
        }
            return $this->render('vehicule/view.html.twig',[
                'formCommande' => $formCommande->createView(),
                'vehicule' => $vehicule
            ]);
        }

        #[Route('/recap/{id}' , name:'recap')]
        public function recap(Commande $commande): Response
        {
            if(!$this->getUser())
            {
                return $this->redirectToRoute('home');  
            }


            return $this->render('vehicule/commandeMembre.html.twig',[
                'commande' => $commande
            ]);
        }
    

    
}
