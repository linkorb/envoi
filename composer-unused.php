<?php

// Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\PatternFilter;

return static function (Configuration $config): Configuration {
    return $config
        // phpqa.composer_unused.default_filtered_packages
        ->addPatternFilter(PatternFilter::fromString('/^ext-.*/'))
    ;
};
