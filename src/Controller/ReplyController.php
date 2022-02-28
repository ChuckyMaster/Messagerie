<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Reply;
use App\Form\ReplyType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
{
    /**
     * @Route("/reply", name="app_reply")
     */
    public function index(): Response
    {
        return $this->render('reply/index.html.twig', [
            'controller_name' => 'ReplyController',
        ]);
    }


    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     *
     * @Route("message/reply/{id}", name="replytomessage", priority="1")
     */
    public function new(Request $request, EntityManagerInterface $manager, Message $message){


        $reply = new Reply();

        $formular = $this->createForm(ReplyType::class, $reply);
        $formular->handleRequest($request);

        if($formular->isSubmitted() && $formular->isValid()) {

            $reply->setUser($this->getUser());
            $reply->setMessage($message);

            $manager->persist($reply);
            $manager->flush();

        }

        return $this->redirectToRoute('showmessage', ['id'=>$reply->getMessage()->getId()]);


    }
}




