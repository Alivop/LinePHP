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

namespace line\core\template;

use line\core\LinePHP;
use line\core\exception\ExpressionException;
use line\core\Config;
use line\core\util\Math;
use line\core\util\StringUtil;

/**
 * Calc expression in modual
 * @class Expression
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package line\core\template
 */
class Expression extends LinePHP
{

    private $expression = '';
    private $page;
    private $map;
    private $varCount;

    public function __construct($expression, $map, $page = '')
    {
        $expression = str_replace(" ", "", $expression);
        if (!self::check($expression)) {
            throw new ExpressionException($page . ':' . StringUtil::systemFormat(Config::$LP_LANG['expression_exception'], $expression), 500);
        }
        $this->expression = $expression;
        $this->map = $map;
        $this->page = $page;
        $this->varCount = 0;
    }

    /**
     * 字符串用+连接
     * @return boolean
     */
    public function parse()
    {
        $expression = $this->transform($this->page, $this->map);
        if (is_bool($expression)) {
            return $expression;
        } elseif (strcasecmp($expression, 'true') == 0) {
            return true;
        } elseif (strcasecmp($expression, 'false') == 0) {
            return false;
        }
        if (self::check($expression)&&$this->varCount>1) {
            if (preg_match("/!|&&|\|\|/", $expression)) {
                return $this->parseLogic($expression);
            } else {
                return $this->parseArithmetic($expression);
            }
        }
        return str_replace("+", "", $expression);
    }

    private function parseArithmetic($expression)
    {
        return Math::calculate($expression);
    }

    private function parseLogic($expression)
    {
        return Math::transform($expression);
    }

    private function transform($page, $map)
    {
        $count = &$this->varCount;
        $expression = preg_replace_callback('/[\w.]+/', function($matche) use ($page, $map,&$count) {
            if (isset($matche[0])) {
                $count++;
                $var = $matche[0];
                if (strcasecmp($var, 'true') == 0 || strcasecmp($var, 'false') == 0 || is_numeric($var)) {
                    $value = $var;
                } else {
                    $varObj = explode(".", $var);
                    $value = $map->get($varObj[0]);
                    if (is_null($value)) {
                        throw new ExpressionException($page . ':' . StringUtil::systemFormat(Config::$LP_LANG['unknown_variable'], $var), 500);
                    }
                    array_shift($varObj);
                    foreach ($varObj as $v) {
                        if (property_exists($value, $v)) {
                            $value = $value->$v;
                        } else if (method_exists($value, $v)) {
                            $value = $value->$v();
                        } else {
                            throw new ExpressionException($page . ':' . StringUtil::systemFormat(Config::$LP_LANG['unknown_variable'], $var), 500);
                        }
                    }
                }
                if ($value === true) {
                    $value = 'true';
                } elseif ($value === false) {
                    $value = 'false';
                }
                return $value;
            } else {
                throw new ExpressionException($page . ':' . StringUtil::systemFormat(Config::$LP_LANG['expression_exception'], $expression), 500);
            }
        }, $this->expression);
        return $expression;
    }

    public static function check($expression)
    {
        if (preg_match("/^[!(-+]?[a-zA-Z_0-9!&|<>=\-\(\)\.\+\*\/\%]+$/", $expression)) {
            return true;
        }
        return false;
    }

}
