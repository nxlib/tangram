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

namespace Tangram\Util;

use Tangram\Spdx\SpdxLicenses;

trigger_error('The ' . __NAMESPACE__ . '\SpdxLicense class is deprecated, use Tangram\Spdx\SpdxLicenses instead.', E_USER_DEPRECATED);

/**
 * @deprecated use Tangram\Spdx\SpdxLicenses instead
 */
class SpdxLicense extends SpdxLicenses
{
}
