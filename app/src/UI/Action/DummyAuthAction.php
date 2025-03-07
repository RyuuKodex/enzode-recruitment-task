<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Domain\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class DummyAuthAction extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function __invoke(JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $user = new User('dev_user', 'password');

        $token = $jwtManager->create($user);

        return $this->json([
            'token' => $token,
        ]);
    }
}
