<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/micro-post/{postID<\d+>}/comment/add', name: 'app_comment_add')]
    public function index(int $postID, Request $request, MicroPostRepository $postRepository, CommentRepository $commentRepository): Response
    {
        $microPost = $postRepository->find($postID);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($microPost);
            $comment->setCreatedAt(new DateTimeImmutable());
            $commentRepository->save($comment, true);
            $this->addFlash('success', "The new comment for Post #{$microPost->getId()} was successfully added");

            return $this->redirectToRoute('app_micro_post_show', ['id' => $microPost->getId()]);
        }

        return $this->render('comment/add.html.twig', [
            'post' => $microPost,
            'form' => $form,
        ]);
    }
}
