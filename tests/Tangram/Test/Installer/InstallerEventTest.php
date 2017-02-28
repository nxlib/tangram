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

namespace Tangram\Test\Installer;

use Tangram\Installer\InstallerEvent;

class InstallerEventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetter()
    {
        $composer = $this->getMock('Tangram\Composer');
        $io = $this->getMock('Tangram\IO\IOInterface');
        $policy = $this->getMock('Tangram\DependencyResolver\PolicyInterface');
        $pool = $this->getMockBuilder('Tangram\DependencyResolver\Pool')->disableOriginalConstructor()->getMock();
        $installedRepo = $this->getMockBuilder('Tangram\Repository\CompositeRepository')->disableOriginalConstructor()->getMock();
        $request = $this->getMockBuilder('Tangram\DependencyResolver\Request')->disableOriginalConstructor()->getMock();
        $operations = array($this->getMock('Tangram\DependencyResolver\Operation\OperationInterface'));
        $event = new InstallerEvent('EVENT_NAME', $composer, $io, true, $policy, $pool, $installedRepo, $request, $operations);

        $this->assertSame('EVENT_NAME', $event->getName());
        $this->assertInstanceOf('Tangram\Composer', $event->getComposer());
        $this->assertInstanceOf('Tangram\IO\IOInterface', $event->getIO());
        $this->assertTrue($event->isDevMode());
        $this->assertInstanceOf('Tangram\DependencyResolver\PolicyInterface', $event->getPolicy());
        $this->assertInstanceOf('Tangram\DependencyResolver\Pool', $event->getPool());
        $this->assertInstanceOf('Tangram\Repository\CompositeRepository', $event->getInstalledRepo());
        $this->assertInstanceOf('Tangram\DependencyResolver\Request', $event->getRequest());
        $this->assertCount(1, $event->getOperations());
    }
}
