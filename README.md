Envoi
=====
Environment variables on steroids

Library to make environment variables more powerful

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
  type: string
  example: "some example value"
  required: true
QUX:
  description: path to qux files
  type: path
  make-absolute-path: true  #  "Expands  relative paths to absolute paths (i.e. ~/qux becomes /home/joe/qux)
BAR:
  description: Used for bar things
  type: url
  default: "https://username:password@example.com/bla"
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


##### Run tests
    
    ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests