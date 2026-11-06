<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class Pluralizer.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Pluralizer
{
    public static function pluralize(int $number, string $one, string $two, string $five): string
    {
        $n = abs($number);
        $n %= 100;
        if ($n >= 5 && $n <= 20) {
            return $five;
        }

        $n %= 10;
        if (1 === $n) {
            return $one;
        }

        if ($n >= 2 && $n <= 4) {
            return $two;
        }

        return $five;
    }
}
