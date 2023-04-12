<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Repository\PlantRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function usersList(PlantRepository $plants)
    {
        return $this->render('plant/plants.html.twig', [
            'plants' => $plants->findAll(),
        ]);
    }
    #[Route('/modifier/{id}', name:'modifier_plant')]

    public function editPlant(Plant $plant, Request $request, ManagerRegistry $doctrine)
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_app_admin');
        }

        return $this->render('admin/edituser.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
