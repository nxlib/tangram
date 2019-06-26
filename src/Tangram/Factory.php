<?php

namespace Tangram;

use Tangram\Config\JsonConfigSource;
use Tangram\Config\ProjectConfig;
use Tangram\Json\JsonFile;
use Tangram\IO\IOInterface;
use Tangram\Util\Platform;
use Tangram\Util\Silencer;
use Seld\JsonLint\DuplicateKeyException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Seld\JsonLint\JsonParser;

/**
 * Creates a configured instance of tangram.
 */
class Factory
{
    /**
     * @return bool
     */
    private static function useXdg()
    {
        foreach (array_keys($_SERVER) as $key) {
            if (substr($key, 0, 4) === 'XDG_') {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \RuntimeException
     * @return string
     */
    private static function getUserDir()
    {
        $home = getenv('HOME');
        if (!$home) {
            throw new \RuntimeException('The HOME or TANGRAM_HOME environment variable must be set for tangram to run correctly');
        }

        return rtrim(strtr($home, '\\', '/'), '/');
    }

    /**
     * @throws \RuntimeException
     * @return string
     */
    protected static function getHomeDir()
    {
        $home = getenv('TANGRAM_HOME');
        if ($home) {
            return $home;
        }

        if (Platform::isWindows()) {
            if (!getenv('APPDATA')) {
                throw new \RuntimeException('The APPDATA or TANGRAM_HOME environment variable must be set for tangram to run correctly');
            }

            return rtrim(strtr(getenv('APPDATA'), '\\', '/'), '/') . '/Tangram';
        }

        $userDir = self::getUserDir();
        if (is_dir($userDir . '/.tangram')) {
            return $userDir . '/.tangram';
        }

        if (self::useXdg()) {
            // XDG Base Directory Specifications
            $xdgConfig = getenv('XDG_CONFIG_HOME') ?: $userDir . '/.config';

            return $xdgConfig . '/tangram';
        }

        return $userDir . '/.tangram';
    }

    /**
     * @param  string $home
     * @return string
     */
    protected static function getCacheDir($home)
    {
        $cacheDir = getenv('TANGRAM_CACHE_DIR');
        if ($cacheDir) {
            return $cacheDir;
        }

        $homeEnv = getenv('TANGRAM_HOME');
        if ($homeEnv) {
            return $homeEnv . '/cache';
        }

        if (Platform::isWindows()) {
            if ($cacheDir = getenv('LOCALAPPDATA')) {
                $cacheDir .= '/Tangram';
            } else {
                $cacheDir = $home . '/cache';
            }

            return rtrim(strtr($cacheDir, '\\', '/'), '/');
        }

        $userDir = self::getUserDir();
        if ($home === $userDir . '/.tangram' && is_dir($home . '/cache')) {
            return $home . '/cache';
        }

        if (self::useXdg()) {
            $xdgCache = getenv('XDG_CACHE_HOME') ?: $userDir . '/.cache';

            return $xdgCache . '/tangram';
        }

        return $home . '/cache';
    }

    /**
     * @param  string $home
     * @return string
     */
    protected static function getDataDir($home)
    {
        $homeEnv = getenv('TANGRAM_HOME');
        if ($homeEnv) {
            return $homeEnv;
        }

        if (Platform::isWindows()) {
            return strtr($home, '\\', '/');
        }

        $userDir = self::getUserDir();
        if ($home !== $userDir . '/.tangram' && self::useXdg()) {
            $xdgData = getenv('XDG_DATA_HOME') ?: $userDir . '/.local/share';

            return $xdgData . '/tangram';
        }

        return $home;
    }


    public static function getTangramFile()
    {
        return trim(getenv('TANGRAM')) ?: './tangram.json';
    }
    public static function getComposerConfigureFile()
    {
        return trim(getenv('COMPOSER_CONFIG')) ?: './composer.json';
    }

    public static function createAdditionalStyles()
    {
        return array(
            'highlight' => new OutputFormatterStyle('red'),
            'warning' => new OutputFormatterStyle('black', 'yellow'),
        );
    }

    /**
     * Creates a ConsoleOutput instance
     *
     * @return ConsoleOutput
     */
    public static function createOutput()
    {
        $styles = self::createAdditionalStyles();
        $formatter = new OutputFormatter(false, $styles);

        return new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
    }


    /**
     * Creates a Tangram instance
     *
     * @param  IOInterface $io IO instance
     * @param  array|string|null $localConfig either a configuration array or a filename to read from, if null it will
     *                                                   read from the default filename
     * @param null $cwd
     *
     * @return Tangram
     * @throws \Exception
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Json\JsonValidationException
     */
    public function createTangram(IOInterface $io, $localConfig = null, $cwd = null)
    {
        $cwd = $cwd ?: getcwd();
        // load Tangram configuration
        if (null === $localConfig) {
            $localConfig = static::getTangramFile();
        }
        if (is_string($localConfig)) {
            $tangramFile = $localConfig;
            $file = new JsonFile($localConfig, null, $io);
            if (!$file->exists()) {
                if ($localConfig === './tangram.json' || $localConfig === 'tangram.json') {
                    $message = 'Tangram could not find a tangram.json file in '.$cwd;
                } else {
                    $message = 'Tangram could not find the config file: '.$localConfig;
                }
                $instructions = 'To initialize a project, please create a tangram.json file ';
                throw new \InvalidArgumentException($message.PHP_EOL.$instructions);
            }
            $file->validateSchema(JsonFile::LAX_SCHEMA);
            $jsonParser = new JsonParser;
            try {
                $jsonParser->parse(file_get_contents($localConfig), JsonParser::DETECT_KEY_CONFLICTS);
            } catch (DuplicateKeyException $e) {
                $details = $e->getDetails();
                $io->writeError('<warning>Key '.$details['key'].' is a duplicate in '.$localConfig.' at line '.$details['line'].'</warning>');
            }
            $localConfig = $file->read();
        }

        // Load config and override with local config/auth config
        $config = static::createConfig($io, $cwd);
//        $config->merge($localConfig);
        if (isset($tangramFile)) {
            $io->writeError('Loading config file ' . $tangramFile, true, IOInterface::DEBUG);
            $config->setConfigSource(new JsonConfigSource(new JsonFile(realpath($tangramFile), null, $io)));
            $localAuthFile = new JsonFile(dirname(realpath($tangramFile)) . '/auth.json', null, $io);
            if ($localAuthFile->exists()) {
                $io->writeError('Loading config file ' . $localAuthFile->getPath(), true, IOInterface::DEBUG);
                $config->merge(array('config' => $localAuthFile->read()));
                $config->setAuthConfigSource(new JsonConfigSource($localAuthFile, true));
            }
        }

        // initialize tangram
        $tangram = new Tangram();
        $tangram->setConfig($config);
        $tangram->setPorjectConfig(new ProjectConfig());
        return $tangram;
    }

    /**
     * @param  IOInterface|null $io
     * @param null $cwd
     * @return Config
     * @throws \Exception
     */
    public static function createConfig(IOInterface $io = null, $cwd = null)
    {
        $cwd = $cwd ?: getcwd();
        $config = new Config(true, $cwd);
        // determine and add main dirs to the config
        $home = self::getHomeDir();
        $config->merge(array('config' => array(
            'home' => $home,
            'cache-dir' => self::getCacheDir($home),
            'data-dir' => self::getDataDir($home),
        )));
        $htaccessProtect = (bool) $config->get('htaccess-protect');
        if ($htaccessProtect) {
            // Protect directory against web access. Since HOME could be
            // the www-data's user home and be web-accessible it is a
            // potential security risk
            $dirs = array($config->get('home'), $config->get('cache-dir'), $config->get('data-dir'));
            foreach ($dirs as $dir) {
                if (!file_exists($dir . '/.htaccess')) {
                    if (!is_dir($dir)) {
                        Silencer::call('mkdir', $dir, 0777, true);
                    }
                    Silencer::call('file_put_contents', $dir . '/.htaccess', 'Deny from all');
                }
            }
        }
        // load global config
        $file = new JsonFile($config->get('home').'/config.json');
        if ($file->exists()) {
            if ($io && $io->isDebug()) {
                $io->writeError('Loading config file ' . $file->getPath());
            }
            $config->merge($file->read());
        }
        $config->setConfigSource(new JsonConfigSource($file));
        // load global auth file
        $file = new JsonFile($config->get('home').'/auth.json');
        if ($file->exists()) {
            if ($io && $io->isDebug()) {
                $io->writeError('Loading config file ' . $file->getPath());
            }
            $config->merge(array('config' => $file->read()));
        }
        $config->setAuthConfigSource(new JsonConfigSource($file, true));
        return $config;
    }


    /**
     * @param  IOInterface $io IO instance
     * @param  mixed $config either a configuration array or a filename to read from, if null it will read from
     *                                     the default filename
     * @param  bool $disablePlugins Whether plugins should not be loaded
     *
     * @return Tangram
     * @throws \Exception
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Json\JsonValidationException
     */
    public static function create(IOInterface $io, $config = null)
    {
        $factory = new static();

        return $factory->createTangram($io, $config);
    }
}
