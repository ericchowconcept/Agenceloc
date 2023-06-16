<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Vehicule;
use App\Form\MembreType;
use App\Form\VehiculeType;
use App\Repository\CommandeRepository;
use App\Repository\MembreRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/vehicule', name:'admin_vehicule')]
    #[Route('/admin/vehicule/edit/{id}', name:'admin_vehicule_edit')]
    public function Vehicule(Request $request, EntityManagerInterface $manager, VehiculeRepository $repo, Vehicule $vehicule = null): Response
    {
        if($vehicule == null)
        {
            $vehicule = new Vehicule;
        }
        $vehicules = $repo->findAll();

        $formVehicule = $this->createForm(VehiculeType::class, $vehicule);
        $formVehicule->handleRequest($request);
        
        if($formVehicule->isSubmitted() && $formVehicule->isValid())
        {
            $vehicule->setDateEnregistrement(new \DateTime());
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', "L'article a bien été enregistré");

            return $this->redirectToRoute('admin_vehicule');
        }
        

        return $this->render('admin/gestionVehicule.html.twig',[
            
            'vehicules' => $vehicules,
            'formVehicule' => $formVehicule->createView(),
            'editMode' => $vehicule->getId()!=null
        ]);
    }
    #[Route('/admin/vehicule/delete/{id}', name:'admin_vehicule_delete')]
    public function removeVehicule(Vehicule $vehicule, EntityManagerInterface $manager)
    {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('success', "L'article a bien été supprimé");
        return $this->redirectToRoute('admin_vehicule');

    }
    #[Route('/admin/membres/edit/{id}', name:'admin_membre_edit')]
    #[Route('/admin/membres', name:'admin_membre')]
    public function Membre(MembreRepository $repo, EntityManagerInterface $manager, Request $request, Membre $membre = null)
    {
        if( $membre == null)
        {
            $membre = new Membre;
        }
        
        $membres = $repo->findAll();
       
        $formMembre = $this->createForm(MembreType::class, $membre);
        $formMembre->handleRequest($request);

        if($formMembre->isSubmitted() && $formMembre->isValid())
        {
            $membre->setDateEnregistrement(new \DateTime());
            $manager->persist($membre);
            $manager->flush();
            $this->addFlash('success', "Le membre est bien modifié");

            return $this->redirectToRoute('admin_membre');
        }

        return $this->render('admin/gestionMembre.html.twig',[
            'formMembre' => $formMembre->createView(),
            'membres' => $membres,
            'editMembre' => $membre->getId()!=null
        ]);
    }

    #[Route('/admin/membres/delete/{id}', name:'admin_membre_delete')]
    public function deleteMembre(Membre $membre, EntityManagerInterface $manager)
    {
        $manager->remove($membre);
        $manager->flush();
        $this->addFlash('success', "L'article a bien été supprimé");
        return $this->redirectToRoute('admin_membre');
    }

    
    #[Route('/admin/commandes', name:'admin_commande')]
    public function commande(CommandeRepository $repo)
    {
        $commandes = $repo->findAll();

        return $this->render('admin/gestionCommande.html.twig',[
            'commandes' => $commandes
        ]);
    }
}
