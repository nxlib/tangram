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

namespace Tangram\Package\LinkConstraint;

use Tangram\Semver\Constraint\ConstraintInterface;

trigger_error('The ' . __NAMESPACE__ . '\LinkConstraintInterface interface is deprecated, use Tangram\Semver\Constraint\ConstraintInterface instead.', E_USER_DEPRECATED);

/**
 * @deprecated use Tangram\Semver\Constraint\ConstraintInterface instead
 */
interface LinkConstraintInterface extends ConstraintInterface
{
}
