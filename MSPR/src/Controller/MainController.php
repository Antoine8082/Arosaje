<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        if($this->getUser() == null){
            return $this->redirectToRoute('app_login');
        }
        $pr = $em->getRepository(Post::class);
        return $this->render('main/index.html.twig', [
            'posts' => $pr->findAll(),
            'user' =>  $this->getUser()
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('profile/index.html.twig',[
            'posts'=> $this->getUser()->getPost(),
            'guardedPosts'=>$this->getUser()->getGuardedPosts()
            ]
        );
    }
    #[Route('/deleteprofile', name: 'app_delete_profile')]
    public function deleteProfile(ManagerRegistry $doctrine){
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('No post found');
        }
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_main');
    }
    #[Route('/cgu', name: 'app_agree_terms')]
    public function agreeTerms(EntityManagerInterface $em, Request $request): Response
    {
        return $this->render('main/cgu.html.twig', [
        ]);
    }
}
