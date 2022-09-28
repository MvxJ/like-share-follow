<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $array = [
        ['message' => 'Hello', 'created' => '2022/09/18'],
        ['message' => 'Hi', 'created' => '2022/07/12'],
        ['message' => 'Bye!', 'created' => '2021/05/12']
    ];

    #[Route('/', name: 'app_index')]
    public function index(MicroPostRepository $postsRepository): Response
    {
        return $this->render(
            'hello/index.html.twig',
            [
                'messages' => $this->array,
                'limit' => 3
            ]
        );
    }

    #[Route('/message/{id}', name: 'app_show_one', requirements: ['id' => '\d+'])]
    public function showOne(int $id): Response
    {
        return $this->render(
            'hello/show_one.html.twig',
            [
                'message' => $this->array[$id]
            ]
        );
    }
}