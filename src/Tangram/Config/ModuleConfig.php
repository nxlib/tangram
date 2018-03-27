<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 09/02/2018
 * Time: 15:37
 */

namespace Tangram\Config;


use Seld\JsonLint\DuplicateKeyException;
use Seld\JsonLint\JsonParser;
use Tangram\Config\Exception\ModuleConfigException;
use Tangram\Json\JsonFile;

class ModuleConfig {

    const SCHEMA_FILE = __DIR__ . '/../../../res/tangram-module-schema.json';

    private $name;
    private $module;
    private $description;
    private $keywords;
    private $type;
    private $license;
    private $authors;
    private $require;
    private $requireModule;
    private $minimumStability;
    private $psr4Autoload;

    /**
     * ModuleConfig constructor.
     *
     * @param string $module
     * @param string|NULL $absoluteModulePath
     *
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Config\Exception\ModuleConfigException
     * @throws \Tangram\Json\JsonValidationException
     */
    public function __construct(string $module,string $absoluteModulePath = null) {
        if(is_null($absoluteModulePath)){
            $projectConfig = new ProjectConfig();
            $modulePath = $projectConfig->getProjectRoot().DIRECTORY_SEPARATOR.$projectConfig->getModulePath();
        }else{
            $modulePath = $absoluteModulePath.DIRECTORY_SEPARATOR.$module;
        }
        $tangramFile = $modulePath.DIRECTORY_SEPARATOR."tangram.json";
        $file = new JsonFile($tangramFile);

        if (!$file->exists()) {
            $message = 'Module could not find a tangram.json file in '.$modulePath;
            $instructions = 'To initialize a module, please create a tangram.json file ';
            throw new \InvalidArgumentException($message.PHP_EOL.$instructions);
        }
        $file->validateSchema(JsonFile::STRICT_SCHEMA,self::SCHEMA_FILE);
        $jsonParser = new JsonParser;
        try {
            $jsonParser->parse(file_get_contents($tangramFile), JsonParser::DETECT_KEY_CONFLICTS);
        } catch (DuplicateKeyException $e) {
            $details = $e->getDetails();
            throw new ModuleConfigException('Key '.$details['key'].' is a duplicate in '.$tangramFile.' at line '.$details['line']);
        }
        $config = $file->read();
        $this->name = $config['name'];
        $this->module = $config['module'];
        $this->description = $config['description'];
        $this->keywords = $config['keywords'];
        $this->type = $config['type'];
        $this->license = $config['license'];
        $this->authors = $config['authors'];
        $this->require = $config['require'];
        $this->requireModule = $config['require-module'] ?? [];
        $this->psr4Autoload = $config['autoload']['psr-4'] ?? [];
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getLicense() {
        return $this->license;
    }

    /**
     * @return mixed
     */
    public function getAuthors() {
        return $this->authors;
    }

    /**
     * @return mixed
     */
    public function getRequire() {
        return $this->require;
    }

    /**
     * @return mixed
     */
    public function getRequireModule() {
        return $this->requireModule;
    }

    /**
     * @return mixed
     */
    public function getMinimumStability() {
        return $this->minimumStability;
    }

    /**
     * @return array
     */
    public function getPsr4Autoload(): array {
        return $this->psr4Autoload;
    }

}