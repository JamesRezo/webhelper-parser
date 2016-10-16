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

use InvalidArgumentException;
use Webmozart\PathUtil\Path;
use League\Uri\Schemes\Http as HttpUri;

/**
 * Web Helper Universal Descriptor, a.k.a. WHUD.
 *
 * This is used to find what a server is actually meant to serve
 *
 * @author James <james@rezo.net>
 */
class Descriptor
{
    private $host;

    private $paths;

    /* TODO
     * private $proxies;
     * private $grants;
     */

    public function __construct($host = '', array $paths = array())
    {
        $this->host = $host;
        $this->paths = $paths;
    }

    public function getServedUrls($path)
    {
        $isServedAs = [];

        foreach ($this->paths as $exposedPath => $exposedDirectory) {
            if (preg_replace(',/$,', '', $path) == $exposedDirectory ||
                Path::isBasePath($exposedDirectory, Path::getDirectory($path))
            ) {
                $relative = Path::makeRelative($path, $exposedDirectory);
                $relative = $relative ? '/'.$relative : '';
                $isServedAs[] = $this->getHost().preg_replace(',/$,', '', $exposedPath).$relative;
            }
        }

        return $isServedAs;
    }

    public function getExposedPath($url)
    {
        $isExposedAs = '';
        list($host, $path) = $this->getUriHostAndPath($url);

        if ($host == $this->host) {
            foreach ($this->paths as $exposedPath => $exposedDirectory) {
                if (preg_replace(',/$,', '', $path) == $exposedPath ||
                    Path::isBasePath($exposedPath, Path::getDirectory($path))
                ) {
                    $relative = Path::makeRelative($path, $exposedPath);
                    $relative = $relative ? '/'.$relative : '';
                    $isExposedAs = $exposedDirectory.$relative;
                    break;
                }
            }
        }

        return $isExposedAs;
    }

    protected function getHost()
    {
        $host = $this->host;
        $scheme = 'http';

        if (preg_match('/:443$/', $this->host)) {
            $scheme = 'https';
            $host = preg_replace('/:443$/', '', $this->host);
        }

        return $scheme.'://'.$host;
    }

    private function getUriHostAndPath($url)
    {
        try {
            $uri = HttpUri::createFromString($url);
        } catch (InvalidArgumentException $e) {
            return ['', ''];
        }

        $host = $uri->getHost();
        $port = $uri->getPort();
        if (!$port && $uri->getScheme() == 'https') {
            $port = 443;
        }
        if ($port) {
            $host .= ':'.strval($port);
        }
        $path = $uri->getPath();
        if (!$path) {
            $path = '/';
        }        

        return [$host, $path];
    }
}
