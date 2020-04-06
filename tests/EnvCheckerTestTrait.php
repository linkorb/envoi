<?php

namespace EnvoiTest;

use Envoi\InvalidEnvException;
use Envoi\Metadata;

trait EnvCheckerTestTrait
{
    public function testCheckWillLoadMetaFromTheSuppliedPath()
    {
        $this->checker
            ->expects($this->once())
            ->method('loadMeta')
            ->with('path/to/.env.yaml')
            ->willReturn([])
        ;

        $this->checker->check('path/to/.env.yaml');
    }

    public function testCheckWillThrowExceptionListingErrorsWhenValidationFails()
    {
        // Expect an exception that contains the validation failure message
        $this->expectException(InvalidEnvException::class);
        $this->expectExceptionMessage('IT IS NOT A STRING');

        $_ENV['X_TESTVAR'] = 'Ceci n\'est pas une chaîne';

        $meta = [
            'X_TESTVAR' => $this->makeMetadata(Metadata::TYPE_STRING),
        ];

        $this->checker
            ->method('loadMeta')
            ->willReturn($meta)
        ;
        $this->checker
            ->expects($this->once())
            ->method('validate')
            ->with('X_TESTVAR', 'Ceci n\'est pas une chaîne', $meta['X_TESTVAR'])
            ->willThrowException(new InvalidEnvException('Fail: IT IS NOT A STRING'))
        ;

        $this->checker->check('path/to/.env.yaml');
    }

    public function testCheckWillReturnWhenValidationSucceeds()
    {
        $_ENV['X_TESTVAR'] = 'String!';

        $meta = [
            'X_TESTVAR' => $this->makeMetadata(Metadata::TYPE_STRING),
        ];

        $this->checker
            ->method('loadMeta')
            ->willReturn($meta)
        ;
        $this->checker
            ->expects($this->once())
            ->method('validate')
            ->with('X_TESTVAR', 'String!', $meta['X_TESTVAR'])
        ;

        $this->checker->check('path/to/.env.yaml');
    }

    protected function makeMetadata($type)
    {
        $m = new Metadata();
        $m->type = $type;
        $m->description = '';
        $m->required = false;
        $m->default = null;
        $m->example = '';
        $m->makeAbsolutePath = false;
        $m->options = null;

        return $m;
    }
}
