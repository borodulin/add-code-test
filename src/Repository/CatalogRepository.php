<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Catalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Catalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalog[]    findAll()
 * @method Catalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Catalog::class);
    }

    public function findAllCatalogs(): array
    {
        $sql = <<<SQL
            WITH RECURSIVE cte (id, code, name, parent_id, path) AS (
                SELECT c1.id, c1.code, c1.name, c1.parent_id, ARRAY[c1.id]
                FROM catalog c1
                WHERE c1.parent_id IS NULL
                UNION DISTINCT
                SELECT c2.id, c2.code, c2.name, c2.parent_id, cte.path || c2.id
                FROM catalog c2, cte
                WHERE c2.parent_id = cte.id
            )
            SELECT c.id, c.code, c.name, c.parent_id
            FROM cte c
            ORDER BY c.path;
            SQL;

        $iterator = $this->getEntityManager()->getConnection()->iterateAssociative($sql);

        return $this->populateCatalogs($iterator);
    }

    public function findFilteredCatalogs(string $term): array
    {
        $sql = <<<SQL
            WITH RECURSIVE cte (id, code, name, parent_id, path) AS (
                SELECT c1.id, c1.code, c1.name, c1.parent_id, ARRAY[c1.id]
                FROM catalog c1
                WHERE c1.code = :term OR c1.name ILIKE :term_mask
                UNION DISTINCT
                SELECT c2.id, c2.code, c2.name, c2.parent_id, c2.id || cte.path
                FROM catalog c2, cte
                WHERE c2.id = cte.parent_id
            )
            SELECT c.id, c.code, c.name, c.parent_id
            FROM cte c
            ORDER BY c.path;
            SQL;

        $iterator = $this->getEntityManager()->getConnection()->iterateAssociative($sql, [
            'term' => $term,
            'term_mask' => "%$term%",
        ]);

        return $this->populateCatalogs($iterator);
    }

    protected function populateCatalogs(iterable $iterator): array
    {
        $result = [];
        $catalogs = [];
        foreach ($iterator as $row) {
            $catalog = (new Catalog())
                ->setId($row['id'])
                ->setCode($row['code'])
                ->setName($row['name']);
            $catalogs[$catalog->getId()] = $catalog;
            if (null === $row['parent_id']) {
                $result[$catalog->getId()] = $catalog;
            } else {
                $parent = $catalogs[$row['parent_id']];
                $catalog->setParent($parent);
                $parent->getItems()->add($catalog);
            }
        }

        return $result;
    }
}
