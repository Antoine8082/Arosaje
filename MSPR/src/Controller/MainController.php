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
