<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Repository\MicroPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    private MicroPostRepository $microPostRepository;

    public function __construct(MicroPostRepository $microPostRepository)
    {
        $this->microPostRepository = $microPostRepository;
    }

    #[Route('/like/{id}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(MicroPost $microPost, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $microPost->addLikedBy($currentUser);
        $this->microPostRepository->add($microPost, true);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unlike/{id}', name: 'app_unlike')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unLike(MicroPost $microPost, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $microPost->removeLikedBy($currentUser);
        $this->microPostRepository->add($microPost, true);

        return $this->redirect($request->headers->get('referer'));
    }
}
