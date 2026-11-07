<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Twig;

use BackedEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Renders labels for backed enums that use LabeledEnum (or expose label()).
 */
class ReadableEnumExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('enum_label', $this->enumLabel(...)),
        ];
    }

    public function enumLabel(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof BackedEnum && method_exists($value, 'label')) {
            return $value->label();
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return (string) $value;
    }
}
