<?php

namespace Envoi;

/**
 * Class Metadata
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class Metadata
{
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_URL = 'url';
    const TYPE_PATH = 'path';

    /** @var string */
    public $type = self:: TYPE_STRING;

    /** @var string|null */
    public $description;

    /** @var bool */
    public $required = false;

    /** @var string|null */
    public $default;

    /** @var string|null */
    public $example;

    /** @var bool */
    public $makeAbsolutePath = false;

    /** @var array|null */
    public $options;
}
