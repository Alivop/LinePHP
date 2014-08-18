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

    //const BASE_PATH = dirname(__DIR__).DIRECTORY_SEPARATOR;
    //const LIB_PATH  = __DIR__.DIRECTORY_SEPARATOR;
    //function __construct (){
    //	$this->init();
    //}
    /**
     * 启动运行框架
     * @return void
     */
    public static function start()
    {
        try {
            self::init();
            self::run();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
//            if (array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0])) {
//                $message = $trace[0]['file'] . "(line:" . $trace[0]['line'] . "):";
//            }else{
//                $message = $trace[1]['file'] . "(line:" . $trace[1]['line'] . "):";
//            }
            $message = $e->getMessage();
            $logger = line\logger\Logger::getInstance();
            $logger->log(line\logger\Level::ERROR, $message);
            exit();
        }
    }

    /**
     * 框架初始化
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
     * 检查PHP版本
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
     * 检测请求URI
     * @return  void
     */
    private static function checkUriExtension()
    {
        $uriExt = pathinfo(filter_input(INPUT_SERVER, 'REQUEST_URI'), PATHINFO_EXTENSION);
        if (!empty($uriExt) && strcasecmp($uriExt, 'lang') == 0 && strcasecmp($uriExt, 'ini') == 0) {
            //header("Location: /404.html");
            //include '404.html';
            header('HTTP/1.1 404 Not Found');
            exit(1);
        }
    }

    /**
     * 初始化参数
     * @return void
     */
    private static function initConfig()
    {
        //设置时区
        date_default_timezone_set('Asia/Shanghai');
        //设置lib库路径
        //set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(dirname(__DIR__))));
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
        line\core\Config::init();
    }

    /**
     * 加载语言文件
     * @return void
     */
    private static function initLanguage()
    {
        line\core\Language::init();
    }

    /**
     * 框架运行。
     * @return void
     */
    private static function run()
    {
        $request = new line\core\request\RequestHandler();
        $request->handler();
    }

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
