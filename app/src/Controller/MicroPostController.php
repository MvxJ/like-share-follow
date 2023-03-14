<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/', name: 'app_micro_post')]
    public function index(MicroPostRepository $microPostRepository): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $microPostRepository->findAllWithComments(),
        ]);
    }

    #[Route('/micro/post/topLiked', name: 'app_micro_post_top_liked')]
    public function topLiked(MicroPostRepository $microPostRepository): Response
    {
        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $microPostRepository->findALlWithMinLikes(1),
        ]);
    }

    #[Route('/micro/post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $microPostRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('micro_post/follows.html.twig', [
            'posts' => $microPostRepository->findAllByAuthors($user->getFollows()),
        ]);
    }

    #[Route('micro/post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('micro/post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('ROLE_WRITER')]
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
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request, MicroPostRepository $microPostRepository): Response
    {
//        $this->denyAccessUnlessGranted(MicroPost::EDIT, $post);

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
