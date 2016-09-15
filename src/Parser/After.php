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
    public static function deleteBlankLines(array $activeConfig = array())
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
     * @return array an array cleaned of blank lines
     */
    public static function continuingDirectives(array $activeConfig = array())
    {
        $cleanedActiveConfig = [];

        //Continuing directives with "\" at the very end of a line are reassembled
        $previousLine = '';
        foreach ($activeConfig as $line) {
            if ($previousLine) {
                $line = $previousLine.' '.trim($line);
                $previousLine = '';
            }

            if (preg_match('/(.+)\\\$/', $line, $container)) {
                $previousLine = $container[1];
            }

            if (!$previousLine) {
                $cleanedActiveConfig[] = $line;
            }
        }

        return $cleanedActiveConfig;
    }
}
