# Web Server Configuration File Generic Parser
Part of [WebHelper](http://github.com/JamesRezo/WebHelper), a Generic Httpd Configuration Helper.

[![Build Status](https://travis-ci.org/JamesRezo/webhelper-parser.svg?branch=master)](https://travis-ci.org/JamesRezo/webhelper-parser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JamesRezo/webhelper-parser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JamesRezo/webhelper-parser/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JamesRezo/webhelper-parser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JamesRezo/webhelper-parser/?branch=master)
[![Code Climate](https://codeclimate.com/github/JamesRezo/webhelper-parser/badges/gpa.svg)](https://codeclimate.com/github/JamesRezo/webhelper-parser)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2ef11e52-9098-4c72-a0c2-c83996e9bf62/mini.png)](https://insight.sensiolabs.com/projects/2ef11e52-9098-4c72-a0c2-c83996e9bf62)
[![Dependency Status](https://www.versioneye.com/user/projects/57d100ee87b0f6002e27f9e9/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57d100ee87b0f6002e27f9e9)
[![Latest Stable Version](https://poser.pugx.org/webhelper/parser/v/stable)](https://packagist.org/packages/webhelper/parser)
[![License](https://poser.pugx.org/webhelper/parser/license)](https://packagist.org/packages/webhelper/parser)
[![StyleCI](https://styleci.io/repos/67290969/shield?branch=master)](https://styleci.io/repos/67290969)

## Installation

```composer require webhelper/parser```

## Basic Usage

Parse an Apache configuration file:
```php
use WebHelper\Parser\Factory;
use WebHelper\Parser\ParserException;
use WebHelper\Parser\InvalidConfigException;

$factory = new Factory()
$parser = $factory->createParser('apache');
$parser->getServer()->setPrefix('/usr');

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
use WebHelper\Parser\Factory;

$factory = new Factory()
$parser = $factory->createParser('nginx');
$parser->getServer()->setPrefix('/usr/sbin/');

$activeConfig = $parser
        ->setConfigFile('/etc/nginx/nginx.conf')
        ->getActiveConfig();

// etc...
```

## Known issues

- Does not support old macos9 file format.
