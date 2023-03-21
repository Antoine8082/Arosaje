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
    public function detail(Post $post): Response
    {
        return $this->render('post/detail.html.twig', [
            'post' => $post,
        ]);
    }
    #[Route('/newpost', name: 'app_new_post')]
    public function addPost(EntityManagerInterface $em,Request $request): Response
    {
        if($request->getMethod() == "POST"){
        $post = new Post();
        $uploadFile = $request->files->get('img');
        $originalFileName = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_FILENAME);
        $safeFileName = preg_replace('/[^a-zA-Z0-9]/','_',$originalFileName);
        $newFileName = $safeFileName . '-'. uniqid() . '.' . $uploadFile->guessExtension();
        $uploadFile->move($this->getParameter('images_directory'),$newFileName);
        $post->setDescription($_POST['description']);
        $post->setTitle($_POST['title']);
        $post->setUserPost($this->getUser());
        $post->setImage($newFileName);
        $em->persist($post);
        $em->flush();
        $this->redirectToRoute('app_main');
    }

        return $this->render('post/form.html.twig');

    }
}
