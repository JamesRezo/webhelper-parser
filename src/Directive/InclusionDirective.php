<?php

/**
 * This file is part of WebHelper Parser.
 *
 * (c) James <james@rezo.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebHelper\Parser\Directive;

use Webmozart\Glob\Iterator\GlobIterator;

/**
 * Describes an inclusion directive instance.
 *
 * An inclusion directive points to a filesystem pathname.
 *
 * This pathname can be a file to load at the point of the active config, or
 * a list of files in a filesystem directory, using wildcards.
 *
 * Pathname can be absolute, or relative to a filesystem path defined by another Directive, or
 * to the path of the actual configuration file, or to the prefix.
 *
 * Technically, an inclusion directive is a simple directive that acts as a block directive.
 *
 * @author James <james@rezo.net>
 */
class InclusionDirective extends BlockDirective implements DirectiveInterface
{
    /** @var string the filesystem path where the web server is installed */
    private $prefix;

    /** @var array file list pointed with that directive */
    private $files = [];

    /**
     * Specific constructor for inclusion directives.
     *
     * @param string $name   the name of the key/context
     * @param string $value  the value of the key/context
     * @param string $prefix the filesystem path where the web server is installed
     */
    public function __construct($name, $value = '', $prefix = '')
    {
        parent::__construct($name, $value);
        $this->prefix = $prefix;
        $this->setFiles();
    }

    /**
     * Gets the detected files of the directive.
     *
     * @return array detected files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Detects and memoizes the included files.
     */
    public function setFiles()
    {
        $this->files = [];
        $path = $this->getValue();

        if (!preg_match('#^/#', $path)) {
            $path = $this->prefix.'/'.$path;
        }

        $iterator = new GlobIterator($path);
        foreach ($iterator as $path) {
            $this->files[] = $path;
        }

        return $this;
    }
}
