<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Class ToChar.
 */
class ToChar extends FunctionNode
{
    public mixed $timestamp;

    public mixed $pattern;

    /**
     * This tells Doctrine's Lexer how to parse the expression:
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->timestamp = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->pattern = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * This tells Doctrine how to create SQL from the expression - namely by (basically) keeping it as is:
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'TO_CHAR(%s, %s)',
            $this->timestamp->dispatch($sqlWalker),
            $this->pattern->dispatch($sqlWalker)
        );
    }
}
