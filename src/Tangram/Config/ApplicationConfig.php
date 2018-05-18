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
use Tangram\Config\Exception\ApplicationConfigException;
use Tangram\Config\Exception\ModuleConfigException;
use Tangram\Json\JsonFile;

class ApplicationConfig {

    const SCHEMA_FILE = __DIR__ . '/../../../res/tangram-application-schema.json';

    private $name;
    private $description;
    private $keywords;
    private $license;
    private $authors;
    private $require;
    private $requireModule;
    private $minimumStability;
    private $psr4Autoload;
    private $web;
    private $restful;

    /**
     * ModuleConfig constructor.
     *
     * @param string $application
     * @param string|null $absoluteApplicationPath
     *
     * @throws \Seld\JsonLint\ParsingException
     * @throws \Tangram\Config\Exception\ApplicationConfigException
     * @throws \Tangram\Json\JsonValidationException
     */
    public function __construct(string $application,string $absoluteApplicationPath = null) {
        if(is_null($absoluteApplicationPath)){
            $projectConfig = new ProjectConfig();
            $applicationPath = $projectConfig->getProjectRoot().DIRECTORY_SEPARATOR.$projectConfig->getApplicationPath();
        }else{
            $applicationPath = $absoluteApplicationPath.DIRECTORY_SEPARATOR.$application;
        }
        $tangramFile = $applicationPath.DIRECTORY_SEPARATOR."tangram.json";
        $file = new JsonFile($tangramFile);

        if (!$file->exists()) {
            $message = 'Application could not find a tangram.json file in '.$applicationPath;
            $instructions = 'To initialize a application, please create a tangram.json file ';
            throw new \InvalidArgumentException($message.PHP_EOL.$instructions);
        }
        $file->validateSchema(JsonFile::STRICT_SCHEMA,self::SCHEMA_FILE);
        $jsonParser = new JsonParser;
        try {
            $jsonParser->parse(file_get_contents($tangramFile), JsonParser::DETECT_KEY_CONFLICTS);
        } catch (DuplicateKeyException $e) {
            $details = $e->getDetails();
            throw new ApplicationConfigException('Key '.$details['key'].' is a duplicate in '.$tangramFile.' at line '.$details['line']);
        }
        $config = $file->read();
        $this->name = $config['name'];
        $this->description = $config['description'];
        $this->keywords = $config['keywords'];
        $this->license = $config['license'];
        $this->authors = $config['authors'];
        $this->require = $config['require'];
        $this->requireModule = $config['require-module'] ?? [];
        $this->psr4Autoload = $config['autoload']['psr-4'] ?? [];
        $this->web = $config['web'];
        $this->restful = $config['restful'];
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

    /**
     * @return mixed
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @return mixed
     */
    public function getRestful()
    {
        return $this->restful;
    }


}