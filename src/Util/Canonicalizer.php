<?php

declare(strict_types=1);

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class Canonicalizer.
 */
class Canonicalizer
{
    public function canonicalize(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);
        $result = $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER)
        ;

        return $result;
    }

    public function canonicalizePhone(?string $phone): ?string
    {
        if (null === $phone) {
            return null;
        }

        // Only numbers
        $phone = preg_replace('/\D/', '', trim($phone));
        if (!$phone) {
            return null;
        }

        // Преобразуем телефонный номер только если он начинается с 8 и содержит 11 цифр
        if (preg_match('/^8\d{10}$/', $phone)) {
            $phone = '7'.substr($phone, 1);
        }

        // Если ни +7 ни 8 не указали, то добавляем 7
        if (10 == strlen($phone)) {
            $phone = '7'.$phone;
        }

        return $phone;
    }

    /**
     * Каноничный телефон для колонок типа bigint: слишком длинное число не влезает в bigint.
     */
    public function canonicalizePhoneInt(?string $phone): ?int
    {
        $phone = $this->canonicalizePhone($phone);
        if (null === $phone || !ctype_digit($phone)) {
            return null;
        }

        $digits = ltrim($phone, '0');
        $value = (int) $phone;

        // (int) на переполнении молча даёт PHP_INT_MAX, поэтому сверяем обратным преобразованием
        return '' === $digits || (string) $value === $digits ? $value : null;
    }

    public function canonicalizePhoneReadable($phoneNumber): ?string
    {
        if (null === $phoneNumber) {
            return null;
        }

        if (is_string($phoneNumber)) {
            $phoneNumber = $this->canonicalizePhone($phoneNumber);
        }

        if (null === $phoneNumber || '' === $phoneNumber) {
            return null;
        }

        // +7 999 001-01-01
        if (11 == strlen($phoneNumber)) {
            if (8 == substr($phoneNumber, 0, 1)) {
                $phoneNumber = 7 .substr($phoneNumber, 1);
            }

            return preg_replace("/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/", '+$1 $2 $3-$4-$5', (string) $phoneNumber);
        }

        return (string) $phoneNumber;
    }
}
