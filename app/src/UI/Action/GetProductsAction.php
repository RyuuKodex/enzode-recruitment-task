<?php

declare(strict_types=1);

namespace App\UI\Action;

use App\Application\Query\GetProductsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class GetProductsAction extends AbstractController
{
    use HandleTrait;

    public function __construct(private readonly MessageBusInterface $queryBus) {}

    #[Route('/api/products', name: 'api_get_products', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $query = new GetProductsQuery(
            $request->query->get('name'),
            $this->getCastParam($request, 'category', 'int'),
            $this->getCastParam($request, 'price_min', 'float'),
            $this->getCastParam($request, 'price_max', 'float'),
        );

        $envelope = $this->queryBus->dispatch($query);

        $handledStamp = $envelope->last(HandledStamp::class);
        $products = $handledStamp ? $handledStamp->getResult() : [];

        return new JsonResponse($products, Response::HTTP_OK);
    }

    private function getCastParam(Request $request, string $param, string $type): mixed
    {
        $value = $request->query->get($param);

        if (null === $value) {
            return null;
        }

        if ('int' === $type) {
            return (int) $value;
        }

        if ('float' === $type) {
            return (float) $value;
        }

        return $value;
    }
}
