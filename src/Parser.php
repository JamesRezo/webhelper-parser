<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser;

use WebHelper\Parser\Server\ServerInterface;
use WebHelper\Parser\Exception\ParserException;
use WebHelper\Parser\Exception\InvalidConfigException;

/**
 * Web server configuration generic parser.
 *
 * @author James <james@rezo.net>
 */
abstract class Parser implements ParserInterface
{
    /** @var Server\ServerInterface a server instance */
    private $server;

    /** @var string configuration file */
    private $configFile = '';

    /** @var array active directives in an array */
    protected $activeConfig = [];

    /**
     * Last pasring attempt error number.
     *
     * 0 all is OK
     * 1 configuration file is not readable
     * 2 no active configuration lines found
     *
     * @var int
     */
    private $lastError = 0;

    /**
     * Setter for the server instance.
     *
     * @see Server\ServerInterface Server Documentation
     *
     * @param Server\ServerInterface $server the server instance
     */
    public function setServer(ServerInterface $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Setter for the config file to parse.
     *
     * @param string $configFile configuration file
     *
     * @throws Exception\ParserException        if configuration file is not readable
     * @throws Exception\InvalidConfigException if active configuration is empty
     */
    public function setConfigFile($configFile = '')
    {
        if (!is_readable($configFile)) {
            $this->lastError = 1;
            $this->activeConfig = [];

            throw ParserException::forFileUnreadable($configFile);
        }

        $this->configFile = $configFile;
        if (!$this->parseConfigFile()) {
            $this->lastError = 2;

            throw InvalidConfigException::forEmptyConfig($configFile);
        }

        return $this;
    }

     /**
      * Getter for the server instance.
      *
      * @see Server\ServerInterface Server Documentation
      *
      * @param Server\ServerInterface the server instance
      */
     public function getServer()
     {
         return $this->server;
     }

    /**
     * Getter for the active config main context.
     *
     * @return Directive\DirectiveInterface the active config
     */
    abstract public function getActiveConfig();

    /**
     * Getter for the last error number.
     *
     * @return int error number of the last parsing attempt
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Getter for the content of the configuration file.
     *
     * @return string content of the configuration file
     */
    public function getOriginalConfig()
    {
        return file_get_contents($this->configFile);
    }

    /**
     * Does some extra parsing before the active config turns into an array.
     *
     * @param string $config a config file content
     *
     * @return string a config file content
     */
    protected function beforeExplode($config)
    {
        $config = $this->deleteComments($config);

        return $config;
    }

    /**
     * Does some extra parsing after the active config has turned into an array.
     *
     * @param array $activeConfig an active config
     *
     * @return array an active config
     */
    protected function afterExplode(array $activeConfig)
    {
        $activeConfig = $this->deleteBlankLines($activeConfig);

        return $activeConfig;
    }

    /**
     * Comon parsing to both apache and nginx.
     *
     * @return bool true if active lines were found
     */
    private function parseConfigFile()
    {
        $activeConfig = $this->getOriginalConfig();
        $activeConfig = $this->beforeExplode($activeConfig);

        //convert into an array
        $activeConfig = explode("\n", $activeConfig);

        $this->activeConfig = $this->afterExplode($activeConfig);

        return !empty($this->activeConfig);
    }

    /**
     * Deletes commented lines and end line comments.
     *
     * @param string $config a file content
     *
     * @return string a file content without comments
     */
    private function deleteComments($config = '')
    {
        $config = preg_replace('/^\s*#.*/m', '', $config);
        $config = preg_replace('/^([^#]+)#.*/m', '$1', $config);

        return $config;
    }

    /**
     * Trim all blank lines.
     *
     * @param array $activeConfig config file exploded in an array of lines
     *
     * @return array an array cleaned of blank lines
     */
    private function deleteBlankLines(array $activeConfig = array())
    {
        $cleanedActiveConfig = [];

        foreach (array_map('trim', $activeConfig) as $line) {
            if ($line != '') {
                $cleanedActiveConfig[] = $line;
            }
        }

        return $cleanedActiveConfig;
    }
}
