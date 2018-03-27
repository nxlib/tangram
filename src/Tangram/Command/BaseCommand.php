<?php


namespace Tangram\Command;

use Tangram\Tangram;
use Tangram\Config;
use Tangram\Console\Application;
use Tangram\IO\IOInterface;
use Tangram\IO\NullIO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    /**
     * @var Tangram
     */
    private $tangram;

    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @param  bool $required
     * @param  bool|null $disablePlugins
     *
     * @return Tangram
     * @throws \Exception
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Json\JsonValidationException
     */
    public function getTangram($required = true, $disablePlugins = null)
    {
        if (null === $this->tangram) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application    Application */
                $this->tangram = $application->getTangram($required, $disablePlugins);
            } elseif ($required) {
                throw new \RuntimeException(
                    'Could not create a Tangram\Tangram instance, you must inject '.
                    'one if this command is not used with a Tangram\Console\Application instance'
                );
            }
        }
        var_dump($this->tangram);
        return $this->tangram;
    }


    /**
     * Removes the cached composer instance
     */
    public function resetTangram()
    {
        $this->tangram = null;
        $this->getApplication()->resetTangram();
    }

    /**
     * Whether or not this command is meant to call another command.
     *
     * This is mainly needed to avoid duplicated warnings messages.
     *
     * @return bool
     */
    public function isProxyCommand()
    {
        return false;
    }

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        if (null === $this->io) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application    Application */
                $this->io = $application->getIO();
            } else {
                $this->io = new NullIO();
            }
        }

        return $this->io;
    }

    /**
     * @param IOInterface $io
     */
    public function setIO(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * @param \Tangram\Tangram $tangram
     */
    public function setTangram(\Tangram\Tangram $tangram)
    {
        $this->tangram = $tangram;
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (true === $input->hasParameterOption(array('--no-ansi')) && $input->hasOption('no-progress')) {
            $input->setOption('no-progress', true);
        }

        parent::initialize($input, $output);
    }

    /**
     * Returns preferSource and preferDist values based on the configuration.
     *
     * @param Config         $config
     * @param InputInterface $input
     * @param bool           $keepVcsRequiresPreferSource
     *
     * @return bool[] An array composed of the preferSource and preferDist values
     */
    protected function getPreferredInstallOptions(Config $config, InputInterface $input, $keepVcsRequiresPreferSource = false)
    {
        $preferSource = false;
        $preferDist = false;

        switch ($config->get('preferred-install')) {
            case 'source':
                $preferSource = true;
                break;
            case 'dist':
                $preferDist = true;
                break;
            case 'auto':
            default:
                // noop
                break;
        }

        if ($input->getOption('prefer-source') || $input->getOption('prefer-dist') || ($keepVcsRequiresPreferSource && $input->hasOption('keep-vcs') && $input->getOption('keep-vcs'))) {
            $preferSource = $input->getOption('prefer-source') || ($keepVcsRequiresPreferSource && $input->hasOption('keep-vcs') && $input->getOption('keep-vcs'));
            $preferDist = $input->getOption('prefer-dist');
        }

        return array($preferSource, $preferDist);
    }
}
