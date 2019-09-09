<?php

/*
 *  LinePHP Framework ( http://linephp.com )
 *  
 *                                 THE LICENSE
 * ==========================================================================
 * Copyright (c) 2014 LinePHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ==========================================================================
 */

namespace line\core;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ConfigConst.php';

use line\core\util\StringUtil;
use line\core\exception\SystemFileNotFoundException;
use line\core\exception\InvalidRequestException;
use line\logger\Level;

/**
 * @class Config
 * @description Init LinePHP Framework system config .
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core
 */
class Config extends ConfigConst
{

    public static $LP_PATH = array();        //app path
    public static $LP_LOG = array();         //log
    public static $LP_SYS = array();         //system
    public static $LP_LANG = array();        //interior language
    public static $LP_DB = array();
    public static $LP_RPC = array();

    /**
     * 2014-04-11 session opened for default
     */
    public static function init()
    {
        session_start();
        //self::initDir();
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $logger = \line\logger\Logger::getInstance();
            switch ($errno) {
                case E_WARNING :
                case E_NOTICE :
                case E_CORE_WARNING :
                case E_COMPILE_WARNING :
                case E_USER_WARNING :
                case E_USER_NOTICE :
                case E_DEPRECATED :
                case E_USER_DEPRECATED :
                    $msg = "[WARNING]" . $errstr;
                    $logger->log(Level::SYSTEM, $msg, $errfile . ':' . $errline);
                    break;
                default :
                    $msg = "[ERROR]" . $errstr;
                    $logger->log(Level::SYSTEM, $msg, $errfile . ':' . $errline);
                    exit();
            }
        });
        define('LP_LANGUAGE', 'linephp_language');
        spl_autoload_register('line\core\Config::autoLoadClass', true, true);
        self::loadSystemLanguage();
        //self::console(Level::INFO, self::$LP_LANG['framework_init']);
        $configKeys = array('Sys', 'Path', 'Log', 'Database', 'RPC');
        $file = LP_CONF_PATH . 'sys.lpc';
        if (file_exists($file)) {
            $config = parse_ini_file($file, true);
            if (count($config) > 0) {
                foreach ($configKeys as $key) {
                    if (array_key_exists($key, $config)) {
                        call_user_func(array('self', 'init' . $key), $config[$key]);
                    } else {
                        call_user_func(array('self', 'init' . $key));
                    }
                }
            } else {
                self::defaultInit();
            }
        } else {
            self::defaultInit();
            $error = StringUtil::systemFormat(self::$LP_LANG['config_not_found'], $file);
            self::console(Level::WARN, $error);
        }
    }

    private static function defaultInit()
    {
        self::initSys();
        self::initPath();
        self::initLog();
        self::initDatabase();
        self::initRPC();
    }

    private static function initSys($config = null)
    {
        //default
        self::$LP_SYS = array(
            self::SYS_MODE => self::SYS_MODE_DEVELOPMENT,
            self::SYS_LANGUAGE => self::SYS_LANGUAGE_DEFAULT,
            self::SYS_LANGUAGE_MULTIPLE => self::SYS_LANGUAGE_MULTIPLE_ON,
            self::SYS_ENCODE => self::SYS_ENCODE_DEFAULT,
            self::SYS_CLASS_PREFIX => '',
            self::SYS_CLASS_SUFFIX => '',
            self::SYS_METHOD_PREFIX => '',
            self::SYS_METHOD_SUFFIX => '',
            self::SYS_TEMPLATE_DIR => self::SYS_TEMPLATE_DIR_DEFAULT
        );
        if (!is_array($config))
            return;
        $keys = array_keys(self::$LP_SYS);
        //custom
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = $config[$key];
                if ($custom) {
                    self::$LP_SYS[$key] = $custom;
                }
            }
        }
        if (strcasecmp(self::$LP_SYS[self::SYS_MODE], self::SYS_MODE_DEVELOPMENT) == 0) {
            self::$LP_LOG[self::LOG_APPENDER] = self::LOG_APPENDER_CONSOLE;
        } else {
            self::$LP_LOG[self::LOG_APPENDER] = self::LOG_APPENDER_FILE;
        }
        //multi-language,need Session.
        if (array_key_exists(LP_LANGUAGE, $_SESSION)) {
            self::$LP_SYS[self::SYS_LANGUAGE] = $_SESSION[LP_LANGUAGE];
        }
    }

    private static function initPath($config = null)
    {
        //default
        self::$LP_PATH = array(
            self::PATH_APP => self::PATH_APP_DEFAULT,
            self::PATH_PAGE => self::PATH_PAGE_DEFAULT
        );
        if (!is_array($config))
            return;
        //custom
        if (array_key_exists(self::PATH_APP, $config) && isset($config[self::PATH_APP])) {
            $custom = realpath($config[self::PATH_APP]);
            if ($custom) {
                self::$LP_PATH[self::PATH_APP] = $custom;
            }
        }

        if (array_key_exists(self::PATH_PAGE, $config) && isset($config[self::PATH_PAGE])) {
            $custom = realpath($config[self::PATH_PAGE]);
            if ($custom) {
                self::$LP_PATH[self::PATH_PAGE] = $custom;
            }
        }

    }

    private static function initLog($config = null)
    {
        //default
        self::$LP_LOG[self::LOG_LEVEL] = self::LOG_LEVEL_DEBUG;
        self::$LP_LOG[self::LOG_LAYOUT] = self::LOG_LAYOUT_DEFAULT;
        self::$LP_LOG[self::LOG_FILE] = self::LOG_FILE_DEFAULT;
        self::$LP_LOG[self::LOG_FILE_PATTERN] = self::LOG_FILE_PATTERN_DEFAULT;
        //self::LOG_LOGGER => self::LOG_LOGGER_OFF,
        //self::LOG_APPENDER => self::LOG_APPENDER_FILE,
        if (!is_array($config))
            return;
        $keys = array_keys(self::$LP_LOG);
        //custom
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = $config[$key];
                if ($custom) {
                    self::$LP_LOG[$key] = $custom;
                }
            }
        }
        if (strcasecmp(self::$LP_SYS[self::SYS_MODE], self::SYS_MODE_PRODUCTION) === 0) {
            self::$LP_LOG[self::LOG_APPENDER] = self::LOG_APPENDER_FILE;
        }
    }

    /**
     * 2015-02-09 add dsn property
     * @param array $config
     * @return void
     */
    private static function initDatabase($config = null)
    {
        self::$LP_DB = array(
            self::DB_DRIVER => '',
            self::DB_TYPE => '',
            self::DB_HOST => '',
            self::DB_PORT => '',
            self::DB_NAME => '',
            self::DB_USER => '',
            self::DB_PASSWORD => '',
            self::DB_CHARSET => '',
            self::DB_DSN => ''
        );
        if (!is_array($config))
            return;
        $keys = array_keys(self::$LP_DB);
        $isError = false;
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = $config[$key];
                if (isset($custom)) {
                    self::$LP_DB[$key] = $custom;
                } else {
                    $isError = true;
                    break;
                }
            } else {
                $isError = true;
            }
        }
    }

    private static function initRPC($config = null)
    {
        self::$LP_RPC = array(
            self::RPC_LIB => '',
            self::RPC_PATH => ''
        );
        if (!is_array($config))
            return;
        if (array_key_exists(self::RPC_LIB, $config) && isset($config[self::RPC_LIB])) {
            self::$LP_RPC[self::RPC_LIB] = $config[self::RPC_LIB] . LP_DS;
        }

        if (array_key_exists(self::RPC_PATH, $config) && isset($config[self::RPC_PATH])) {
            $path = str_replace(array('/', '\\'), '', $config[self::RPC_PATH]);
            $path .= '/';
            self::$LP_RPC[self::RPC_PATH] = $path;
        }
    }

    private static function loadSystemLanguage()
    {
        $local = 'ZH';
        if (file_exists(LP_CORE_PATH . 'system.lang')) {
            $system = parse_ini_file('system.lang', true);
            if (count($system) > 0) {
                if (array_key_exists($local, $system)) {
                    self::$LP_LANG = $system[$local];
                    return;
                }
            }
        }
        throw new SystemFileNotFoundException('Loaded file "system.lang" error!', ERROR_500);
    }

    private static function autoLoadClass($class)
    {
        if (strpos($class, '\\') === false) {
            $source = LP_CORE_ABSTRACT . "{$class}.php";
        } else {
            $source = LP_LIBRARY_PATH . "{$class}.php";
        }
        $source = str_replace("\\", "/", $source);
        if (is_file($source)) {
            require_once($source);
        } else {
            throw new InvalidRequestException(Config::$LP_LANG['bad_request'] . ":{$class}", ERROR_400);
        }
    }

    /**
     * check whether Session is started
     * @return boolean Started return true or false。
     */
    protected static function isSessionStarted()
    {
        if (function_exists("session_status") === true) {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
    }

}
