<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
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
            'posts' => $pr->findAll()
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('profile/index.html.twig',['posts'=> $this->getUser()->getPost(),
            'guardedPosts'=>$this->getUser()->getGuardedPosts()
            ]
        );
    }

}
