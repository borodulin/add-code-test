<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"CLASS", "PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class ServiceConstraint extends Constraint
{
    final public const SERVICE_ERROR = 'c2d4e0f3-674f-4cb0-98d9-af882aa6d8a3';

    public string $message = 'service_validator_message';
    public string $service;
    public ?string $errorPath = null;
    public bool $ignoreNull = true;

    protected static $errorNames = [
        self::SERVICE_ERROR => 'SERVICE_ERROR',
    ];

    public function __construct(
        array $options = null,
        string $service = null,
        string $message = null,
        string $errorPath = null,
        bool $ignoreNull = null,
        array $groups = null,
        $payload = null
    ) {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->service = $service ?? $this->service;
        $this->errorPath = $errorPath ?? $this->errorPath;
        $this->ignoreNull = $ignoreNull ?? $this->ignoreNull;
    }

    public function validatedBy(): string
    {
        return $this->service;
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }

    public function getDefaultOption(): string
    {
        return 'service';
    }
}
