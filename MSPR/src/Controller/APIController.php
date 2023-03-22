<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    #[Route('/api/guardian', name: 'app_api')]
    public function setGuardian(Request $request,PostRepository $pr, EntityManagerInterface $em, UserRepository $ur): JsonResponse
    {
        if($request->getMethod() == "POST"){
            $data = json_decode($request->getContent(),true);
            $guardianObj = $ur->findOneBy(['email'=> $data['guardian']]);
            if(!$guardianObj){
                return new JsonResponse(false);
            }
            $post = $pr->find($data['post']);
            $post->setGuardian($guardianObj);
            $post->setIsHeld(true);
            $em->flush();
            return new JsonResponse(true);
        }
        return new JsonResponse('Hello');
    }
}
