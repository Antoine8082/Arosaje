<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Form\EditPlantFormType;
use App\Repository\PlantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/conseil', name: 'conseil_')]
class PlantController extends AbstractController
{
    #[Route('/', name: 'app_plant')]
    public function index(PlantRepository $plants): Response
    {
        if($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }
        if($this->denyAccessUnlessGranted('ROLE_BOT')){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('plant/index.html.twig', [
            'controller_name' => 'PlantController',
            'plants' => $plants->findAll(),
        ]);
    }
    #[Route('/plants', name:'plants')]
    public function plantList(PlantRepository $plants)
    {
        return $this->render('plant/plants.html.twig', [
            'plants' => $plants->findAll(),
        ]);
    }
    #[Route('/modifier/{id}', name:'modifier_plant')]

    public function editPlant(Plant $plant, Request $request, ManagerRegistry $doctrine)
    {

        $form = $this->createForm(EditPlantFormType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($plant);
            $entityManager->flush();

            $this->addFlash('message', 'plante modifiée avec succès');
            return $this->redirectToRoute('conseil_app_plant');
        }

        return $this->render('plant/editplant.html.twig', [
            'plantForm' => $form->createView(),
            'plant' => $plant,
        ]);
    }
    #[Route('/deleteplant/{id}', name: 'app_delete_plant')]
    public function deletePlant(ManagerRegistry $doctrine,Plant $plant){

        if (!$plant) {
            throw $this->createNotFoundException('No post found');
        }
        $em = $doctrine->getManager();
        $em->remove($plant);
        $em->flush();
        return $this->redirectToRoute('conseil_app_plant');
    }
}
