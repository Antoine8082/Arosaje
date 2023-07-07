<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Entity\Comments;
use App\Repository\ChatRepository;
use App\Repository\CommentsRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    #[Route('/api/addComment', name : 'app_api_add_comment')]
    function addComment(Request $request,PostRepository $pr, UserRepository $ur, EntityManagerInterface $em){
        if($request->getMethod() == "POST"){
            try{
                $data = json_decode($request->getContent(),true);
                $post = $pr->find($data['post']);
                $userComment = $ur->find($data['sender']);
                $comment = new Comments();
                $comment->setContent($data['content']);
                $comment->setPost($post);
                $comment->setSendDate(new \DateTime('now'));
                $comment->setUserComment($userComment);
                $em->persist($comment);
                $post->addComment($comment);
                $em->flush();
            }catch (\Exception $e){
                return new JsonResponse(false);
            }
            return new JsonResponse(true);
        }
    }
    #[Route('/api/commentsByPost', name : 'app_api_comments_by_post')]
    function commentsByPost(Request $request, PostRepository $pr){
        if($request->getMethod() == "POST"){
            try{
                $data = json_decode($request->getContent(),true);
                $post = $pr->find($data['post']);
                return new JsonResponse(json_encode($post));
            }catch (\Exception $e){
                return new JsonResponse(false);
            }
        }
    }
    #[Route('/api/deleteCommentByPost', name : 'app_api_delete_comments_by_post')]
    function deleteCommentByPost(Request $request, PostRepository $pr, CommentsRepository $cr, EntityManagerInterface $em){
        if($request->getMethod() == "POST"){
            try{
                $data = json_decode($request->getContent(),true);
                $post = $pr->find($data['post']);
                $comment = $cr->findOneBy(['post'=>$post]);
                $post->removeComment($comment);
                $em->flush();
                return new JsonResponse(true);
            }catch (\Exception $e){
                return new JsonResponse(false);
            }
        }
    }

    #[Route('/api/loadChat', name : 'chat_load')]
    public function loadChat(Request $request, ChatRepository $cr, UserRepository $ur, SerializerInterface $serializer ){
        if($request->getMethod() == "POST"){
            $data = json_decode($request->getContent(),true);
            $targetUser = (int)$data['targetUser'];
            $user = $this->getUser();
            $chats = $cr->findAllChat($this->getUser());
            foreach ($chats as $chat){
                $data= [];
                if($chat->getMessages()->count() > 0){
                    $countMessage = $chat->getMessages()->count();
                    $data =[
                        'count' => $countMessage,
                        'target' => $ur->find($targetUser)->getEmail(),
                    ];
                    if($chat->getSender()->getId() == $targetUser || $chat->getReceiver()->getId() == $targetUser){
                        foreach ($chat->getMessages() as $message){
                            $data["messages"][] = [
                                'content' => $message->getContent(),
                                'sender' => $message->getSender()->getEmail(),
                                'date' => $message->getSendAt()->format('d-m-Y H:i:s'),
                                'id' => $chat->getId()
                            ];
                        }
                    }else{
                        $data[] = [
                            'id' => $chat->getId()
                        ];
                    }
                    return new JsonResponse($serializer->serialize($data,'json'),200, [], true);
                }
        }
        }
        return new JsonResponse("Wrong method",400);
    }
}
