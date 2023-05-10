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
    #[Route('/', name: 'app_index', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return new Response('HI!');
    }

    #[Route('/messages/{id<\d+>?898}', name: 'app_show_message', methods: Request::METHOD_GET)]
    public function showMessage(int $id): Response
    {
        return $this->render('hello/index.html.twig', ['message' => "Number #{$id}"]);
    }
}