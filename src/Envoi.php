<?php

namespace Envoi;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;


class MetaItem {
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_URL = 'url';
    const TYPE_PATH = 'path';

    public $type = self:: TYPE_STRING;
    public $description;
    public $required;
    public $default;
    public $example;
    public $makeAbsolutePath;
    public $options;
}

/**
 * Class Envoi
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class Envoi
{
    public static function init($envPath = null, $meta = null)
    {
        if (!$envPath) {
            $envPath = '.env';
        }

        if ($meta) {
            if (!is_file($meta)) {
                throw new \InvalidArgumentException(sprintf('No meta file %s', $meta));
            }
        } else {
            if (is_file('.env.yaml')) {
                $meta = '.env.yaml';
            }
        }
        if ($meta) {
            $meta = Yaml::parseFile($meta);
        }

        $varsMeta = [];
        foreach ($meta as $key => $item) {
            $metaItem = new MetaItem();
            $varsMeta[$key] = $metaItem;
        }

        $dotenv = new Dotenv();
        $envVars = $dotenv->parse(file_get_contents($envPath));
    }

    /**
     * @param $envVars
     * @param $varsMeta MetaItem[]
     */
    public static function validate($envVars, $varsMeta)
    {
        foreach ($varsMeta as $key => $meta) {
            if ($meta->required && !isset($envVars[$key])) {
                throw new \InvalidArgumentException('');// TODO custom exception
            }
            $value = $envVars[$key];
            if ($meta->type === MetaItem::TYPE_INT && !is_numeric($value)) {
                throw new \InvalidArgumentException('');// TODO custom exception
            }
            if ($meta->type === MetaItem::TYPE_URL && filter_var($value, FILTER_VALIDATE_URL) === false) {
                throw new \InvalidArgumentException('');// TODO custom exception
            }
            if ($meta->type === MetaItem::TYPE_PATH) {
                if (!is_path($value)) {
                    throw new \InvalidArgumentException('');// TODO custom exception
                }

                $value = realpath($value);
            }
            if ($meta->options && !in_array($value, explode(',', $meta->options))) {
                throw new \InvalidArgumentException('');// TODO custom exception
            }

            $envVars[$key]  = $value;
        }
    }

    /**
     * @param $envVars
     * @param $varsMeta MetaItem[]
     */
    public static function update ($envVars, $varsMeta)
    {

    }
}

Envoi::init(__DIR__ . '/../.env');
