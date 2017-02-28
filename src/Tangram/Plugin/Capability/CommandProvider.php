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

namespace Tangram\Plugin\Capability;

/**
 * Commands Provider Interface
 *
 * This capability will receive an array with 'composer' and 'io' keys as
 * constructor argument. Those contain Tangram\Composer and Tangram\IO\IOInterface
 * instances. It also contains a 'plugin' key containing the plugin instance that
 * created the capability.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
interface CommandProvider extends Capability
{
    /**
     * Retreives an array of commands
     *
     * @return \Tangram\Command\BaseCommand[]
     */
    public function getCommands();
}
