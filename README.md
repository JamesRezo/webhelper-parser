# Web Server Configuration File Generic Parser
Part of [WebHelper](http://github.com/JamesRezo/WebHelper), a Generic Httpd Configuration Helper.

[![Build Status](https://travis-ci.org/JamesRezo/webhelper-parser.svg?branch=master)](https://travis-ci.org/JamesRezo/webhelper-parser)
[![Code Climate](https://codeclimate.com/github/JamesRezo/webhelper-parser/badges/gpa.svg)](https://codeclimate.com/github/JamesRezo/webhelper-parser)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2ef11e52-9098-4c72-a0c2-c83996e9bf62/mini.png)](https://insight.sensiolabs.com/projects/2ef11e52-9098-4c72-a0c2-c83996e9bf62)

## Installation

```composer require webhelper/parser```

## Basic Usage

Parse an Apache configuration file:
```php
use WebHelper\Parser\ApacheParser;
use WebHelper\Parser\ParserException;
use WebHelper\Parser\InvalidConfigException;

$parser = new ApacheParser();

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

Or the same with Nginx
```php
use WebHelper\Parser\NginxParser;
use WebHelper\Parser\ParserException;
use WebHelper\Parser\InvalidConfigException;

$parser = new NginxParser();

$activeConfig = $parser
        ->setConfigFile('/etc/nginx/nginx.conf')
        ->getActiveConfig();

// etc...
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

Use the compiler to parse specific configuration items :
```php
use WebHelper\Parser\Parser;

class MyParser extends Parser
{
    public function getActiveConfig()
    {
        $compiler = new Compiler(
            //a Regexp that matches the begining of a multiline syntax
            $mutilineStarter,
            //a Regexp that matches the end of a multiline syntax
            $multilineEnder,
            //a Regexp that matches a simple line syntax
            $simplelineChecker
        );
        return $compiler->doCompile($this->activeConfig);
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
