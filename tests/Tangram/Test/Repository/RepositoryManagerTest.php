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

namespace Tangram\Test\Repository;

use Tangram\Repository\RepositoryManager;
use Tangram\TestCase;
use Tangram\Util\Filesystem;

class RepositoryManagerTest extends TestCase
{
    protected $tmpdir;

    public function setUp()
    {
        $this->tmpdir = $this->getUniqueTmpDirectory();
    }

    public function tearDown()
    {
        if (is_dir($this->tmpdir)) {
            $fs = new Filesystem();
            $fs->removeDirectory($this->tmpdir);
        }
    }

    public function testPrepend()
    {
        $rm = new RepositoryManager(
            $this->getMock('Tangram\IO\IOInterface'),
            $this->getMock('Tangram\Config'),
            $this->getMockBuilder('Tangram\EventDispatcher\EventDispatcher')->disableOriginalConstructor()->getMock()
        );

        $repository1 = $this->getMock('Tangram\Repository\RepositoryInterface');
        $repository2 = $this->getMock('Tangram\Repository\RepositoryInterface');
        $rm->addRepository($repository1);
        $rm->prependRepository($repository2);

        $this->assertEquals(array($repository2, $repository1), $rm->getRepositories());
    }

    /**
     * @dataProvider creationCases
     */
    public function testRepoCreation($type, $options, $exception = null)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }

        $rm = new RepositoryManager(
            $this->getMock('Tangram\IO\IOInterface'),
            $config = $this->getMock('Tangram\Config', array('get')),
            $this->getMockBuilder('Tangram\EventDispatcher\EventDispatcher')->disableOriginalConstructor()->getMock()
        );

        $tmpdir = $this->tmpdir;
        $config
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($arg) use ($tmpdir) {
                return 'cache-repo-dir' === $arg ? $tmpdir : null;
            }))
        ;

        $rm->setRepositoryClass('composer', 'Tangram\Repository\ComposerRepository');
        $rm->setRepositoryClass('vcs', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('package', 'Tangram\Repository\PackageRepository');
        $rm->setRepositoryClass('pear', 'Tangram\Repository\PearRepository');
        $rm->setRepositoryClass('git', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('svn', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('perforce', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('hg', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('artifact', 'Tangram\Repository\ArtifactRepository');

        $rm->createRepository('composer', array('url' => 'http://example.org'));
        $rm->createRepository($type, $options);
    }

    public function creationCases()
    {
        $cases = array(
            array('composer', array('url' => 'http://example.org')),
            array('vcs', array('url' => 'http://github.com/foo/bar')),
            array('git', array('url' => 'http://github.com/foo/bar')),
            array('git', array('url' => 'git@example.org:foo/bar.git')),
            array('svn', array('url' => 'svn://example.org/foo/bar')),
            array('pear', array('url' => 'http://pear.example.org/foo')),
            array('package', array('package' => array())),
            array('invalid', array(), 'InvalidArgumentException'),
        );

        if (class_exists('ZipArchive')) {
            $cases[] = array('artifact', array('url' => '/path/to/zips'));
        }

        return $cases;
    }
}
