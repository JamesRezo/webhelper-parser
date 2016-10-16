<?php

namespace WebHelper\Test\Parser;

use PHPUnit_Framework_TestCase;
use WebHelper\Parser\Parser\Descriptor;

class DescriptorTest extends PHPUnit_Framework_TestCase
{
    public function dataGetServedUrls()
    {
        $data = [];

        $data['basic localhost'] = [
            ['http://localhost'],
            '/var/www',
            ['localhost', [
                '/~user' => '/home/user/public_html',
                '/myapp' => '/opt/apps/myapp/web',
                '/'      => '/var/www',
            ]],
        ];

        $data['alias location 1'] = [
            ['http://localhost/myapp/app.php'],
            '/opt/apps/myapp/web/app.php',
            ['localhost', [
                '/~user' => '/home/user/public_html',
                '/myapp' => '/opt/apps/myapp/web',
                '/'      => '/var/www',
            ]],
        ];

        $data['alias location 2'] = [
            ['http://localhost/myapp/app.php', 'http://localhost/myaliasedapp/app.php'],
            '/opt/apps/myapp/web/app.php',
            ['localhost', [
                '/~user'        => '/home/user/public_html',
                '/myapp'        => '/opt/apps/myapp/web',
                '/myaliasedapp' => '/opt/apps/myapp/web',
                '/'             => '/var/www',
            ]],
        ];

        $data['https'] = [
            ['https://example.com'],
            '/var/www',
            ['example.com:443', [
                '/images'     => '/var/secured/icons',
                '/images/fun' => '/var/secured_but_fun/icons',
                '/'           => '/var/www',
            ]],
        ];

        $data['https'] = [
            ['https://example.com/images/fun/cat.gif'],
            '/var/secured_but_fun/icons/cat.gif',
            ['example.com:443', [
                '/images'     => '/var/secured/icons',
                '/images/fun' => '/var/secured_but_fun/icons',
                '/'           => '/var/www',
            ]],
        ];

        return $data;
    }

    /**
     * @dataProvider dataGetServedUrls
     */
    public function testGetServedUrls($expected, $path, $descriptor)
    {
        $descriptor = new Descriptor($descriptor[0], $descriptor[1]);

        $this->assertEquals($expected, $descriptor->getServedUrls($path));
    }

    public function dataGetExposedPath()
    {
        $data = [];

        $data['not an URI'] = [
            '',
            'localhost:80',
            ['localhost', [
                '/~user' => '/home/user/public_html',
                '/myapp' => '/opt/apps/myapp/web',
                '/'      => '/var/www',
            ]],
        ];

        $data['basic localhost'] = [
            '/var/www',
            'http://localhost',
            ['localhost', [
                '/~user' => '/home/user/public_html',
                '/myapp' => '/opt/apps/myapp/web',
                '/'      => '/var/www',
            ]],
        ];

        $data['localhost on random port'] = [
            '/var/icons/alert.png',
            'http://localhost:8080/images/alert.png',
            ['localhost:8080', [
                '/images/fun' => '/var/funny',
                '/images'     => '/var/icons',
                '/~user'      => '/home/user/public_html',
                '/'           => '/var/www',
            ]],
        ];

        $data['localhost on random port 2'] = [
            '/var/funny/lol.jpg',
            'http://localhost:8080/images/fun/lol.jpg',
            ['localhost:8080', [
                '/images/fun' => '/var/funny',
                '/images'     => '/var/icons',
                '/~user'      => '/home/user/public_html',
                '/'           => '/var/www',
            ]],
        ];

        $data['https'] = [
            '/var/secured/icons/fun/ninja.png',
            'https://example.com/images/fun/ninja.png',
            ['example.com:443', [
                '/images'     => '/var/secured/icons',
                '/images/fun' => '/var/secured_but_fun/icons',
                '/'           => '/var/www',
            ]],
        ];

        return $data;
    }

    /**
     * @dataProvider dataGetExposedPath
     */
    public function testGetExposedPath($expected, $url, $descriptor)
    {
        $descriptor = new Descriptor($descriptor[0], $descriptor[1]);

        $this->assertEquals($expected, $descriptor->getExposedPath($url));
    }
}
