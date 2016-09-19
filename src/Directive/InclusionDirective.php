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
use WebHelper\Parser\Parser;
use WebHelper\Parser\Server\ServerInterface;

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
    /** @var \WebHelper\Parser\Parser the parser instance */
    private $parser;

    /** @var array file list pointed with that directive */
    private $files = [];

    /**
     * Specific constructor for inclusion directives.
     *
     * @param string                   $name   the name of the key/context
     * @param string                   $value  the value of the key/context
     * @param \WebHelper\Parser\Parser $parser the parser instance
     */
    public function __construct($name, $value, Parser $parser)
    {
        parent::__construct($name, $value);
        $this->parser = $parser;
        $this->setFiles();
        $this->compileFiles();
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
            $path = $this->parser->getServer()->getPrefix().'/'.$path;
        }

        $iterator = new GlobIterator($path);
        foreach ($iterator as $path) {
            $this->files[] = $path;
        }

        return $this;
    }

    /**
     * Fills the block directives by compiling the memoized files.
     */
    public function compileFiles()
    {
        foreach ($this->files as $file) {
            $activeConfig = $this->parser->setConfigFile($file)->getActiveConfig();
            $this->add($activeConfig);
        }

        return $this;
    }

    /**
     * Dumps the directive respecting a server syntax.
     *
     * @param ServerInterface $server a server instance
     * @param int             $spaces the indentation spaces
     *
     * @return string the dumped directive
     */
    public function dump(ServerInterface $server, $spaces = 0)
    {
        $value = $this->getValue() ? ' '.$this->getValue() : '';

        return str_repeat(' ', $spaces).sprintf(
            $server->getDumperSimpleDirective(),
            $this->getName(),
            $value
        ).PHP_EOL;
    }
}
