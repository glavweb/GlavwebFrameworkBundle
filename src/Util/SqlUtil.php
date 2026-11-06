<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

use InvalidArgumentException;

/**
 * Class SqlUtil.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class SqlUtil
{
    public static function intList(array $intArray): string
    {
        foreach ($intArray as $value) {
            if (!is_int($value)) {
                throw new InvalidArgumentException('Int array contains non-int value');
            }
        }

        return '('.implode(', ', $intArray).')';
    }

    public static function idsList(array $intOrEntityArray): string
    {
        $ids = [];

        foreach ($intOrEntityArray as $value) {
            $ids[] = is_int($value) ? $value : $value->getId();
        }

        return self::intList($ids);
    }
}
