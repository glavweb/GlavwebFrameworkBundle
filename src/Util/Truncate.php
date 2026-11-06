<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class Truncate.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Truncate
{
    /**
     * Const charset.
     */
    public const CHARSET = 'UTF-8';

    public static function truncate(string $value, int $length = 30, bool $preserve = false, string $separator = '...'): string
    {
        if (mb_strlen($value, self::CHARSET) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, self::CHARSET))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, self::CHARSET)).$separator;
        }

        return $value;
    }
}
