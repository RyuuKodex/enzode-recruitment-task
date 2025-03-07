<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Application\Command\CreateProductCommand;
use App\Domain\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddProductAction extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $commandBus) {}

    #[Route('/api/products', name: 'api_add_product', methods: ['POST'])]
    public function __invoke(Request $request, ValidatorInterface $validator, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = $serializer->deserialize(json_encode($data), Product::class, 'json');

        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreateProductCommand(
            $data['name'],
            $data['description'] ?? null,
            (float) $data['price'],
            $data['currencyId'],
            $data['categoryId'],
            $data['attributes'] ?? []
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(['message' => 'Product created successfully'], Response::HTTP_CREATED);
    }
}
