<?php

declare(strict_types=1);

/*
* This file is part of the Pobeda package.
*
* (c) Andrey Nilov <nilov@glavweb.ru>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Class Random.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Random extends FunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'RANDOM()';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
