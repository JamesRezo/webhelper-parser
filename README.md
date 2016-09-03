# Web Server Configuration File Generic Parser
Part of WebHelper, a Generic Httpd Configuration Helper.

[![Build Status](https://travis-ci.org/JamesRezo/webhelper-parser.svg?branch=master)](https://travis-ci.org/JamesRezo/webhelper-parser)

## Installation

```composer require webhelper/parser```

## Usage

```php
use WebHelper\Parser\Parser;

$parser = new Parser();

$parser->setConfigFile($someConfigFile);

if (!$parser->getLastError()) {
    $activeConfig = $parser->getActiveConfig();
}
```

## Known issues

- Does not support old macos9 file format.

## License

MIT
