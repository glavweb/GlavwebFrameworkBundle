<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Enum;

use BackedEnum;
use InvalidArgumentException;
use ValueError;

/**
 * Shared API for labels and choices on backed enums.
 *
 * Each enum must implement {@see label()}.
 */
trait LabeledEnum
{
    abstract public function label(): string;

    /**
     * @return array<string, string> value => label
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->value] = $case->label();
        }

        return $choices;
    }

    /**
     * @return array<string, string> label => value (for Symfony ChoiceType)
     */
    public static function getFormChoices(): array
    {
        return array_flip(static::getChoices());
    }

    /**
     * @return array<string, string> value => label
     */
    public static function getReadableValues(): array
    {
        return static::getChoices();
    }

    public static function getReadableValue(self|string|null $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            $enum = $value instanceof static ? $value : static::from((string) $value);
        } catch (ValueError $e) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Invalid value "%s" for ENUM type "%s".',
                    $value instanceof BackedEnum ? $value->value : (string) $value,
                    static::class
                ),
                0,
                $e
            );
        }

        return $enum->label();
    }

    /**
     * @return list<string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return static::getValues();
    }

    public static function isValueExist(self|string $value): bool
    {
        if ($value instanceof static) {
            return true;
        }

        return static::tryFrom((string) $value) instanceof static;
    }

    public static function assertValidChoice(self|string $value): void
    {
        if (!static::isValueExist($value)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Invalid value "%s" for ENUM type "%s".',
                    $value instanceof BackedEnum ? $value->value : (string) $value,
                    static::class
                )
            );
        }
    }

    public static function coerce(self|string|null $value): ?static
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $value instanceof static ? $value : static::from((string) $value);
    }

    public static function toValue(self|string|null $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $value instanceof static ? $value->value : (string) $value;
    }
}
