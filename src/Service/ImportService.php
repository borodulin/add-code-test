<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Catalog;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(string $content): void
    {
        $xml = new \SimpleXMLElement($content);
        foreach ($xml->children() as $child) {
            if (!$this->checkNode($child)) {
                continue;
            }
            $catalog = (new Catalog())
                ->setCode((string) $child->attributes()['code'])
                ->setName((string) $child->attributes()['name']);
            $this->entityManager->persist($catalog);
            $this->logger->warning(\count($child));
            if (\count($child)) {
                $this->processNodes($catalog, $child);
            }
        }
        $this->entityManager->flush();
    }

    private function processNodes(Catalog $parent, \SimpleXMLElement $nodes): void
    {
        foreach ($nodes as $node) {
            if (!$this->checkNode($node)) {
                continue;
            }
            $catalog = (new Catalog())
                ->setCode((string) $node->attributes()['code'])
                ->setName((string) $node->attributes()['name'])
                ->setParent($parent);
            $this->entityManager->persist($catalog);
            $parent->getItems()->add($catalog);
            if (\count($node)) {
                $this->processNodes($catalog, $node);
            }
        }
    }

    private function checkNode(\SimpleXMLElement $node): bool
    {
        $valid = true;
        if (!isset($node->attributes()['code'])) {
            $this->logger->warning('Code attribute is not set');
            $valid = false;
        }
        if (!isset($node->attributes()['name'])) {
            $this->logger->warning('Name attribute is not set');
            $valid = false;
        }

        return $valid;
    }
}
