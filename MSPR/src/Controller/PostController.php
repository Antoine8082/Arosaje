<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Entity\Post;
use App\Entity\User;
use App\Form\GuardianFormType;
use App\Form\PostFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts/{id}', name: 'app_post_detail')]
    public function detail(Post $post,Request $request,EntityManagerInterface $em, UserRepository $ur,ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(GuardianFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('message', 'Gardien ajouté avec succès');
        }
        return $this->render('post/detail.html.twig', [
            'post' => $post,
            'users' => $ur->findAll(),
            'guardianForm' => $form->createView(),
        ]);
    }

    #[Route('/newpost', name: 'app_new_post')]
    public function addPost(Request $request,ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        if($user != null && $form->isSubmitted() && $form->isValid()){
            $uploadFile = $form['image']->getData();;
            $originalFileName = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_FILENAME);
            $safeFileName = preg_replace('/[^a-zA-Z0-9]/','_',$originalFileName);
            $newFileName = $safeFileName . '-'. uniqid() . '.' . $uploadFile->guessExtension();
            $uploadFile->move($this->getParameter('images_directory'),$newFileName);
            if($_POST['latitude'] && $_POST['longitude']){
                $post->setLongitude($_POST['longitude']);
                $post->setLatitude($_POST['latitude']);
            }
            $post->setImage($newFileName);
            $post->setUserPost($user);
            $post->setIsHeld(true);
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('message', 'post créé avec succès');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('post/form.html.twig',[
            'postForm' => $form->createView(),]);

    }
    #[Route('/deletepost/{id}', name: 'app_delete_post')]
    public function deletePost(Post $post,ManagerRegistry $doctrine){
        if (!$post) {
            throw $this->createNotFoundException('No post found');
        }
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirectToRoute('app_main');
    }
}