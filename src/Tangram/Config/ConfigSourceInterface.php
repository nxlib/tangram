<?php


namespace Tangram\Config;

interface ConfigSourceInterface
{
    /**
     * Add a repository
     *
     * @param string $name   Name
     * @param array  $config Configuration
     */
    public function addRepository($name, $config);

    /**
     * Remove a repository
     *
     * @param string $name
     */
    public function removeRepository($name);

    /**
     * Add a config setting
     *
     * @param string $name  Name
     * @param string $value Value
     */
    public function addConfigSetting($name, $value);

    /**
     * Remove a config setting
     *
     * @param string $name
     */
    public function removeConfigSetting($name);

    /**
     * Add a property
     *
     * @param string $name  Name
     * @param string $value Value
     */
    public function addProperty($name, $value);

    /**
     * Remove a property
     *
     * @param string $name
     */
    public function removeProperty($name);

    /**
     * Add a package link
     *
     * @param string $type  Type (require, require-dev, provide, suggest, replace, conflict)
     * @param string $name  Name
     * @param string $value Value
     */
    public function addLink($type, $name, $value);

    /**
     * Remove a package link
     *
     * @param string $type Type (require, require-dev, provide, suggest, replace, conflict)
     * @param string $name Name
     */
    public function removeLink($type, $name);

    /**
     * Gives a user-friendly name to this source (file path or so)
     *
     * @return string
     */
    public function getName();
}
