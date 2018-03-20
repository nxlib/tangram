<?php

namespace Tangram\Repository;

use Tangram\Util\Silencer;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PlatformRepository
{
    const PLATFORM_PACKAGE_REGEX = '{^(?:php(?:-64bit|-ipv6|-zts|-debug)?|hhvm|(?:ext|lib)-[^/]+)$}i';
}
