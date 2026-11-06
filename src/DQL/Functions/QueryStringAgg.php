<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\AST\Subselect;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * QueryStringAgg.
 */
class QueryStringAgg extends FunctionNode
{
    private ?Node $delimiter = null;

    private bool $isDistinct = false;

    public ?OrderByClause $orderBy = null;

    public ?Subselect $subselect = null;

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(TokenType::T_DISTINCT)) {
            $parser->match(TokenType::T_DISTINCT);

            $this->isDistinct = true;
        }

        $this->delimiter = $parser->StringPrimary();

        if ($lexer->isNextToken(TokenType::T_ORDER)) {
            $this->orderBy = $parser->OrderByClause();
        }

        $parser->match(TokenType::T_COMMA);
        $this->subselect = $parser->Subselect();

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        if (null === $this->subselect) {
            throw new \LogicException('Subselect не задан');
        }

        $alias = uniqid('s_');

        $walkSubselect = $sqlWalker->walkSubselect($this->subselect);

        if (1 !== preg_match('/AS (.+?) /i', $walkSubselect, $matches)) {
            throw new \LogicException('Не удалось разобрать псевдоним в подзапросе');
        }

        $path = $matches[1];

        if (null === $this->delimiter) {
            throw new \LogicException('Разделитель не задан');
        }

        return \sprintf('(SELECT STRING_AGG(%s%s, %s%s) FROM (%s) AS %s)',
            $this->isDistinct ? 'DISTINCT ' : '',
            "$alias.$path",
            $sqlWalker->walkStringPrimary($this->delimiter),
            $this->orderBy ? $sqlWalker->walkOrderByClause($this->orderBy) : '',
            $walkSubselect,
            $alias
        );
    }
}
