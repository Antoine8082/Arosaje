<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\PlantFormType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $users): Response
    {

        if($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }
        if($this->denyAccessUnlessGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users->findAll(),
        ]);
    }
    #[Route('/users', name:'users')]
    public function usersList(UserRepository $users)
    {
        return $this->render('admin/users.html.twig', [
            'users' => $users->findAll(),
        ]);
    }

     #[Route('/utilisateurs/modifier/{id}', name:'modifier_utilisateur')]

    public function editUser(User $user, Request $request, ManagerRegistry $doctrine)
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

    #[Route('/plant', name:'add_plant')]
    public function addPlant(Request $request,ManagerRegistry $doctrine)
    {
        $plant = new Plant();
        $form = $this->createForm(PlantFormType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            $originalFileName = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
            $safeFileName = preg_replace('/[^a-zA-Z0-9]/','_',$originalFileName);
            $newFileName = $safeFileName . '-'. uniqid() . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $newFileName);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($plant);
            $entityManager->flush();

            $this->addFlash('message', 'plante crée avec succès');
            return $this->redirectToRoute('admin_app_admin');
        }

        return $this->render('admin/plant.html.twig', [
            'plantForm' => $form->createView(),
        ]);
    }

}
