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

/**
 * Web server configuration generic parser.
 *
 * @author James <james@rezo.net>
 */
abstract class Parser implements ParserInterface
{
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
     * Setter for the config file to parse.
     *
     * @param string $configFile configuration file
     */
    public function setConfigFile($configFile = '')
    {
        if (!is_readable($configFile)) {
            $this->lastError = 1;
            $this->activeConfig = [];

            return $this;
        }

        $this->configFile = $configFile;
        if (!$this->parseConfigFile()) {
            $this->lastError = 2;
        }

        return $this;
    }

    /**
     * Getter for the active config array.
     *
     * @return array active config
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
     * Does some extra parsing before the active configs turns into an array.
     *
     * @param string $config a config file content
     *
     * @return string a config file content
     */
    protected function beforeExplode($config)
    {
        return $config;
    }

    /**
     * Comon parsing to both apache and nginx.
     *
     * @return bool true if active lines were found
     */
    protected function parseConfigFile()
    {
        $activeConfig = $this->getOriginalConfig();

        //delete commented lines and end line comments
        $activeConfig = preg_replace('/^\s*#.*/m', '', $activeConfig);
        $activeConfig = preg_replace('/^([^#]+)#.*/m', '$1', $activeConfig);

        //convert into an array
        $activeConfig = $this->beforeExplode($activeConfig);
        $activeConfig = explode("\n", $activeConfig);

        $this->activeConfig = $this->deleteBlankLines($activeConfig);

        return !empty($this->activeConfig);
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
