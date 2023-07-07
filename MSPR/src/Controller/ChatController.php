<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Message;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ChatController extends AbstractController
{
    #[Route('/chat/new', name: 'chat_new')]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $ur, SerializerInterface $serializer): JsonResponse
    {
        if($request->getMethod() === "POST"){
            $chat = new Chat();
            $receiver = $ur->find($_POST['user_id']);
            $chat->setReceiver($receiver);
            $chat->setSender($this->getUser());
            $receiver->addChat($chat);
            $this->getUser()->addChat($chat);
            $em->persist($chat);
            $em->flush();
            $data = [
                "id" => $chat->getId(),
                "receiver" => $chat->getReceiver()->getEmail(),
                "sender" => $chat->getSender()->getEmail(),
                "idReceiver" => $chat->getReceiver()->getId(),
                "idSender" => $chat->getSender()->getId(),
            ];
            return new JsonResponse(json_encode($data),Response::HTTP_OK,[],true);
        }else{
            return new JsonResponse("Error",Response::HTTP_BAD_REQUEST,[],true);
        }
    }
    #[Route('/chat/send', name: 'chat_send')]
    public function send(Request $request, ChatRepository $cr, EntityManagerInterface $em) : JsonResponse
    {
        if($request->getMethod() == "POST"){
            $data = json_decode($request->getContent(),true);
            $chat = $cr->find($data['chatId']);
            $message = new Message();
            $message->setSender($this->getUser());
            $message->setContent($data['content']);
            $message->setSendAt(new \DateTime('now'));
            $message->setChat($chat);
            $em->persist($message);
            $chat->addMessage($message);
            $em->flush();
            return new JsonResponse(json_encode('Success'),Response::HTTP_OK,[],true);
        }else{
            return new JsonResponse("Error",Response::HTTP_BAD_REQUEST,[],true);
        }
    }
}
