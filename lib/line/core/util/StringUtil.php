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
namespace line\core\util;

use line\core\LinePHP;

/**
 * @class StringUtil
 * @description Operations on string.
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\util
 */
class StringUtil extends LinePHP
{
    /**
     * LinePHP use this format.Replace more than one '{}' in your string.
     * @param string $string subject string
     * @param string $replace the replaced string
     * @return string 
     */
    public static function systemFormat($string, $replace)
    {
        $argsNum = func_num_args();
        if ($argsNum > 2) {
            $args = func_get_args();
            for ($i = 1; $i < $argsNum; $i++) {
                $string = preg_replace("/{}/", $args[$i], $string, 1);
            }
            return $string;
        } else {
            return str_replace("{}", $replace, $string);
        }
    }

    /**
     * Customize string format,to replace more than one pattern value.User can defined the search pattern.
     * @param string $pattern search pattern
     * @param string $string subject string
     * @param string $replace the replaced string
     * @return string
     */
    public static function format($pattern, $string, $replace)
    {
        $argsNum = func_num_args();
        $args = func_get_args();
        for ($i = 2; $i < $argsNum; $i++) {
            $string = preg_replace("/$pattern/", $args[$i], $string, 1);
        }
        return $string;
    }

    /**
     * 2014-04-10 更新W匹配-
     * 2014-04-11 更正中文匹配
     * 2014-04-15 更新可以自定义正则匹配
     * @param type $type
     * @param type $string
     * @param int $min
     * @param string $max
     * @return boolean
     */
    public static function validator($type, $string, $min = 0, $max = 0)
    {
        if (empty($min))
            $min = 1;
        if (empty($max))
            $max = '';
        switch ($type) {
            case 'CN': //匹配中文
                $reg = "/^[\x{4e00}-\x{9fa5}]{{$min},{$max}}$/u";
                break;
            case 'LN': //匹配字母和数字
                $reg = "/^[a-zA-Z0-9]{{$min},{$max}}/";
                break;
            case 'N': //匹配数字
                $reg = "/^\d{{$min},{$max}}$/";
                break;
            case 'L': //匹配字母
                $reg = "/^[a-zA-Z]{{$min},{$max}}$/";
                break;
            case 'W'://匹配字母，数字，下划线，-
                $reg = "/^[\w-]{{$min},{$max}}$/";
                break;
            case 'ZC'://匹配邮编
                $reg = '/^[1-9]\d{5}/';
                break;
            case 'MP'://匹配手机号码
                $reg = '/^(13|15|17|18|14)[0-9]{9}$/';
                break;
            case 'EM': //匹配邮箱
                $match = filter_var($string, FILTER_VALIDATE_EMAIL);
                if (strcmp($match, $string) === 0)
                    return true;
                return false;
            case 'ID': //匹配身份证
                $reg = '/^(\d{15})|\d{17}(\d|x)$/';
                break;
            case 'TP': //匹配固定电话.格式0551-00000000
                $reg = '/^(010-|02\d-|0\d{3}-)\d{8}$/';
                break;
            case 'IP': //匹配IP
                $match = filter_var($string, FILTER_VALIDATE_IP);
                if (strcmp($match, $string) === 0)
                    return true;
                return false;
            default :
                $reg = $type;
        }
        preg_match($reg, $string, $match);
        if (count($match) === 0)
            return false;
        if (strcmp($match[0], $string) === 0)
            return true;
        return false;
    }

}
