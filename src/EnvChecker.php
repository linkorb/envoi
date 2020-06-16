<?php

namespace Envoi;

/**
 * EnvChecker checks the validity of environment variables.
 */
class EnvChecker
{
    /**
     * Check the environment and complain loudly if invalid.
     *
     * @param string $path of the Yaml env file
     *
     * @throws \Envoi\InvalidEnvException
     */
    public function check(string $path): void
    {
        $errors = [];

        $meta = $this->loadMeta($path);

        // check that required vars are set and that set vars are valid
        foreach ($meta as $varname => $metadata) {
            try {
                $this->validate($varname, ($_SERVER[$varname] ?? ($_ENV[$varname] ?? '')), $metadata);
            } catch (InvalidEnvException $e) {
                $errors[] = $e->getMessage();
            }
        }

        foreach ($this->checkUndocumented($meta) as $varname => $_) {
            $errors[] = "{$varname} is undocumented";
        }

        $this->complain($errors);
    }

    protected function loadMeta($path): array
    {
        return Envoi::metaFromYamlFile($path);
    }

    protected function validate($key, $value, $metadata)
    {
        return Envoi::validateValue($value, $key, $metadata);
    }

    protected function checkUndocumented(array $meta): array
    {
        if (!class_exists('\Symfony\Component\Dotenv\Dotenv')) {
            return [];
        }

        $undocumented = [];

        if (isset($_SERVER['SYMFONY_DOTENV_VARS'])) {
            $undocumented += array_diff_key(
                array_flip(
                    explode(',', $_SERVER['SYMFONY_DOTENV_VARS'])
                ),
                $meta
            );
        }

        if (isset($_ENV['SYMFONY_DOTENV_VARS'])) {
            $undocumented += array_diff_key(
                array_flip(
                    explode(',', $_ENV['SYMFONY_DOTENV_VARS'])
                ),
                $meta
            );
        }

        return $undocumented;
    }

    protected function complain(array $errors): void
    {
        if (0 === sizeof($errors)) {
            return;
        }

        $this->registerExceptionHandler();

        throw new InvalidEnvException(implode('; ', $errors));
    }

    protected function registerExceptionHandler()
    {
        if ('cli' === php_sapi_name() || ini_get('display_errors')) {
            return;
        }

        $originalExceptionHandler = set_exception_handler(
            static function (\Throwable $e) {
                if (!$e instanceof InvalidEnvException) {
                    return;
                }

                ob_start();
                echo '<html><body><h1>Envoi validation failed</h1></body></html>';
                ob_end_flush();

                // this seems to be what the default handler does
                $exceptionName = get_class($e);
                trigger_error("Uncaught {$exceptionName}: {$e->getMessage()}", E_USER_ERROR);
            }
        );

        if (null !== $originalExceptionHandler) {
            restore_exception_handler();
        }
    }
}
