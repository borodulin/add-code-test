<?php

declare(strict_types=1);

namespace App\Form\Data;

use App\Entity\Catalog;

class CatalogData
{
    public string $code;
    public string $name;
    public ?Catalog $parent = null;
}
