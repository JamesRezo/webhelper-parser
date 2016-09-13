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
class Before
{
    /**
     * Deletes commented lines and end line comments.
     *
     * @param string $config a file content
     *
     * @return string a file content without comments
     */
    public static function deleteComments($config = '')
    {
        $config = preg_replace('/^\s*#.*/m', '', $config);
        $config = preg_replace('/^([^#]+)#.*/m', '$1', $config);

        return $config;
    }

    /**
     * Sets starting and endind directives in a line.
     *
     * For convinience, in nginx configuration context, braces are placed one per line
     *
     * @param string $config a file content
     *
     * @return string a file content without comments
     */
    public static function bracesPlacedOnePerLine($config = '')
    {
        $config = preg_replace('/\{\n?/m', "{\n", $config);
        $config = preg_replace('/\n?\}/m', "\n}", $config);

        return $config;
    }
}
