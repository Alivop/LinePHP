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

    /**
     * 2014-04-11 session opened for default
     */
    public static function init()
    {
        session_start();
        self::initDir();
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
                    $msg = "<span style='font-weight:bold'>WARNING:</span><span style='color:#CD0000'>[" . $errfile .
                            "(line:$errline)]</span>" . $errstr;
                    $logger->log(Level::SYSTEM, $msg);
                    break;
                default :
                    $msg = "<span style='font-weight:bold'>ERROR   :</span><span style='color:#CD0000;font-weight:bold'>[" . $errfile
                            . "(line:$errline)]" . $errstr . "</span>";
                    $logger->log(Level::SYSTEM, $msg);
                    exit();
            }
        });
        define('LP_LANGUAGE', 'linephp_language');
        spl_autoload_register('line\core\Config::autoLoadClass', true, true);
        self::loadSystemLanguage();
        //self::console(Level::INFO, self::$LP_LANG['framework_init']);
        $configKeys = array('Sys', 'Path', 'Log', 'Database');
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
                call_user_func(array('self', 'init' . $key));
            }
        } else {
            self::initSys();
            self::initPath();
            self::initLog();
            self::initDB();
            $error = StringUtil::systemFormat(self::$LP_LANG['config_not_found'], $file);
            self::console(Level::WARN, $error);
        }
    }

    private static function initDir()
    {
        define('LP_DS', DIRECTORY_SEPARATOR);
        define('LP_CORE_PATH', __DIR__ . LP_DS);
        define('LP_PATH', dirname(LP_CORE_PATH) . LP_DS);
        define('LP_LIBRARY_PATH', dirname(LP_PATH) . LP_DS);
        define('LP_ROOT', dirname(LP_LIBRARY_PATH) . LP_DS);
        define('LP_CONF_PATH', LP_PATH . 'conf' . LP_DS);
        define('LP_LANG_PATH', LP_PATH . 'i18n' . LP_DS);
        define('LP_IO_PATH', LP_PATH . 'io' . LP_DS);
        define('LP_DB_PATH', LP_PATH . 'db' . LP_DS);
        define('LP_LOG_PATH', LP_PATH . 'logger' . LP_DS);
        define('LP_CORE_CONF', LP_CORE_PATH . 'config' . LP_DS);
        define('LP_CORE_LINE', LP_CORE_PATH . 'linephp' . LP_DS);
        define('LP_CORE_ABSTRACT', LP_CORE_PATH . 'abstract' . LP_DS);
        define('LP_IO_LINE', LP_IO_PATH . 'linephp' . LP_DS);
        define('LP_DB_LINE', LP_DB_PATH . 'linephp' . LP_DS);
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
        $isError = false;
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = $config[$key];
                if ($custom) {
                    self::$LP_SYS[$key] = $custom;
                } else {
                    $isError = true;
                }
            } else {
                $isError = true;
            }
        }
        if (strcasecmp(self::$LP_SYS[self::SYS_MODE], self::SYS_MODE_DEVELOPMENT) == 0) {
            self::$LP_LOG[self::LOG_APPENDER] = self::LOG_APPENDER_CONSOLE;
        } else {
            self::$LP_LOG[self::LOG_APPENDER] = self::LOG_APPENDER_FILE;
        }
        //multi-language,need Session.
        if (strcasecmp(self::$LP_SYS[self::SYS_LANGUAGE_MULTIPLE], self::SYS_LANGUAGE_MULTIPLE_ON) === 0) {
            if (!self::isSessionStarted()) {
                session_start();
            }
            if (array_key_exists(LP_LANGUAGE, $_SESSION)) {
                self::$LP_SYS[self::SYS_LANGUAGE] = $_SESSION[LP_LANGUAGE];
            }
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
        $keys = array_keys(self::$LP_PATH);
        //custom
        $isError = false;
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = realpath($config[$key]);
                if ($custom) {
                    self::$LP_PATH[$key] = $custom;
                } else {
                    $isError = true;
                }
            } else {
                $isError = true;
            }
        }
    }

    private static function initLog($config = null)
    {
        //default
        self::$LP_LOG[self::LOG_LEVEL] = self::LOG_LEVEL_DEBUG;
        self::$LP_LOG[self::LOG_LAYOUT] = self::LOG_LAYOUT_DEFAULT;
        self::$LP_LOG[self::LOG_FILE] = self::LOG_FILE_DEFAULE;
        self::$LP_LOG[self::LOG_FIEL_PATTERN] = self::LOG_FIEL_PATTERN_DEFAULT;
        //self::LOG_LOGGER => self::LOG_LOGGER_OFF,
        //self::LOG_APPENDER => self::LOG_APPENDER_FILE,
        if (!is_array($config))
            return;
        $keys = array_keys(self::$LP_LOG);
        //custom
        $isError = false;
        foreach ($keys as $key) {
            if (array_key_exists($key, $config)) {
                $custom = $config[$key];
                if ($custom) {
                    self::$LP_LOG[$key] = $custom;
                } else {
                    $isError = true;
                }
            } else {
                $isError = true;
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
        throw new SystemFileNotFoundException('Loaded file "system.lang" error!', 500);
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
            //echo $source;
            $message = StringUtil::systemFormat(self::$LP_LANG['file_not_exist'], $source);
            self::Level(self::ERROR, $message);
            throw new InvalidRequestException(Config::$LP_LANG['bad_request'] . ":{$class}", 400);
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
