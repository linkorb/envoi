<?php

namespace EnvoiTest;

use Envoi\Envoi;

/**
 * Class EnvoiTest
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class EnvoiTest extends \PHPUnit\Framework\TestCase
{
    protected static $readme = __DIR__.DIRECTORY_SEPARATOR.'TEST_README.md';

    public static function setUpBeforeClass(): void
    {
        Envoi::init(
            __DIR__. DIRECTORY_SEPARATOR.Envoi::DEFAULT_ENV_FILE_NAME,
            __DIR__. DIRECTORY_SEPARATOR.Envoi::DEFAULT_META_FILE_NAME
        );

        self::generateTestReadme();
    }

    private static function generateTestReadme()
    {
        if (!is_file(self::$readme)) {
            file_put_contents(self::$readme, <<<EOT
TEST
====

Available env variables:

<!-- envoi start -->
<!-- envoi end -->
EOT
            );
        }
    }


    public function testInit()
    {
        $this->assertEquals('localhost', getenv('TEST_DATABASE_HOST'));
        $this->assertEquals('80', getenv('TEST_DATABASE_PORT'));
        $this->assertEquals('vendor', getenv('TEST_CACHE_FOLDER'));
        $this->assertEquals('BLUE', getenv('TEST_QUUX'));
    }

    public function testMarkdown()
    {
        $this->assertTrue(Envoi::markdown(__DIR__. DIRECTORY_SEPARATOR.Envoi::DEFAULT_META_FILE_NAME, self::$readme));
        $this->assertTrue(Envoi::markdown(__DIR__. DIRECTORY_SEPARATOR.Envoi::DEFAULT_META_FILE_NAME, self::$readme));
    }
}
