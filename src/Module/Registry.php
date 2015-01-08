<?php

namespace BonsaiEdit\Module;

require_once __DIR__ . '/../BonsaiEdit.php';

use Bonsai\Exception\BonsaiException;

/**
 * Registry for settings related to the Bonsai
 *
 * @author abenedict
 */
class Registry
{

    const DEFAULT_INI = "Config/config.ini";
    const PROJECT_NAMESPACE = "BonsaiEdit";
    const PROJECT_NAME = "Bonsai Editor";

    /** @var boolean */
    private $init = false;

    /** @var array */
    private $config;

    /** @var Bonsai\Module\Registry */
    static $instance = null;

    /**
     * Returns the *Registry* instance of this class.
     *
     * @return Registry The *Registry* instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Registry();
        }

        return self::$instance;
    }

    /**
     * Returns the *Registry* instance of this class.
     *
     * @staticvar Registry $instance The *Registry* instances of this class.
     *
     * @return Registry The *Registry* instance.
     */
    public function initialize($custom = false)
    {
        if ($this->init) {
            return $this;
        }

        $defaultConfigFile = constant(self::PROJECT_NAMESPACE . "\\PROJECT_ROOT") . '/' . self::DEFAULT_INI;
        if (!file_exists($defaultConfigFile)) {
            throw new BonsaiException("Default Configuration for " . self::PROJECT_NAME . " not found.");
        }
        $defaultConfig = parse_ini_file($defaultConfigFile);

        if (!empty($custom)) {
            $customConfigFile = constant(self::PROJECT_NAMESPACE . "\\DOCUMENT_ROOT") . '/' . $custom;
            if (!file_exists($customConfigFile)) {
                throw new BonsaiException("Custom Configuration for " . self::PROJECT_NAME . " not found at $customConfigFile.");
            }
            $customConfig = parse_ini_file($customConfigFile);

            $this->config = array_merge($defaultConfig, $customConfig);
        } else {
            $this->config = $defaultConfig;
        }

        $this->init = true;

        return $this;
    }

    public function __get($name)
    {
        if (!$this->init) {
            $this->initialize();
        }

        return !empty($this->config[$name]) ? $this->config[$name] : null;
    }

    public static function get($name)
    {
        return self::getInstance()->$name;
    }

    public function __set($name, $value)
    {
        throw new BonsaiException("Configuration for " . self::PROJECT_NAME . " cannot be modified at runtime.");
    }

    /**
     * Private constructor to prevent creating a new instance via 'new'
     * @return void
     */
    private function __construct()
    {
        
    }

    /**
     * Private constructor to prevent creating a new instance via 'clone'
     * @return void
     */
    private function __clone()
    {
        
    }

    /**
     * Private constructor to prevent creating a new instance via 'unserialize'
     * @return void
     */
    private function __wakeup()
    {
        
    }

}
