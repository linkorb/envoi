<?php

namespace Envoi;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Dotenv\Dotenv;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Envoi
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class Envoi
{
    public const VERSION = "1.1.1";

    public const DEFAULT_ENV_FILE_NAME = '.env';
    public const DEFAULT_META_FILE_NAME = '.env.yaml';
    public const DEFAULT_MARKDOWN_FILE = 'README.md';

    /**
     * @param string|null $envPath
     * @param string|null $metaPath
     * @param bool|true   $usePutenv
     * @throws InvalidEnvException
     */
    public static function init(string $envPath = null, string $metaPath = null, $usePutenv = true)
    {
        if (!$envPath) {
            $envPath = self::DEFAULT_ENV_FILE_NAME;
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
            $defaultFilePath = self::DEFAULT_META_FILE_NAME;

            if (is_file($defaultFilePath)) {
                $metaPath = $defaultFilePath;
            }
        }

        $dotenv = new Dotenv($usePutenv);
        if ($metaPath) {
            $meta = self::metaFromYamlFile($metaPath);
            $envContent = file_get_contents($envPath);
            $envVars = $dotenv->parse($envContent);

            $envVars = self::validate($envVars, $meta);

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
        if (!$metaContent || !is_iterable($metaContent)) {
            return [];
        }

        foreach ($metaContent as $key => $item) {
            $metadata = new Metadata();
            $metadata->type = $item['type'] ?? Metadata::TYPE_STRING;
            $metadata->description = $item['description'] ?? null;
            $metadata->required = $item['required'] ?? false;
            $metadata->default = $item['default'] ?? null;
            $metadata->example = $item['example'] ?? null;

            if (isset($item['make-absolute-path'])) {
                if ($item['make-absolute-path'] === 'true') {
                    $metadata->makeAbsolutePath = true;
                }
            }

            if (isset($item['options'])) {
                $metadata->options = explode(',', $item['options']);
            }

            $meta[$key] = $metadata;
        }

        return $meta;
    }

    /**
     * @param $value
     * @param string $key
     * @param Metadata $metadata
     * @return bool|string|null
     * @throws InvalidEnvException
     */
    public static function validateValue($value, string $key, Metadata $metadata)
    {
        if ($metadata->required && !$value) {
            throw new InvalidEnvException(sprintf('Env variable "%s" is required', $key));
        }

        if (!$value) {
            if ($metadata->default) {
                $value = $metadata->default;
            }
            return $value;
        }

        if ($metadata->type === Metadata::TYPE_INT && !is_numeric($value)) {
            throw new InvalidEnvException(sprintf('Env variable "%s" should be int', $key));
        }
        if ($metadata->type === Metadata::TYPE_URL && filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new InvalidEnvException(sprintf('Env variable "%s" is not valid url', $key));
        }
        if ($metadata->type === Metadata::TYPE_PATH) {
            if (!is_file($value) && !is_dir($value)) {
                throw new InvalidEnvException(sprintf('Env variable "%s" is not valid path', $key));
            }

            if ($metadata->makeAbsolutePath) {
                $value = realpath($value);
            }
        }
        if ($metadata->options && !in_array($value, $metadata->options)) {
            throw new InvalidEnvException(sprintf('Env variable "%s" is not included in options "%s"', $key, implode($metadata->options, ', ')));
        }

        return $value;
    }

    /**
     * @param $envVars
     * @param Metadata[] $meta
     * @return array
     * @throws InvalidEnvException
     */
    public static function validate($envVars, array $meta): array
    {
        foreach ($meta as $key => $metadata) {
            $value = $envVars[$key] ?? null;
            $value = self::validateValue($value, $key, $metadata);
            $envVars[$key]  = $value;
        }

        return $envVars;
    }

    public static function markdown($envMetaPath = self::DEFAULT_META_FILE_NAME, $markdownFile = self::DEFAULT_MARKDOWN_FILE): bool
    {
        $meta = self::metaFromYamlFile($envMetaPath);

        if (count($meta) === 0) {
            return false;
        }

        if (!is_file($markdownFile)) {
            throw new \InvalidArgumentException(sprintf('No markdown file %s', $markdownFile));
        }

        if (!is_readable($markdownFile)) {
            throw new \InvalidArgumentException(sprintf('Not reachable markdown file %s', $markdownFile));
        }

        if (!is_writable($markdownFile)) {
            throw new \InvalidArgumentException(sprintf('Not writable markdown file %s', $markdownFile));
        }

        $content = file_get_contents($markdownFile);

        $startTag = '<!-- envoi start -->';
        $endTag = '<!-- envoi end -->';

        $output = new BufferedOutput();
        $table = new Table($output);

        $table->setHeaders(['Variable', 'Type', 'Description', 'Example', 'Default', 'Required', 'Options']);

        foreach ($meta as $key => $metadata) {
            $table->addRow([
                $key,
                $metadata->type,
                $metadata->description,
                $metadata->example,
                $metadata->default,
                $metadata->required ? '*' : '',
                $metadata->options ? join(', ', $metadata->options): ''
            ]);
        }

        $table->render();

        $variablesContent = $output->fetch();
        $replaceCount = 0;
        $content = preg_replace("/$startTag\n(([a-zA-Z0-9_,:\-\+\`*\|\.\${}\s\n]*)?)$endTag/i", "$startTag\n```\n$variablesContent```\n$endTag", $content, 1, $replaceCount);

        $isUpdated = $replaceCount > 0;

        if ($isUpdated) {
            file_put_contents($markdownFile, $content);
        }

        return $isUpdated;
    }
}
