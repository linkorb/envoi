### Use

#### Env Checkers

A checker should be invoked as early as possible in the life-cycle of an
application.  The ideal time is immediately after the environment has been
populated with env vars.  For example, in a Symfony-based app, the checker
should be invoked right after the Dotenv component has loaded the env vars from
the various `.env*` files:

```php
<?php

// config/bootstrap.php

use Envoi\EnvChecker;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');
// check the env!
(new EnvChecker())->check(dirname(__DIR__).'/.env.yaml');
```

The checker will throw an exception to halt the application when invalid env
vars are found.  The list of validation errors is included in the exception
message.

`EnvChecker` treats the environment as immutable: it validates env vars, but
does not modify them.  `MutableEnvChecker` validates env vars and can also
transform values, making it the ideal checker when you want to take advantage
of the various env var transformation features of Envoi.

#### Interpolation

Assign one variable based on another in `.env` file

```shell
FOO="foo"
BAR="{FOO}/logs"
```

Metadata environment example `.env.yaml`
Supports types: `int`, `string`, `url`, `path`

```yaml
FOO:
  description: Used to configure foo system
  type: url
  default: "https://username:password@example.com/bla"
  required: true
QUX:
  description: path to qux files
  type: path
  example: "some example value"
  make-absolute-path: true  #  "Expands  relative paths to absolute paths (i.e. ~/qux becomes /home/joe/qux)
BAR:
  description: Used for bar things
  type: string
  options: RED,GREEN,BLUE # validates that input is one of the available options
```

Init environment variables from `.env`

```php
Envoi::init();
$foo = getenv('FOO');
```

#### CLI

```shell
./vendor/bin/envoi
```

Available commands:

`validate`   Validate based on meta file `.env.yaml`.<br/>
`configure`  CLI wizard to ask + update .env file based on `.env.yaml`.<br/>
`markdown`   Output a GitHub Flavored Markdown documentation for the available variables.
Look for a `<!-- envoi start -->` and `<!-- envoi end -->` tags in file (default to README.md), and insert/update the generated markdown between those tags.


### Run tests

```shell
./vendor/bin/phpunit
```

