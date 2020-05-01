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
                $this->validate($varname, $_ENV[$varname] ?? '', $metadata);
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
        if (!class_exists('\Symfony\Component\Dotenv\Dotenv')
            || !isset($_ENV['SYMFONY_DOTENV_VARS'])
        ) {
            return [];
        }

        return array_diff_key(
            array_flip(
                explode(',', $_ENV['SYMFONY_DOTENV_VARS'])
            ),
            $meta
        );
    }

    protected function complain(array $errors): void
    {
        if (0 === sizeof($errors)) {
            return;
        }

        throw new InvalidEnvException(implode('; ', $errors));
    }
}
