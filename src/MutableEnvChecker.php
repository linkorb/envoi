<?php

namespace Envoi;

/**
 * MutableEnvChecker checks the validity of environment variables and is able to
 * modify the values according to their metadata.
 */
class MutableEnvChecker extends EnvChecker
{
    /**
     * Check and adjust the environment and complain loudly if invalid.
     *
     * @param string $path of the Yaml env file
     *
     * @throws \Envoi\InvalidEnvException
     */
    public function check(string $path): void
    {
        $errors = $replacements = [];

        $meta = $this->loadMeta($path);

        // check that required vars are set and that set vars are valid
        foreach ($meta as $varname => $metadata) {
            try {
                $replacementValue = $this->validate($varname, $_ENV[$varname], $metadata);
                if ($_ENV[$varname] !== $replacementValue) {
                    $replacements[$varname] = $replacementValue;
                }
            } catch (InvalidEnvException $e) {
                $errors[] = $e->getMessage();
            }
        }

        foreach ($this->checkUndocumented($meta) as $varname => $_) {
            $errors[] = "{$varname} is undocumented";
        }

        $this->complain($errors);

        foreach ($replacements as $varname => $value) {
            $_ENV[$varname] = $value;
        }
    }
}
