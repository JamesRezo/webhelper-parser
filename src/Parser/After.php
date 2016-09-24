<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebHelper\Parser\Parser;

/**
 * Methods that can be applied depending of the kind of server after a parsed configuration file turns into an array.
 *
 * @author James <james@rezo.net>
 */
class After
{
    /**
     * Trim all blank lines.
     *
     * @param array $activeConfig config file exploded in an array of lines
     *
     * @return array an array cleaned of blank lines
     */
    public static function deleteBlankLines(array $activeConfig = [])
    {
        $cleanedActiveConfig = [];

        foreach (array_map('trim', $activeConfig) as $line) {
            if ($line != '') {
                $cleanedActiveConfig[] = $line;
            }
        }

        return $cleanedActiveConfig;
    }

    /**
     * Reassembles discontinued simple directives in one line.
     *
     * In an Apache server context, it may be encountered.
     *
     * @param array $activeConfig config file exploded in an array of lines
     *
     * @return array an array with continuing lines gathered as one
     */
    public static function continuingDirectives(array $activeConfig = [])
    {
        $cleanedActiveConfig = [];

        //Continuing directives with "\" at the very end of a line are reassembled
        foreach ($activeConfig as $line) {
            if (!self::setLineIfPrevious($line)) {
                $cleanedActiveConfig[] = $line;
            }
        }

        return $cleanedActiveConfig;
    }

    /**
     * Helps to gather continuing lines as one.
     *
     * @param string &$line a line to add to previous lines or matching a contuining end line marker
     */
    private static function setLineIfPrevious(&$line)
    {
        static $previousLine = '';

        if ($previousLine) {
            $line = $previousLine.' '.trim($line);
            $previousLine = '';
        }

        if (preg_match('/(.+)\\\$/', $line, $container)) {
            $previousLine = $container[1];
        }

        return $previousLine;
    }
}
