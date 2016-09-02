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
class Parser
{
    /** @var string configuration file */
    private $configFile = '';

    /** @var array active directives in an array */
    private $activeConfig = [];

    /**
     * Last pasring attempt error number.
     *
     * 0 all is OK
     * 1 configuration file is not readable
     * 2 no active configuration lines found
     *
     * @var integer
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
    public function getActiveConfig()
    {
        return $this->activeConfig;
    }

    /**
     * Getter for the last error number.
     *
     * @return integer error number of the last parsing attempt
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Comon parsing to both apache and nginx.
     *
     * @return bool true if active lines were found
     */
    protected function parseConfigFile()
    {
        $activeConfig = file_get_contents($this->configFile);

        //delete commented lines and end line comments
        $activeConfig = preg_replace('/^\s*#.*/m', '', $activeConfig);
        $activeConfig = preg_replace('/^([^#]+)#.*/m', '$1', $activeConfig);

        //convert into an array
        $activeConfig = explode("\n", $activeConfig);

        $this->activeConfig = $this->deleteBlankLines($activeConfig);
        return !empty($this->activeConfig);
    }

    /**
     * Trim all blank lines.
     *
     * @param  array  $activeConfig config file exploded in an array of lines
     * @return array                an array cleaned of blank lines
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
