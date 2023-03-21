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
        if($request->getMethod() == "POST"){
            $post = new Post();
            $post->setDescription($_POST['description']);
            $post->setTitle($_POST['title']);
            $post->setUserPost($this->getUser());
            $em->persist($post);
            $em->flush();
        }
        $pr = $em->getRepository(Post::class);
        $currentRoute = $request->get('_route');

        return $this->render('main/index.html.twig', [
            'posts' => $pr->findAll(),
            'route' => $currentRoute
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        return $this->render('profile/index.html.twig',['posts'=> $this->getUser()->getPost(),
            'guardedPosts'=>$this->getUser()->getGuardedPosts(),
            'route'=>$currentRoute
            ]
        );
    }

}
