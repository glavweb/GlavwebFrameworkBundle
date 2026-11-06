<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class ArrayUtils.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class ArrayUtils
{
    public static function mergeRecursive(array $array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::mergeRecursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
