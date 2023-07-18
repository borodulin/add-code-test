<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Catalog;
use App\Form\CatalogForm;
use App\Form\Data\CatalogData;
use App\Infrastructure\Attribute\ApiForm;
use App\Service\CatalogService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Catalog')]
#[Route(path: '/api/catalog')]
class CatalogController extends AbstractController
{
    #[OA\Parameter(name: 'term', in: 'query')]
    #[Route(methods: ['GET'])]
    public function search(
        CatalogService $catalogService,
        Request $request,
    ): JsonResponse {
        $term = $request->query->get('term');

        return $this->json($catalogService->getRoots($term));
    }

    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CatalogForm::class)))]
    #[OA\Response(
        response: 200,
        description: '',
        content: new OA\JsonContent(ref: new Model(type: Catalog::class))
    )]
    #[Route(methods: ['POST'])]
    public function create(
        #[ApiForm(formClass: CatalogForm::class)]
        CatalogData $catalogData,
        CatalogService $catalogService,
    ): JsonResponse {
        return $this->json($catalogService->create($catalogData));
    }

    #[OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: CatalogForm::class)))]
    #[OA\Response(
        response: 200,
        description: '',
        content: new OA\JsonContent(ref: new Model(type: Catalog::class))
    )]
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
