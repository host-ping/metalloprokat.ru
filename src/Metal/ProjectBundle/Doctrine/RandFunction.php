<?php

namespace Metal\ProjectBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class RandFunction extends FunctionNode
{
    protected $seed;

    public function getSql(SqlWalker $sqlWalker)
    {
        if ($this->seed) {
            return 'RAND('.$sqlWalker->walkArithmeticExpression($this->seed).')';
        }

        return 'RAND()';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        if ($parser->getLexer()->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);

            return;
        }

        $this->seed = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
