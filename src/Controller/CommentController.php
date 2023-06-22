<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Security\Voter\CommentVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class CommentController extends AbstractController
{
    #[
        Route('/micro-post/{post<\d+>}/comment/add', name: 'app_comment_add'),
        IsGranted(CommentVoter::CREATE, 'post')
    ]
    public function add(MicroPost $post, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment
                ->setPost($post)
                ->setOwner($this->getUser())
            ;
            $commentRepository->save($comment, true);
            $this->addFlash('success', "The new comment for Post #{$post->getId()} was successfully added");

            return $this->redirectToRoute('app_micro_post_show', ['post' => $post->getId()]);
        }

        return $this->render('comment/add.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }
}
