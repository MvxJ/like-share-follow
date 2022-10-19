<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $user, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($user->getId() != $currentUser->getId()) {
            $currentUser->follow($user);
            $this->managerRegistry->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unFollow(User $user, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($user->getId() != $currentUser->getId()) {
            $currentUser->unFollow($user);
            $this->managerRegistry->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
