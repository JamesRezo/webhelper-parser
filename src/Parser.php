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
use WebHelper\Parser\Parser\Before;
use WebHelper\Parser\Parser\After;

/**
 * Web server configuration generic parser.
 *
 * @author James <james@rezo.net>
 */
class Parser implements ParserInterface
{
    /** @var Server\ServerInterface a server instance */
    private $server;

    /** @var Compiler a Compiler instance */
    private $compiler;

    /** @var string configuration file */
    private $configFile = '';

    /** @var array active directives in an array */
    protected $activeConfig = [];

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
     * Setter for the compiler instance.
     *
     * @param Compiler $compiler the compiler instance
     */
    public function setCompiler(Compiler $compiler)
    {
        $this->compiler = $compiler;
        $this->compiler->setParser($this);

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
            $this->activeConfig = [];

            throw ParserException::forFileUnreadable($configFile);
        }

        $this->configFile = $configFile;
        if (!$this->parseConfigFile()) {
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
    public function getActiveConfig()
    {
        return $this->compiler->doCompile($this->activeConfig);
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
        foreach ($this->server->getBeforeMethods() as $beforeMethod) {
            $config = Before::$beforeMethod($config);
        }

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
        foreach ($this->server->getAfterMethods() as $afterMethod) {
            $activeConfig = After::$afterMethod($activeConfig);
        }

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
}
