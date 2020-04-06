<?php

namespace EnvoiTest;

use Envoi\Metadata;
use Envoi\MutableEnvChecker;

class MutableEnvCheckerTest extends \PHPUnit\Framework\TestCase
{
    use EnvCheckerTestTrait;

    protected $checker;

    protected function setUp(): void
    {
        $this->checker = $this->getMockBuilder(MutableEnvChecker::class)
            ->setMethods(['checkUndocumented', 'loadMeta', 'validate'])
            ->getMock();
    }

    public function testCheckWillAlterTheEnvironment()
    {
        $_ENV['X_TESTVAR'] = $initialVarValue = 'String!';

        $meta = [
            'X_TESTVAR' => $this->makeMetadata(Metadata::TYPE_STRING),
        ];

        $this->checker
            ->method('loadMeta')
            ->willReturn($meta)
        ;
        $this->checker
            ->method('validate')
            ->with('X_TESTVAR', $initialVarValue, $meta['X_TESTVAR'])
            ->willReturn('altered String!')
        ;

        $this->checker->check('path/to/.env.yaml');

        $this->assertSame(
            'altered String!',
            $_ENV['X_TESTVAR'],
            'The value of X_TESTVAR was modified by EnvChecker.'
        );
    }
}
