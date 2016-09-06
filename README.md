# Web Server Configuration File Generic Parser
Part of [WebHelper](http://github.com/JamesRezo/WebHelper), a Generic Httpd Configuration Helper.

[![Build Status](https://travis-ci.org/JamesRezo/webhelper-parser.svg?branch=master)](https://travis-ci.org/JamesRezo/webhelper-parser)

## Installation

```composer require webhelper/parser```

## Basic Usage

```php
use WebHelper\Parser\ApacheParser;
use WebHelper\Parser\ParserException;
use WebHelper\Parser\InvalidConfigException;

$apache = new ApacheParser();

try {
    $activeConfig = $parser
        ->setConfigFile('/private/etc/apache2/httpd.conf')
        ->getActiveConfig();

    echo $parser->getOriginalConfig();

    echo var_export($activeConfig, true).PHP_EOL;
} catch (ParserException $e) {
    //file not found
    var_dump($e->getMessage());
} catch (InvalidConfigException $e) {
    //empty config or syntax error
    var_dump($e->getMessage());
}
```

## Advanced Usage

Create a concrete class extending Parser :
```php
use WebHelper\Parser\Parser;

class MyParser extends Parser
{
    //optionally
    protected function beforeExplode($config)
    {
        // ... code ...
        return $config;
    }

    //optionally
    protected function afterExplode($config)
    {
        // ... code ...
        return $config;
    }

    //mandatory
    public function getActiveConfig()
    {
        // ... code ...
        $this->activeConfig = [ ... ];
        // ... code ...

        return $this->activeConfig;
    }
}
```

Use it :
```php
use MyParser;

$parser = new MyParser();

$parser->setConfigFile($someConfigFile);

if (!$parser->getLastError()) {
    $activeConfig = $parser->getActiveConfig();
}
```

## Known issues

- Does not support old macos9 file format.

## License

MIT
