<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tangram\Test\Repository\Vcs;

use Tangram\Repository\Vcs\FossilDriver;
use Tangram\Config;
use Tangram\TestCase;
use Tangram\Util\Filesystem;
use Tangram\Util\Platform;

class FossilDriverTest extends TestCase
{
    protected $home;
    protected $config;

    public function setUp()
    {
        $this->home = $this->getUniqueTmpDirectory();
        $this->config = new Config();
        $this->config->merge(array(
            'config' => array(
                'home' => $this->home,
            ),
        ));
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->removeDirectory($this->home);
    }

    private function getCmd($cmd)
    {
        if (Platform::isWindows()) {
            return strtr($cmd, "'", '"');
        }

        return $cmd;
    }

    public static function supportProvider()
    {
        return array(
            array('http://fossil.kd2.org/kd2fw/', true),
            array('https://chiselapp.com/user/rkeene/repository/flint/index', true),
            array('ssh://fossil.kd2.org/kd2fw.fossil', true),
        );
    }

    /**
     * @dataProvider supportProvider
     */
    public function testSupport($url, $assertion)
    {
        $config = new Config();
        $result = FossilDriver::supports($this->getMock('Tangram\IO\IOInterface'), $config, $url);
        $this->assertEquals($assertion, $result);
    }
}
