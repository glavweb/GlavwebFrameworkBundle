<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Class Extract.
 */
class Extract extends FunctionNode
{
    public mixed $part;
    public mixed $timestamp;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $parser->match(TokenType::T_IDENTIFIER);
        $this->part = $parser->getLexer()->token->value;
        $parser->match(TokenType::T_COMMA);
        $this->timestamp = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'EXTRACT(%s FROM %s)',
            $this->part,
            $this->timestamp->dispatch($sqlWalker)
        );
    }
}
