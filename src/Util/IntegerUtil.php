<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

use RuntimeException;

/**
 * Class IntegerUtil.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class IntegerUtil
{
    public static function randomIntWithExcept(int $min, int $max, array $except): int
    {
        $except = array_unique($except);

        if (count($except) > $max - $min) {
            throw new RuntimeException('Parameter $except contains all possible variants.');
        }

        do {
            $result = rand($min, $max);
        } while (in_array($result, $except));

        return $result;
    }
}
