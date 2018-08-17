<?php
/**
 * Author: garming
 * Date: 26/03/2018
 * Time: 11:25
 */

namespace Tangram\AutoGenerator;


class EnvMapGenerator extends BaseGenerator
{
    public function __construct()
    {
        $this->fileName = "autobuild_env.php";
        $this->config = [];
    }

    /**
     * @var array
     */
    private $config;

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return \Tangram\AutoGenerator\EnvMapGenerator
     */
    public function setConfig(array $config):EnvMapGenerator
    {
        $this->config = array_merge($this->config,$config);
        return $this;
    }

    public function generate($absolutePathPerfix)
    {
        $content = preg_replace('#,(\s+|)\)#', '$1)', var_export($this->config, true));
        $this->write($absolutePathPerfix,$this->fileContent($content));
    }
    private function fileContent($str){
        return <<<"EOF"
<?php
function env(\$key = null){
    static \$config = {$str};
    if(empty(\$key)){
        return \$config;
    }
    if(isset(\$config[\$key])){
        return \$config[\$key];
    }
    return null;
}
EOF;
    }
}