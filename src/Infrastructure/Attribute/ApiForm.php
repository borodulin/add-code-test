<?php

declare(strict_types=1);

namespace App\Infrastructure\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class ApiForm
{
    public function __construct(
        public string $formClass
    ) {
    }
}
