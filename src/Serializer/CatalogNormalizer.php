<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Catalog;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CatalogNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Catalog) {
            throw new UnexpectedTypeException($object, Catalog::class);
        }
        $items = array_map(
            fn (Catalog $item) => $this->normalize($item, $format, $context),
            $object->getItems()->toArray()
        );
        $result = [
            'id' => $object->getId(),
            'code' => $object->getCode(),
            'name' => $object->getName(),
        ];
        if (\count($items) > 0) {
            $result['items'] = $items;
        }

        return $result;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Catalog;
    }
}
