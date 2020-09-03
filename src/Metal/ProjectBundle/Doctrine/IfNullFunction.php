<?php

namespace Metal\ProjectBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

/**
 * @author Andrew Mackrodt <andrew@ajmm.org>
 */
class IfNullFunction extends FunctionNode
{
    private $expr1;
    private $expr2;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
		//Тут используем получения ConditionalExpression потому что у нас выражение, а не просто значение
        $this->expr1 = $parser->ConditionalExpression();
        $parser->match(Lexer::T_COMMA);
        $this->expr2 = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'IFNULL('
        .$sqlWalker->walkConditionalPrimary($this->expr1). ', '
        .$sqlWalker->walkArithmeticPrimary($this->expr2).')';
    }
}
