<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Catalog;
use App\Form\Data\CatalogData;
use App\Repository\CatalogRepository;
use Doctrine\ORM\EntityManagerInterface;

class CatalogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CatalogRepository $catalogRepository,
    ) {
    }

    public function create(CatalogData $catalogData): Catalog
    {
        $catalog = (new Catalog())
            ->setCode($catalogData->code)
            ->setName($catalogData->name)
            ->setParent($catalogData->parent);
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();

        return $catalog;
    }

    public function update(Catalog $catalog, CatalogData $catalogData): Catalog
    {
        $catalog->setCode($catalogData->code)
            ->setName($catalogData->name)
            ->setParent($catalogData->parent);
        $this->entityManager->flush();

        return $catalog;
    }

    public function getCatalog(int $id): ?Catalog
    {
        return $this->entityManager->find(Catalog::class, $id);
    }

    public function delete(Catalog $catalog): ?Catalog
    {
        $this->entityManager->remove($catalog);
        $this->entityManager->flush();

        return null;
    }

    public function getRoots(?string $term): array
    {
        if (null === $term) {
            return $this->catalogRepository->findAllCatalogs();
        } else {
            return $this->catalogRepository->findFilteredCatalogs($term);
        }
    }
}
