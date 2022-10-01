<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro/post', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $microPostRepository->findAllWithComments(),
        ]);
    }

    #[Route('micro/post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('micro/post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, MicroPostRepository $microPostRepository): Response
    {
//        $this->denyAccessUnlessGranted(
//            'IS_AUTHENTICATED_FULLY'
//        );

        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MicroPost $post */
            $post = $form->getData();
            $post->setAuthor($user);
            $microPostRepository->add($post, true);
            $this->addFlash('success', 'Your micro post was successfully created!');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->renderForm(
            'micro_post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('micro/post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $microPostRepository): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MicroPost $post */
            $post = $form->getData();
            $microPostRepository->add($post, true);
            $this->addFlash('success', 'Your micro post was successfully updated!');
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->renderForm(
            'micro_post/edit.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }

    #[Route('micro/post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $commentRepository): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($user);
            $commentRepository->add($comment, true);
            $this->addFlash('success', 'Your comment was added successfully!');
            return $this->redirectToRoute('app_micro_post_show',
                [
                    'post' => $post->getId()
                ]
            );
        }

        return $this->renderForm(
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }
}
