<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 11/01/2018
 * Time: 01:07
 */

namespace Tangram\Command\Build;

use Tangram\Command\BaseCommandRun;

class RouterBuild extends BaseCommandRun
{

    public function exec($targetApplication = null){
        $this->writeHeader('🎯 Build Router >>>');
    }
}