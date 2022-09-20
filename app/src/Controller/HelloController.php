<?php

declare(strict_types=1);

namespace App\Controller;

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

    #[Route('/{limit<\d+>?3}', name: 'app_index')]
    public function index(int $limit): Response
    {
        return $this->render(
            'hello/index.html.twig',
            [
                'messages' => $this->array,
                'limit' => $limit
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