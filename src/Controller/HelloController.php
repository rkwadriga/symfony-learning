<?php declare(strict_types=1);
/**
 * Created 2023-05-10
 * Author Dmitry Kushneriov
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $messages = [
        ['text' => 'Message 1', 'createdAt' => '2023-05-11 21:12:25'],
        ['text' => 'Message 2', 'createdAt' => '2023-05-10 12:25:03'],
        ['text' => 'Message 3', 'createdAt' => '2023-05-09 17:18:47'],
        ['text' => 'Message 4', 'createdAt' => '2023-05-08 09:12:05'],
        ['text' => 'Message 5', 'createdAt' => '2023-05-07 13:36:15'],
    ];

    #[Route('/', name: 'app_index', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', ['messages' => $this->messages]);
    }

    #[Route('/messages/{limit<\d+>?5}', name: 'app_show_messages', methods: Request::METHOD_GET)]
    public function showMessages(int $limit): Response
    {
        return $this->render('hello/messages.html.twig', [
            'messages' => $this->messages,
            'limit' => $limit,
        ]);
    }

    #[Route('/message/{id<\d+>}', name: 'app_show_message', methods: Request::METHOD_GET)]
    public function showOne(int $id): Response
    {
        return $this->render('hello/message.html.twig', ['message' => $this->messages[$id - 1]]);
    }
}