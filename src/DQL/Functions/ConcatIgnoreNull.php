<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * "CONCAT" "(" StringPrimary "," StringPrimary {"," StringPrimary }* ")".
 */
class ConcatIgnoreNull extends FunctionNode
{
    public mixed $firstStringPrimary;

    public mixed $secondStringPrimary;

    public array $concatExpressions = [];

    public function getSql(SqlWalker $sqlWalker): string
    {
        $args = [];

        foreach ($this->concatExpressions as $expression) {
            $args[] = $sqlWalker->walkStringPrimary($expression);
        }

        return 'CONCAT('.implode(', ', $args).')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->firstStringPrimary = $parser->StringPrimary();
        $this->concatExpressions[] = $this->firstStringPrimary;

        $parser->match(TokenType::T_COMMA);

        $this->secondStringPrimary = $parser->StringPrimary();
        $this->concatExpressions[] = $this->secondStringPrimary;

        while ($parser->getLexer()->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->concatExpressions[] = $parser->StringPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
