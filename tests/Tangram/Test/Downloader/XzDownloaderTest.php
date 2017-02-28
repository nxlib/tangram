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

namespace Tangram\Test\Downloader;

use Tangram\Downloader\XzDownloader;
use Tangram\TestCase;
use Tangram\Util\Filesystem;
use Tangram\Util\Platform;
use Tangram\Util\RemoteFilesystem;

class XzDownloaderTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $testDir;

    public function setUp()
    {
        if (Platform::isWindows()) {
            $this->markTestSkipped('Skip test on Windows');
        }
        $this->testDir = $this->getUniqueTmpDirectory();
    }

    public function tearDown()
    {
        $this->fs = new Filesystem;
        $this->fs->removeDirectory($this->testDir);
    }

    public function testErrorMessages()
    {
        $packageMock = $this->getMock('Tangram\Package\PackageInterface');
        $packageMock->expects($this->any())
            ->method('getDistUrl')
            ->will($this->returnValue($distUrl = 'file://'.__FILE__))
        ;
        $packageMock->expects($this->any())
            ->method('getDistUrls')
            ->will($this->returnValue(array($distUrl)))
        ;
        $packageMock->expects($this->atLeastOnce())
            ->method('getTransportOptions')
            ->will($this->returnValue(array()))
        ;

        $io = $this->getMock('Tangram\IO\IOInterface');
        $config = $this->getMock('Tangram\Config');
        $config->expects($this->any())
            ->method('get')
            ->with('vendor-dir')
            ->will($this->returnValue($this->testDir));
        $downloader = new XzDownloader($io, $config, null, null, null, new RemoteFilesystem($io));

        try {
            $downloader->download($packageMock, $this->getUniqueTmpDirectory());
            $this->fail('Download of invalid tarball should throw an exception');
        } catch (\RuntimeException $e) {
            $this->assertRegexp('/(File format not recognized|Unrecognized archive format)/i', $e->getMessage());
        }
    }
}
