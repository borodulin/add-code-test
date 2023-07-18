<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\CatalogForm;
use App\Form\Data\CatalogData;
use App\Infrastructure\Attribute\ApiForm;
use App\Service\CatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/catalog')]
class CatalogController extends AbstractController
{
    #[Route(methods: ['GET'])]
    public function search(
        CatalogService $catalogService,
        string $term = null,
    ): JsonResponse {
        return $this->json($catalogService->search($term));
    }

    #[Route(methods: ['POST'])]
    public function create(
        #[ApiForm(formClass: CatalogForm::class)]
        CatalogData $catalogData,
        CatalogService $catalogService,
    ): JsonResponse {
        return $this->json($catalogService->create($catalogData));
    }

    #[Route(path: '/{id<\d+>}', methods: ['PATCH'])]
    public function update(
        int $id,
        #[ApiForm(formClass: CatalogForm::class)]
        CatalogData $catalogData,
        CatalogService $catalogService,
    ): JsonResponse {
        $catalog = $catalogService->getCatalog($id);
        if (null === $catalog) {
            throw new NotFoundHttpException();
        }

        return $this->json($catalogService->update($catalog, $catalogData));
    }

    #[Route(path: '/{id<\d+>}', methods: ['DELETE'])]
    public function delete(
        int $id,
        CatalogService $catalogService,
    ): JsonResponse {
        $catalog = $catalogService->getCatalog($id);
        if (null === $catalog) {
            throw new NotFoundHttpException();
        }

        return $this->json($catalogService->delete($catalog));
    }
}
