<!-- Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten. -->
envoi
============

Envoi aims to ease the use and documentation of environment variables (env
vars) in PHP applications.

Envoi features:

- a Yaml schema to describe the env vars that may be used to configure an
  application

- tools to validate env vars against a schema

- a tool to assist in the population of a `.env` file

- a tool which converts a schema to markdown

Envoi sports a console command which validates a `.env` file against a schema
(by convention, `.env.yaml`).  It also provides checkers that, when invoked
early in the start-up phase of an application, will halt an application which
doesn't have a complete and valid set of env vars.



### Install

```shell
composer require linkorb/envoi
```


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

## Contributing

We welcome contributions to make this repository even better. Whether it's fixing a bug, adding a feature, or improving documentation, your help is highly appreciated. To get started, fork this repository then clone your fork.

Be sure to familiarize yourself with LinkORB's [Contribution Guidelines](/CONTRIBUTING.md) for our standards around commits, branches, and pull requests, as well as our [code of conduct](/.github/CODE_OF_CONDUCT.md) before submitting any changes.

If you are unable to implement changes you like yourself, don't hesitate to open a new issue report so that we or others may take care of it.
## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).
By the way, we're hiring!
