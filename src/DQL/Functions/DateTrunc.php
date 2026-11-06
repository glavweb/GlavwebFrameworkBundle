<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * Class DateTrunc.
 *
 * DateTrunc ::= "date_trunc" "(" ArithmeticPrimary "," ArithmeticPrimary ")"
 *
 * DATE_TRUNC('month', e.created_dt) as month
 */
class DateTrunc extends FunctionNode
{
    public mixed $firstDateExpression;
    public mixed $secondDateExpression;

    /**
     * This tells Doctrine's Lexer how to parse the expression:
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->firstDateExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->secondDateExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * This tells Doctrine how to create SQL from the expression - namely by (basically) keeping it as is:
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'DATE_TRUNC(%s, %s)',
            $this->firstDateExpression->dispatch($sqlWalker),
            $this->secondDateExpression->dispatch($sqlWalker)
        );
    }
}
