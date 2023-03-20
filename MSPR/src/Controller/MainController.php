<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $em): Response
    {
        $pr = $em->getRepository(Post::class);
        return $this->render('main/form.html.twig', [
            'posts' => $pr->findAll()
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(EntityManagerInterface $em): Response
    {
        return $this->render('profile/form.html.twig',['posts'=> $this->getUser()->getPosts(),'guardedPosts'=>$this->getUser()->getGuardedPosts()]);
    }

}
