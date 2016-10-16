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
use League\Uri\Schemes\Http as HttpUri;
use Webmozart\PathUtil\Path;

/**
 * Web Helper Universal Descriptor, a.k.a. WHUD.
 *
 * This is used to find what a server is actually meant to serve
 *
 * @author James <james@rezo.net>
 */
class Descriptor
{
    /** @var string the server name */
    private $host;

    /** @var array a list of paths that exposes filesystem directories in the host context */
    private $paths;

    /**
     * Base constructor.
     *
     * @param string $host  The host
     * @param array  $paths The paths
     */
    public function __construct($host = '', array $paths = [])
    {
        $this->host = $host;
        $this->paths = $paths;
    }

    /**
     * Gets the served urls.
     *
     * @param string $path The path
     *
     * @return array The served urls
     */
    public function getServedUrls($path)
    {
        $isServedAs = [];

        foreach ($this->paths as $exposedPath => $exposedDirectory) {
            $relative = $this->getRelative($path, $exposedDirectory);
            if (!is_null($relative)) {
                $isServedAs[] = $this->getHost().preg_replace(',/$,', '', $exposedPath).$relative;
            }
        }

        return $isServedAs;
    }

    /**
     * Gets the exposed path.
     *
     * @param string $url The url
     *
     * @return string The exposed path
     */
    public function getExposedPath($url)
    {
        $isExposedAs = '';
        list($host, $path) = $this->getUriHostAndPath($url);

        if ($host == $this->host) {
            foreach ($this->paths as $exposedPath => $exposedDirectory) {
                $relative = $this->getRelative($path, $exposedPath);
                if (!is_null($relative)) {
                    $isExposedAs = $exposedDirectory.$relative;
                    break;
                }
            }
        }

        return $isExposedAs;
    }

    /**
     * Gets the relative path if it matches against another path.
     *
     * @param string $path    The path
     * @param string $against The path to match against
     *
     * @return null|string The relative path
     */
    private function getRelative($path, $against)
    {
        $relative = null;

        if (preg_replace(',/$,', '', $path) == $against ||
            Path::isBasePath($against, Path::getDirectory($path))
        ) {
            $relative = Path::makeRelative($path, $against);
            $relative = $relative ? '/'.$relative : '';
        }

        return $relative;
    }

    /**
     * Gets the host.
     *
     * @return string The starting uri based on the host
     */
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

    /**
     * Gets the uri host and path.
     *
     * @param string $url The url
     *
     * @return array The uri host and path
     */
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
