<?php

namespace Envoi;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Envoi
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class Envoi
{
    public const DEFAULT_META_FILE_NAME = '.env.yaml';

    public static function getDefaultFolder()
    {
        return dirname($_SERVER["SCRIPT_FILENAME"]);
    }

    /**
     * @param string|null $envPath
     * @param string|null $metaPath
     * @throws InvalidEnvException
     */
    public static function init(string $envPath = null, string $metaPath = null)
    {
        if (!$envPath) {
            $envPath = self::getDefaultFolder() . DIRECTORY_SEPARATOR . '.env';
        }

        if (!is_file($envPath)) {
            throw new \InvalidArgumentException(sprintf('No env file "%s"', $envPath));
        }

        if (!is_readable($envPath)) {
            throw new \InvalidArgumentException(sprintf('Env file "%s" is not readable', $envPath));
        }


        if ($metaPath) {
            if (!is_file($metaPath)) {
                throw new \InvalidArgumentException(sprintf('No meta file "%s"', $metaPath));
            }
        } else {
            $defaultFilePath =  self::getDefaultFolder() . DIRECTORY_SEPARATOR . self::DEFAULT_META_FILE_NAME;

            if (is_file($defaultFilePath)) {
                $metaPath = $defaultFilePath;
            }
        }

        $dotenv = new Dotenv();
        if ($metaPath) {
            $meta = self::metaFromYamlFile($metaPath);
            $envContent = file_get_contents($envPath);
            $envVars = $dotenv->parse($envContent);

            $envVars = self::applyMetadata($envVars, $meta);

            $dotenv->populate($envVars);
        } else {
            $dotenv->load($envPath);
        }
    }

    /**
     * @param $metaPath
     * @return Metadata[]
     */
    public static function metaFromYamlFile($metaPath)
    {
        $metaContent = Yaml::parseFile($metaPath);

        $meta = [];
        if (!$metaContent) {
            return [];
        }

        foreach ($metaContent as $key => $item) {
            $metadata = new Metadata();
            $metadata->type = $item['type'] ?? Metadata::TYPE_STRING;
            $metadata->description = $item['description'] ?? null;
            $metadata->required = $item['required'] ?? false;
            $metadata->default = $item['default'] ?? null;
            $metadata->example = $item['example'] ?? null;
            $metadata->makeAbsolutePath = $item['make_absolute_path'] ?? null;

            if (isset($item['options'])) {
                $metadata->options = explode(',', $item['options']);
            }

            $meta[$key] = $metadata;
        }

        return $meta;
    }

    /**
     * @param $envVars
     * @param array $meta
     * @return array
     * @throws InvalidEnvException
     */
    public static function applyMetadata($envVars, array $meta): array
    {
        foreach ($meta as $key => $metadata) {
            $value = $envVars[$key] ?? null;

            if ($metadata->required && !$value) {
                throw new InvalidEnvException(sprintf('Env variable "%s" is required', $key));
            }

            if (!$value) {
                if ($metadata->default) {
                    $value = $metadata->default;
                    $envVars[$key]  = $value;
                }
                continue;
            }

            if ($metadata->type === Metadata::TYPE_INT && !is_numeric($value)) {
                throw new InvalidEnvException(sprintf('Env variable "%s" should be int', $key));
            }
            if ($metadata->type === Metadata::TYPE_URL && filter_var($value, FILTER_VALIDATE_URL) === false) {
                throw new InvalidEnvException(sprintf('Env variable "%s" is not valid url', $key));
            }
            if ($metadata->type === Metadata::TYPE_PATH) {
                if (!is_path($value)) {
                    throw new InvalidEnvException(sprintf('Env variable "%s" is not valid path', $key));
                }

                if ($metadata->makeAbsolutePath) {
                    $value = realpath($value);
                }
            }
            if ($metadata->options && !in_array($value, $metadata->options)) {
                throw new InvalidEnvException(sprintf('Env variable "%s" is not included in options "%s"', $key, implode($metadata->options, ', ')));
            }

            $envVars[$key]  = $value;
        }

        return $envVars;
    }

}
