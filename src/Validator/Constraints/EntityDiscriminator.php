<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Validator\Constraints;

use Attribute;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * Validates that value is a discriminator map key of the given entity class.
 *
 * @Annotation
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class EntityDiscriminator extends Choice
{
    public string $rootClass;

    public function __construct(
        ?string $rootClass = null,
        ?array $choices = null,
        callable|string|null $callback = null,
        ?bool $multiple = null,
        ?bool $strict = null,
        ?int $min = null,
        ?int $max = null,
        ?string $message = null,
        ?string $multipleMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        ?bool $match = null,
    ) {
        if (null === $rootClass) {
            throw new InvalidArgumentException(sprintf('"%s" requires the "rootClass" option.', static::class));
        }

        $this->rootClass = $rootClass;

        parent::__construct(
            null,
            $choices,
            $callback,
            $multiple,
            $strict,
            $min,
            $max,
            $message,
            $multipleMessage,
            $minMessage,
            $maxMessage,
            $groups,
            $payload,
            $match,
        );
    }

    public function getDefaultOption(): ?string
    {
        return 'rootClass';
    }

    public function getRequiredOptions(): array
    {
        return ['rootClass'];
    }
}
