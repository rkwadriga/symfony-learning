<?php

namespace App\Controller;

use App\Form\MicroPostType;
use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\MicroPostVoter;

#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class MicroPostController extends AbstractController
{
    #[Route('/micro-posts', name: 'app_micro_posts', methods: Request::METHOD_GET)]
    public function index(MicroPostRepository $repository): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $repository->findAllWithCommentsAndUsers(),
        ]);
    }

    #[
        Route('/micro-post/{post<\d+>}', name: 'app_micro_post_show', methods: Request::METHOD_GET),
        IsGranted(MicroPostVoter::VIEW, 'post')
    ]
    public function show(MicroPost $post): Response
    {
        return $this->render('micro_post/show-one.html.twig', [
            'post' => $post,
        ]);
    }

    #[
        Route('/micro-post/add', name: 'app_micro_post_add'),
        IsGranted('ROLE_WRITER')
    ]
    public function add(Request $request, MicroPostRepository $repository): Response
    {
        $microPost = new MicroPost();
        $form = $this->createForm(MicroPostType::class, $microPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $microPost->setOwner($this->getUser());
            $repository->save($microPost, true);
            $this->addFlash('success', 'The new post was successfully created');

            return $this->redirectToRoute('app_micro_posts');
        }

        return $this->render('micro_post/add.html.twig', ['form' => $form, 'post' => $microPost]);
    }

    #[
        Route('/micro-post/{post<\d+>}/edit', name: 'app_micro_post_edit'),
        IsGranted(MicroPostVoter::EDIT, 'post')
    ]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $repository): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($post, true);
            $this->addFlash('success', "Post #{$post->getId()} successfully updated");

            return $this->redirectToRoute('app_micro_posts');
        }

        return $this->render('micro_post/edit.html.twig', ['form' => $form, 'post' => $post]);
    }
}
