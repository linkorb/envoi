<?php

namespace EnvoiTest;

use Envoi\EnvChecker;
use Envoi\Metadata;

class EnvCheckerTest extends \PHPUnit\Framework\TestCase
{
    use EnvCheckerTestTrait;

    protected $checker;

    protected function setUp(): void
    {
        $this->checker = $this->getMockBuilder(EnvChecker::class)
            ->setMethods(['loadMeta', 'validate'])
            ->getMock();
    }

    public function testCheckWillNotAlterTheEnvironment()
    {
        $_ENV['X_TESTVAR'] = $expectedVarValue = 'String!';

        $meta = [
            'X_TESTVAR' => $this->makeMetadata(Metadata::TYPE_STRING),
        ];

        $this->checker
            ->method('loadMeta')
            ->willReturn($meta)
        ;
        $this->checker
            ->method('validate')
            ->with('X_TESTVAR', $expectedVarValue, $meta['X_TESTVAR'])
            ->willReturn('altered String!')
        ;

        $this->checker->check('path/to/.env.yaml');

        $this->assertSame(
            $expectedVarValue,
            $_ENV['X_TESTVAR'],
            'The value of X_TESTVAR was not modified by EnvChecker.'
        );
    }
}
