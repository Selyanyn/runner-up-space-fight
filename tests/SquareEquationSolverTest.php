<?php

use Hproject\SquareEquationSolver;
use PHPUnit\Framework\TestCase;

final class SquareEquationSolverTest extends TestCase
{
    // x^2+1 = 0
    function testEquationWithNoRoots(): void
    {
        $this->assertCount(0, SquareEquationSolver::solve(1.0, 0.0, 1.0));
    }


    // x^2-1 = 0
    function testEquationWithTwoRoots()
    {
        $this->assertCount(2, SquareEquationSolver::solve(1.0, 0.0, -1.0));
    }


    // x^2+2x+1 = 0
    function testEquationWithOneRoot()
    {
        $this->assertCount(1, SquareEquationSolver::solve(1.0, 2.0, 1.0));
    }


    // a = 0
    function testEquationTrhrowsExceptionOnAEqualZero()
    {
        $this->expectException(InvalidArgumentException::class);
        SquareEquationSolver::solve(0.0, 2.0, 1.0);
    }


    // discriminant < epsilon
    function testEquationWithDiscriminantLessThanEpsilon()
    {
        $this->assertCount(1, SquareEquationSolver::solve(1.0, 2.000001, 1.000001));
    }


    /**
     * a = 'a', b=[] e. t. c.
     *
     * Из-за объявленной в корне константы strict_types (стандарт для современной разработки PHP) интерпретатор
     * сам бросает исключение TypeError при получении аргументов неверного типа. Благодаря этому прописывать
     * проверку типов в методе не требуется (а если бы требовалось, проверил бы для все три переменные на is_float()).
     */
    function testEquationWitWrongArgumentTypes()
    {
        $this->expectException(TypeError::class);
        SquareEquationSolver::solve('a', [], false);
    }
}
