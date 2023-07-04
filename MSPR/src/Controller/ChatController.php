<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat/new', name: 'chat_new')]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $ur): JsonResponse
    {
        if($request->getMethod() === "POST"){
            $chat = new Chat();
            $chat->setSender($this->getUser());
            $chat->setReceiver($ur->find($request->get('user_id')));
            $this->getUser()->addChat($chat);
            $em->persist($chat);
            $em->flush();
            return new JsonResponse(["success" => true]);
        }
    }
}
