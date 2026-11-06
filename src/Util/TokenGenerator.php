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
 * Class TokenGenerator.
 */
class TokenGenerator
{
    public function generateToken(int $protectionLevel = 32): string
    {
        return trim(strtr(base64_encode(random_bytes(max(1, $protectionLevel))), '+/', '--'), '=-_');
    }

    public function generateUuid(int $type = UUID_TYPE_RANDOM): string
    {
        return uuid_create($type);
    }
}
