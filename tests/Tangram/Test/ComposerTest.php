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

namespace Tangram\Test;

use Tangram\Composer;
use Tangram\TestCase;

class ComposerTest extends TestCase
{
    public function testSetGetPackage()
    {
        $composer = new Composer();
        $package = $this->getMock('Tangram\Package\RootPackageInterface');
        $composer->setPackage($package);

        $this->assertSame($package, $composer->getPackage());
    }

    public function testSetGetLocker()
    {
        $composer = new Composer();
        $locker = $this->getMockBuilder('Tangram\Package\Locker')->disableOriginalConstructor()->getMock();
        $composer->setLocker($locker);

        $this->assertSame($locker, $composer->getLocker());
    }

    public function testSetGetRepositoryManager()
    {
        $composer = new Composer();
        $manager = $this->getMockBuilder('Tangram\Repository\RepositoryManager')->disableOriginalConstructor()->getMock();
        $composer->setRepositoryManager($manager);

        $this->assertSame($manager, $composer->getRepositoryManager());
    }

    public function testSetGetDownloadManager()
    {
        $composer = new Composer();
        $io = $this->getMock('Tangram\IO\IOInterface');
        $manager = $this->getMock('Tangram\Downloader\DownloadManager', array(), array($io));
        $composer->setDownloadManager($manager);

        $this->assertSame($manager, $composer->getDownloadManager());
    }

    public function testSetGetInstallationManager()
    {
        $composer = new Composer();
        $manager = $this->getMock('Tangram\Installer\InstallationManager');
        $composer->setInstallationManager($manager);

        $this->assertSame($manager, $composer->getInstallationManager());
    }
}
