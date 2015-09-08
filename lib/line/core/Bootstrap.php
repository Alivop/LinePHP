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
/**
 * 框架入口文件
 * @class Bootstrap
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line
 */
class Bootstrap
{

    /**
     * start framework
     * @return void
     */
    public static function start()
    {
        try {
            self::init();
            self::run();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $logger = line\logger\Logger::getInstance();
            $logger->log(line\logger\Level::ERROR, $message);
            exit();
        }
    }

    /**
     * init framework
     * @return void
     */
    private static function init()
    {
        self::checkPHPVersion();
        self::checkUriExtension();
        self::initConfig();
        self::initLanguage();
    }

    /**
     * check PHP version
     * @return void
     */
    private static function checkPHPVersion()
    {
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $message = '<b>Your PHP Version:' . PHP_VERSION . ',LinePHP needed PHP 5.3.0 and above!</b>';
            exit($message);
        }
    }

    /**
     * validate uri
     * @return  void
     */
    private static function checkUriExtension()
    {
        $uriExt = pathinfo(filter_input(INPUT_SERVER, 'REQUEST_URI'), PATHINFO_EXTENSION);
        if (!empty($uriExt) && strcasecmp($uriExt, 'lang') == 0 && strcasecmp($uriExt, 'ini') == 0) {
            header('HTTP/1.1 404 Not Found');
            exit(1);
        }
    }

    /**
     * init parameters 
     * @return void
     */
    private static function initConfig()
    {
        //set timezone
        date_default_timezone_set('Asia/Shanghai');
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
        line\core\Config::init();
    }

    /**
     * load language file
     * @return void
     */
    private static function initLanguage()
    {
        line\core\Language::init();
    }

    /**
     * running framework
     * @return void
     */
    private static function run()
    {
        $request = new line\core\request\RequestHandler();
        $request->handler();
    }

    /**
     * system exception
     * @param int $code
     * @return void
     */
    private static function systemError($code)
    {
        switch ($code) {
            case 301:
                header('HTTP/1.1 301 Moved Permanently');
                break;
            case 400:
                header('HTTP/1.1 400 Bad Request');
                break;
            case 403:
                header('HTTP/1.1 403 Forbidden');
                break;
            case 404:
                header('HTTP/1.1 404 Not Found');
                break;
            case 500:
                header('HTTP/1.1 500 Internal Server Error');
                break;
            default:
                header('HTTP/1.1 404 Not Found');
        }
        exit(1);
    }

}
