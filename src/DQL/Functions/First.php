<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Class First.
 *
 * @author  Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
final class First extends FunctionNode
{
    private Subselect $subselect;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->subselect = $parser->Subselect();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return '('.$this->subselect->dispatch($sqlWalker).' LIMIT 1)';
    }
}
