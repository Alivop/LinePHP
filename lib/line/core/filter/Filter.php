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
namespace line\core\filter;

use line\core\LinePHP;
use line\core\Config;
use line\core\exception\InvalidRequestException;

/**
 *  请求过滤。检查请求是否正确。
 * @class Filter
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\filter
 */
class Filter extends LinePHP
{
    /**
     * 2014-05-21 重新定义获取ROOT文件夹，不同服务器下获取的目录分隔符不一样，如apache:/,windows:\ 
     * 2014-06-07 考虑到子目录情况，重新获取文件的相对URL
     * 2014-06-17 修正检查php文件的路径
     * @throws InvalidRequestException
     */
    public static function checkURL()
    {
        $dir = self::getMainDir(); //2014-06-07
        $file = $dir . self::filterURL();
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (strcasecmp($ext, 'php') == 0) {
            $file = substr($file, 1); //2014-06-17
            if (is_file($file)) {
                require_once $file;
                exit(0);
            } else {
                //self::console(Config::ERROR, Config::$LP_LANG['bad_request']);
                throw new InvalidRequestException(Config::$LP_LANG['bad_request'] . $file);
            }
        }
        //前端使用的路径前缀
        define('LP', $dir);
        define('LP_A', $dir . Config::$LP_PATH[Config::PATH_APP] . '/');
        define('LP_P', $dir . Config::$LP_PATH[Config::PATH_PAGE] . '/');
        define('LP_T', $dir . Config::$LP_PATH[Config::PATH_PAGE] . '/' . Config::$LP_SYS[Config::SYS_TEMPLATE_DIR] . '/');
    }

    /**
     * 2014-05-21 更正获取不包含querystring的URL
     * 2014-06-07 过滤子目录
     * @param string $url
     * @return string
     */
    public static function filterURL()
    {
        $url = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $urlArray = explode("?", $url);
        if (count($urlArray) > 0)
            $url = $urlArray[0];
        //if ($subPath == '/')
        if (!isset($url) || strcmp("//", $url) == 0 || strcmp("///", $url) == 0)
            $url = "/";
        $dir = self::getMainDir();
        if ($dir == '')
            return $url;
        else
            return str_replace($dir, "", $url);
    }

    /**
     * 2014-05-13 对所有提交的参数进行编码和过滤。
     * 2014-07-30 change htmlspecialchars() parameter
     */
    public static function filterParameter(&$param)
    {
        if (is_array($param)) {
            foreach ($param as &$value)
                self::filterParameter($value);
        } else {
            //过滤HTML代码
            $param = htmlspecialchars($param, ENT_QUOTES | ENT_IGNORE);//2014-07-30 change flags parameter ENT_XHTML(PHP5.4) to ENT_IGNORE
            //过滤SQL特殊字符和通配符
            //$param = str_replace(array('\\', "'", '"', '_', "%", '[', ']', '[!', '[!'), array('\\\\', "\'", '\"','\_', '\%', '\[', '\]', '\[', '\[!'), $param);
            //过滤SQL关键词
            $param = str_ireplace(array('select', 'insert', 'update', 'delete', 'drop', 'create'), array('s elect', 'i nsert', 'u pdate', 'd elete', 'd rop', 'c reate'), $param);
        }
    }

    /**
     * 2014-06-07 增加获取子目录方法（如果框架是在网站子目录下）。
     * @return string
     */
    private static function getMainDir()
    {
        $absolute = str_replace("\\", "/", filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
        $relative = str_replace("\\", "/", filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
        $root = str_replace("\\", "/", LP_ROOT);
        $mainFile = str_replace($root, "", $absolute);
        $mainDir = str_replace($mainFile, "", $relative);
        if ($mainDir != '/')
            return substr($mainDir, 1);
        else
            return '';
    }

}
