<?php

namespace Tangram\Console;

use Tangram\Util\Platform;
use Tangram\Util\Silencer;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tangram\Command;
use Tangram\Tangram;
use Tangram\Factory;
use Tangram\IO\IOInterface;
use Tangram\IO\ConsoleIO;
use Tangram\Json\JsonValidationException;
use Tangram\Util\ErrorHandler;
use Tangram\EventDispatcher\ScriptExecutionException;

/**
 * The console application that handles the commands
 *
 */
class Application extends BaseApplication
{
    /**
     * @var Tangram
     */
    protected $tangram;

    /**
     * @var IOInterface
     */
    protected $io;

  private static $logo = '   ________
  /__   __/__     ___   _____  _____ ___     ____ ___
    / / / __ `/  / __ \/ __  \/ ___/ __ `/  / __ `__ \ 
   / / / /__| \ / / / / /_ / / /  / /__| \ / / / / / / 
  /_/  \____\ \/_/ /_/\___/ /_/   \____\ \/_/ /_/ /_/ 
             `-`      __ / /            `-`
                     /____/   
';

    private $hasPluginCommands = false;
    private $disablePluginsByDefault = false;

    public function __construct()
    {
        static $shutdownRegistered = false;

//        if (function_exists('ini_set') && extension_loaded('xdebug')) {
//            ini_set('xdebug.show_exception_trace', false);
//            ini_set('xdebug.scream', false);
//        }

        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(Silencer::call('date_default_timezone_get'));
        }

        if (!$shutdownRegistered) {
            $shutdownRegistered = true;

            register_shutdown_function(function () {
                $lastError = error_get_last();

                if ($lastError && $lastError['message'] &&
                   (strpos($lastError['message'], 'Allowed memory') !== false /*Zend PHP out of memory error*/ ||
                    strpos($lastError['message'], 'exceeded memory') !== false /*HHVM out of memory errors*/)) {
                    echo "\n". 'Check https://nxlib.xyz/doc/articles/troubleshooting.md#memory-limit-errors for more info on how to handle out of memory errors.';
                }
            });
        }

        parent::__construct('Tangram', Tangram::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $output = Factory::createOutput();
        }
        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->disablePluginsByDefault = $input->hasParameterOption('--no-plugins');
        $io = $this->io = new ConsoleIO($input, $output, $this->getHelperSet());

        ErrorHandler::register($io);
        // switch working dir
        if ($newWorkDir = $this->getNewWorkingDir($input)) {
            $oldWorkingDir = getcwd();
            chdir($newWorkDir);
            $io->writeError('Changed CWD to ' . getcwd(), true, IOInterface::DEBUG);
        }
        // determine command name to be executed without including plugin commands
        $commandName = '';
        if ($name = $this->getCommandName($input)) {
            try {
                $commandName = $this->find($name)->getName();
            } catch (\InvalidArgumentException $e) {
                $io->writeError(PHP_EOL.'<error>'.$e->getMessage().'</error>'.PHP_EOL);
                exit(1);
            }
        }
        // prompt user for dir change if no tangram.json is present in current dir
        if ($io->isInteractive() && !$newWorkDir && !in_array($commandName, array('', 'list', 'init', 'about', 'help', 'diagnose', 'self-update', 'global', 'create-project'), true) && !file_exists(Factory::getTangramFile())) {
            $dir = dirname(getcwd());
            $home = realpath(getenv('HOME') ?: getenv('USERPROFILE') ?: '/');

            // abort when we reach the home dir or top of the filesystem
            while (dirname($dir) !== $dir && $dir !== $home) {

                if (!file_exists($dir.'/'.Factory::getTangramFile())) {
//                    if ($io->askConfirmation('<info>No tangram.json in current directory, do you want to use the one at '.$dir.'?</info> [<comment>Y,n</comment>]? ', true)) {
//                        $oldWorkingDir = getcwd();
//                        chdir($dir);
//                    }
//                    break;
                    $io->alert('<info>No tangram.json in current directory, You must create one first!');
                    exit;
                }
                $dir = dirname($dir);
            }
        }

        // determine command name to be executed incl plugin commands, and check if it's a proxy command
        $isProxyCommand = false;
        if ($name = $this->getCommandName($input)) {
            try {
                $command = $this->find($name);
                $commandName = $command->getName();
                $isProxyCommand = ($command instanceof Command\BaseCommand && $command->isProxyCommand());
            } catch (\InvalidArgumentException $e) {
                $io->writeError(PHP_EOL.'<error>'.$e->getMessage().'</error>'.PHP_EOL);
                exit(1);
            }
        }
        if (!$isProxyCommand) {
            $io->writeError(sprintf(
                'Running %s (%s) with %s on %s',
                Tangram::VERSION,
                Tangram::RELEASE_DATE,
                defined('HHVM_VERSION') ? 'HHVM '.HHVM_VERSION : 'PHP '.PHP_VERSION,
                function_exists('php_uname') ? php_uname('s') . ' / ' . php_uname('r') : 'Unknown OS'
            ), true, IOInterface::DEBUG);

            if (PHP_VERSION_ID < 50302) {
                $io->writeError('<warning>Composer only officially supports PHP 5.3.2 and above, you will most likely encounter problems with your PHP '.PHP_VERSION.', upgrading is strongly recommended.</warning>');
            }

//            if (extension_loaded('xdebug') && !getenv('COMPOSER_DISABLE_XDEBUG_WARN')) {
//                $io->writeError('<warning>You are running composer with xdebug enabled. This has a major impact on runtime performance. See https://getcomposer.org/xdebug</warning>');
//            }

            if (defined('TANGRAM_DEV_WARNING_TIME') && $commandName !== 'self-update' && $commandName !== 'selfupdate' && time() > TANGRAM_DEV_WARNING_TIME) {
//                $io->writeError(sprintf('<warning>Warning: This development build of tangram is over 60 days old. It is recommended to update it by running "%s self-update" to get the latest version.</warning>', $_SERVER['PHP_SELF']));
            }

            if (getenv('TANGRAM_NO_INTERACTION')) {
                $input->setInteractive(false);
            }

            if (!Platform::isWindows() && function_exists('exec') && !getenv('TANGRAM_ALLOW_SUPERUSER')) {
                if (function_exists('posix_getuid') && posix_getuid() === 0) {
                    if ($commandName !== 'self-update' && $commandName !== 'selfupdate') {
                        $io->writeError('<warning>Do not run Tangram as root/super user! </warning>');
                    }
                    if ($uid = (int) getenv('SUDO_UID')) {
                        // Silently clobber any sudo credentials on the invoking user to avoid privilege escalations later on
                        Silencer::call('exec', "sudo -u \\#{$uid} sudo -K > /dev/null 2>&1");
                    }
                }
                // Silently clobber any remaining sudo leases on the current user as well to avoid privilege escalations
                Silencer::call('exec', 'sudo -K > /dev/null 2>&1');
            }

            // Check system temp folder for usability as it can cause weird runtime issues otherwise
            Silencer::call(function () use ($io) {
                $tempfile = sys_get_temp_dir() . '/temp-' . md5(microtime());
                if (!(file_put_contents($tempfile, __FILE__) && (file_get_contents($tempfile) == __FILE__) && unlink($tempfile) && !file_exists($tempfile))) {
                    $io->writeError(sprintf('<error>PHP temp directory (%s) does not exist or is not writable to Tangram. Set sys_temp_dir in your php.ini</error>', sys_get_temp_dir()));
                }
            });

            // add non-standard scripts as own commands
//            $file = Factory::getTangramFile();
//            if (is_file($file) && is_readable($file) && is_array($composer = json_decode(file_get_contents($file), true))) {
//                if (isset($composer['scripts']) && is_array($composer['scripts'])) {
//                    foreach ($composer['scripts'] as $script => $dummy) {
//                        if (!defined('Tangram\Script\ScriptEvents::'.str_replace('-', '_', strtoupper($script)))) {
//                            if ($this->has($script)) {
//                                $io->writeError('<warning>A script named '.$script.' would override a Composer command and has been skipped</warning>');
//                            } else {
//                                $description = null;
//
//                                if (isset($composer['scripts-descriptions'][$script])) {
//                                    $description = $composer['scripts-descriptions'][$script];
//                                }
//
//                                $this->add(new Command\ScriptAliasCommand($script, $description));
//                            }
//                        }
//                    }
//                }
//            }
        }

        try {
            if ($input->hasParameterOption('--profile')) {
                $startTime = microtime(true);
                $this->io->enableDebugging($startTime);
            }

            $result = parent::doRun($input, $output);

            if (isset($oldWorkingDir)) {
                chdir($oldWorkingDir);
            }

            if (isset($startTime)) {
                $io->writeError('<info>Memory usage: '.round(memory_get_usage() / 1024 / 1024, 2).'MB (peak: '.round(memory_get_peak_usage() / 1024 / 1024, 2).'MB), time: '.round(microtime(true) - $startTime, 2).'s');
            }

            restore_error_handler();

            return $result;
        } catch (ScriptExecutionException $e) {
            return $e->getCode();
        } catch (\Exception $e) {
            $this->hintCommonErrors($e);
            restore_error_handler();
            throw $e;
        }
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     * @return string
     */
    private function getNewWorkingDir(InputInterface $input)
    {
        $workingDir = $input->getParameterOption(array('--working-dir', '-d'));
        if (false !== $workingDir && !is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified, '.$workingDir.' does not exist.');
        }

        return $workingDir;
    }

    /**
     * {@inheritDoc}
     */
    private function hintCommonErrors($exception)
    {
        $io = $this->getIO();

        Silencer::suppress();
        try {
            $tangarm = $this->getTangram(false, true);
            if ($tangarm) {
                $config = $tangarm->getConfig();

                $minSpaceFree = 1024 * 1024;
                if ((($df = disk_free_space($dir = $config->get('home'))) !== false && $df < $minSpaceFree)
                    || (($df = disk_free_space($dir = $config->get('vendor-dir'))) !== false && $df < $minSpaceFree)
                    || (($df = disk_free_space($dir = sys_get_temp_dir())) !== false && $df < $minSpaceFree)
                ) {
                    $io->writeError('<error>The disk hosting '.$dir.' is full, this may be the cause of the following exception</error>', true, IOInterface::QUIET);
                }
            }
        } catch (\Exception $e) {
        }
        Silencer::restore();

        if (Platform::isWindows() && false !== strpos($exception->getMessage(), 'The system cannot find the path specified')) {
            $io->writeError('<error>The following exception may be caused by a stale entry in your cmd.exe AutoRun</error>', true, IOInterface::QUIET);
            $io->writeError('<error>Check http://nxlib.xyz for details</error>', true, IOInterface::QUIET);
        }

        if (false !== strpos($exception->getMessage(), 'fork failed - Cannot allocate memory')) {
            $io->writeError('<error>The following exception is caused by a lack of memory or swap, or not having swap configured</error>', true, IOInterface::QUIET);
            $io->writeError('<error>Check http://nxlib.xyz for details</error>', true, IOInterface::QUIET);
        }
    }

    /**
     * @param  bool $required
     * @param  bool|null $disablePlugins
     *
     * @return \Tangram\Tangram
     * @throws \Exception
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Json\JsonValidationException
     */
    public function getTangram($required = true, $disablePlugins = null)
    {
        if (null === $disablePlugins) {
            $disablePlugins = $this->disablePluginsByDefault;
        }

        if (null === $this->tangram) {
            try {
                $this->tangram = Factory::create($this->io, null, $disablePlugins);
            } catch (\InvalidArgumentException $e) {
                if ($required) {
                    $this->io->writeError($e->getMessage());
                    exit(1);
                }
            } catch (JsonValidationException $e) {
                $errors = ' - ' . implode(PHP_EOL . ' - ', $e->getErrors());
                $message = $e->getMessage() . ':' . PHP_EOL . $errors;
                throw new JsonValidationException($message);
            }
        }
        return $this->tangram;
    }

    /**
     * Removes the cached composer instance
     */
    public function resetTangram()
    {
        $this->tangram = null;
    }

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }

    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }

    /**
     * Initializes all the composer commands.
     */
    protected function getDefaultCommands()
    {
        $commands = array_merge(parent::getDefaultCommands(), array(
            new Command\AboutCommand(),
            new Command\Build\BuildCommand(),
            new Command\CreateCommand(),
            new Command\FrameworkCommand()
        ));

//        if ('phar:' === substr(__FILE__, 0, 5)) {
//            $commands[] = new Command\SelfUpdateCommand();
//        }

        return $commands;
    }

    /**
     * {@inheritDoc}
     */
    public function getLongVersion()
    {
        if (Tangram::BRANCH_ALIAS_VERSION) {
            return sprintf(
                '<info>%s</info> version <comment>%s (%s)</comment> %s',
                $this->getName(),
                Tangram::BRANCH_ALIAS_VERSION,
                $this->getVersion(),
                Tangram::RELEASE_DATE
            );
        }

        return parent::getLongVersion() . ' ' . Tangram::RELEASE_DATE;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('--profile', null, InputOption::VALUE_NONE, 'Display timing and memory usage information'));
        $definition->addOption(new InputOption('--no-plugins', null, InputOption::VALUE_NONE, 'Whether to disable plugins.'));
        $definition->addOption(new InputOption('--working-dir', '-d', InputOption::VALUE_REQUIRED, 'If specified, use the given directory as working directory.'));

        return $definition;
    }
}
