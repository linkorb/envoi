<?php

require dirname(__DIR__).'/vendor/autoload.php';

\Envoi\Envoi::init();

echo getenv('DATABASE_HOST').PHP_EOL;
echo getenv('DATABASE_PORT').PHP_EOL;