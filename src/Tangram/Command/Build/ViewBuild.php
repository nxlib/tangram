<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 17:14
 */

namespace Tangram\Command\Build;


use Tangram\Command\BaseCommandRun;

class ViewBuild extends BaseCommandRun
{
    public function exec($targetApplication = null){
        $this->writeHeader('👮 Build Views ');
    }
}