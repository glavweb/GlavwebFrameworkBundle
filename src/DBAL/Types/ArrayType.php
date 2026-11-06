<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

/**
 * Compatibility replacement for removed Doctrine DBAL "array" type (serialize-based).
 * Existing PostgreSQL columns still store PHP-serialized payloads.
 */
final class ArrayType extends Type
{
    public const string NAME = 'array';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        return serialize($value);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        $value = is_resource($value) ? stream_get_contents($value) : $value;

        set_error_handler(static function (int $code, string $message): bool {
            throw new ConversionException(sprintf('Could not convert database value to "%s" as an error was triggered by the unserialization: %s', self::NAME, $message));
        });

        try {
            return unserialize($value);
        } finally {
            restore_error_handler();
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
