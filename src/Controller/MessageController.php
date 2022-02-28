<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Reply;
use App\Form\MessageType;
use App\Form\ReplyType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="app_messages")
     */
    public function index(MessageRepository $messageRepository): Response
    {
        $message = new Message();

        $formular = $this->createForm(MessageType::class, $message);
        $messages = $messageRepository->findAll();


        return $this->renderForm('message/index.html.twig', [
            'messages' => $messages,
            'formular' => $formular
        ]);
    }


    /**
     * @return Response
     *
     * @Route("/message/{id}", name="showmessage")
     *
     */
    public function show(Message $message):Response{

        $reply = new Reply();
        $formular = $this->createForm(ReplyType::class, $reply);



        return $this->renderForm('message/show.html.twig', [
           'message' => $message,
            'formular' => $formular
        ]);



    }

    /**
     * @param Message $message
     * @param EntityManagerInterface $manager
     *
     *
     * @return RedirectResponse
     * @Route("/deletemessage/{id}", name="deletemessage")
     */
    public function suppr(Message $message, EntityManagerInterface $manager){

        if($message && $message->getUser() == $this->getUser()){

            $manager->remove($message);

            $manager->flush();
        }
    return $this->redirectToRoute("app_messages");
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     *
     * @Route("/message/new", name="newmessage", priority="2")
     */
    public function new(Request $request, EntityManagerInterface $manager){

        $message = new Message();

        $formular = $this->createForm(MessageType::class, $message);

        $formular->handleRequest($request);

        if($formular->isSubmitted() && $formular->isValid()){


            $message = $formular->getData();

            $message->setCreatedAt(new \DateTime());
            $message->setUser($this->getUser());


            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute("app_messages");

        }

        return $this->renderForm('message/index.html.twig', ["formular"=>$formular]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Message $message
     * @return RedirectResponse|Response
     *
     * @Route("message/edit/{id}", name="editmessage", priority="3")
     */
    public function change(Request$request, EntityManagerInterface $manager, Message $message){


        $formular = $this->createForm(MessageType::class, $message);
        $formular->handleRequest($request);

        if($formular->isSubmitted()){

            return $this->redirectToRoute("app_messages");
        }

        return $this->renderForm('message/edit.html.twig', ['formular' => $formular]);


    }


}
