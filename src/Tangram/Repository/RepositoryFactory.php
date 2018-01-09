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

namespace Tangram\Repository;

use Tangram\Factory;
use Tangram\IO\IOInterface;
use Tangram\Config;
use Tangram\EventDispatcher\EventDispatcher;
use Tangram\Util\RemoteFilesystem;
use Tangram\Json\JsonFile;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class RepositoryFactory
{
    /**
     * @param  IOInterface $io
     * @param  Config      $config
     * @param  string      $repository
     * @param  bool        $allowFilesystem
     * @return array|mixed
     */
    public static function configFromString(IOInterface $io, Config $config, $repository, $allowFilesystem = false)
    {
        if (0 === strpos($repository, 'http')) {
            $repoConfig = array('type' => 'composer', 'url' => $repository);
        } elseif ("json" === pathinfo($repository, PATHINFO_EXTENSION)) {
            $json = new JsonFile($repository, Factory::createRemoteFilesystem($io, $config));
            $data = $json->read();
            if (!empty($data['packages']) || !empty($data['includes']) || !empty($data['provider-includes'])) {
                $repoConfig = array('type' => 'composer', 'url' => 'file://' . strtr(realpath($repository), '\\', '/'));
            } elseif ($allowFilesystem) {
                $repoConfig = array('type' => 'filesystem', 'json' => $json);
            } else {
                throw new \InvalidArgumentException("Invalid repository URL ($repository) given. This file does not contain a valid composer repository.");
            }
        } elseif ('{' === substr($repository, 0, 1)) {
            // assume it is a json object that makes a repo config
            $repoConfig = JsonFile::parseJson($repository);
        } else {
            throw new \InvalidArgumentException("Invalid repository url ($repository) given. Has to be a .json file, an http url or a JSON object.");
        }

        return $repoConfig;
    }

    /**
     * @param  IOInterface         $io
     * @param  Config              $config
     * @param  string              $repository
     * @param  bool                $allowFilesystem
     * @return RepositoryInterface
     */
    public static function fromString(IOInterface $io, Config $config, $repository, $allowFilesystem = false)
    {
        $repoConfig = static::configFromString($io, $config, $repository, $allowFilesystem);

        return static::createRepo($io, $config, $repoConfig);
    }

    /**
     * @param  IOInterface         $io
     * @param  Config              $config
     * @param  array               $repoConfig
     * @return RepositoryInterface
     */
    public static function createRepo(IOInterface $io, Config $config, array $repoConfig)
    {
        $rm = static::manager($io, $config, null, Factory::createRemoteFilesystem($io, $config));
        $repos = static::createRepos($rm, array($repoConfig));

        return reset($repos);
    }

    /**
     * @param  IOInterface|null       $io
     * @param  Config|null            $config
     * @param  RepositoryManager|null $rm
     * @return RepositoryInterface[]
     */
    public static function defaultRepos(IOInterface $io = null, Config $config = null, RepositoryManager $rm = null)
    {
        if (!$config) {
            $config = Factory::createConfig($io);
        }
        if (!$rm) {
            if (!$io) {
                throw new \InvalidArgumentException('This function requires either an IOInterface or a RepositoryManager');
            }
            $rm = static::manager($io, $config, null, Factory::createRemoteFilesystem($io, $config));
        }

        return static::createRepos($rm, $config->getRepositories());
    }

    /**
     * @param  IOInterface       $io
     * @param  Config            $config
     * @param  EventDispatcher   $eventDispatcher
     * @param  RemoteFilesystem  $rfs
     * @return RepositoryManager
     */
    public static function manager(IOInterface $io, Config $config, EventDispatcher $eventDispatcher = null, RemoteFilesystem $rfs = null)
    {
        $rm = new RepositoryManager($io, $config, $eventDispatcher, $rfs);
        $rm->setRepositoryClass('composer', 'Tangram\Repository\ComposerRepository');
        $rm->setRepositoryClass('vcs', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('package', 'Tangram\Repository\PackageRepository');
        $rm->setRepositoryClass('pear', 'Tangram\Repository\PearRepository');
        $rm->setRepositoryClass('git', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('github', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('gitlab', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('svn', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('fossil', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('perforce', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('hg', 'Tangram\Repository\VcsRepository');
        $rm->setRepositoryClass('artifact', 'Tangram\Repository\ArtifactRepository');
        $rm->setRepositoryClass('path', 'Tangram\Repository\PathRepository');

        return $rm;
    }

    /**
     * @return RepositoryInterface[]
     */
    private static function createRepos(RepositoryManager $rm, array $repoConfigs)
    {
        $repos = array();

        foreach ($repoConfigs as $index => $repo) {
            if (is_string($repo)) {
                throw new \UnexpectedValueException('"repositories" should be an array of repository definitions, only a single repository was given');
            }
            if (!is_array($repo)) {
                throw new \UnexpectedValueException('Repository "'.$index.'" ('.json_encode($repo).') should be an array, '.gettype($repo).' given');
            }
            if (!isset($repo['type'])) {
                throw new \UnexpectedValueException('Repository "'.$index.'" ('.json_encode($repo).') must have a type defined');
            }
            $name = is_int($index) && isset($repo['url']) ? preg_replace('{^https?://}i', '', $repo['url']) : $index;
            while (isset($repos[$name])) {
                $name .= '2';
            }
            if ($repo['type'] === 'filesystem') {
                $repos[$name] = new FilesystemRepository($repo['json']);
            } else {
                $repos[$name] = $rm->createRepository($repo['type'], $repo, $index);
            }
        }

        return $repos;
    }
}
