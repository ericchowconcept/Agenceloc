<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Vehicule;
use App\Form\VehiculeType;
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

    #[Route('/admin/membre', name:'admin_membre')]
    public function Membre(MembreRepository $repo, EntityManagerInterface $manager)
    {
        
        $membres = $repo->findAll();
       
        return $this->render('admin/gestionMembre.html.twig',[
            'membres' => $membres
        ]);
    }
}
