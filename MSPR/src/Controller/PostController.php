<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts/{id}', name: 'app_post_detail')]
    public function detail(Post $post,Request $request): Response
    {
        $currentRoute = $request->get('_route');
        return $this->render('post/detail.html.twig', [
            'post' => $post,
            'route'=> $currentRoute
        ]);
    }
}
