Envoi
=====

Envoi aims to ease the use and documentation of environment variables (env
vars) in PHP applications.

Envoi features:

- a Yaml schema to describe the env vars that may be used to configure an
  application

- tools to validate env vars against a schema

- a tool to assist in the population of a `.env` file

- a tool which converts a schema to markdown

### Install

    composer require linkorb/envoi

### Use

#### Interpolation

Assign one variable based on another in `.env` file

```
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

    ./vendor/bin/envoi
    
Available commands:

`validate`   Validate based on meta file `.env.yaml`.<br/>
`configure`  CLI wizard to ask + update .env file based on `.env.yaml`.<br/>
`markdown`   Output a GitHub Flavored Markdown documentation for the available variables.
Look for a `<!-- envoi start -->` and `<!-- envoi end -->` tags in file (default to README.md), and insert/update the generated markdown between those tags.


### Run tests
    
    ./vendor/bin/phpunit
