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
use line\core\Config;
use line\core\util\StringUtil;
use line\core\template\exception\ExpressionException;

/**
 * Arithmetic operations class
 * @class Math
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package line\core\util
 */
class Math extends LinePHP
{

    private static $arithmetics = array("+", "-", "*", "/", "%", "(", ")");
    private static $logics = array("!", "&&", "||", "(", ")");
    private static $gt = ">";
    private static $gte = ">=";
    private static $lt = "<";
    private static $lte = "<=";
    private static $eq = "=";
    private static $ueq = "<>";

    public static function calculate($expression)
    {
        if (!is_string($expression)) {
            return false;
        }
        preg_match("/^([^<>=]*)(<?>?=?)([^<>=]*)$/", $expression, $ari);
        if (count($ari) == 4) {
            if (empty($ari[2]) || empty($ari[3])) {
                return self::parseArithmetic($expression);
            } else {
                $left = self::parseArithmetic($ari[1]);
                $right = self::parseArithmetic($ari[3]);
                switch ($ari[2]) {
                    case self::$gt:
                        return $left > $right;
                        break;
                    case self::$gte:
                        return $left >= $right;
                        break;
                    case self::$lt:
                        return $left < $right;
                        break;
                    case self::$lte:
                        return $left <= $right;
                        break;
                    case self::$eq:
                        return $left == $right;
                        break;
                    case self::$ueq:
                        return $left != $right;
                        break;
                }
            }
        } else {
            throw new ExpressionException(StringUtil::systemFormat(Config::$LP_LANG['expression_exception'], $expression), 500);
        }
        //return self::parseArithmetic($expression);
    }

    public static function transform($expression)
    {
        if (!is_string($expression)) {
            return false;
        }
        if (is_numeric($expression)) {
            return intval($expression) > 0 ? true : false;
        }
        return self::parseLogic($expression);
    }

    private static function parseArithmetic($expression)
    {
        if (is_numeric($expression)) {
            return intval($expression);
        }
        $symbol = array();
        $final = array();
        $tmp = '';
        for ($i = 0; $i < strlen($expression); $i++) {
            $v = $expression{$i};
            if (in_array($v, self::$arithmetics)) {
                if (in_array($v, array('-', '+')) && (($i == 0) || (in_array($expression{$i - 1}, self::$arithmetics) && $expression{$i - 1} != ')'))) {
                    $tmp .= $v;
                    continue;
                } else {
                    ($tmp != '') && ($final[] = $tmp);
                    $tmp = '';
                }
                if ($v == ')') {
                    $p = array_pop($symbol);
                    while (isset($p) && $p != '(') {
                        $final[] = $p;
                        $p = array_pop($symbol);
                    }
                } elseif ($v == '(') {
                    $symbol[] = $v;
                } else {
                    $p = array_pop($symbol);
                    if (self::precedence($p, $v)) {
                        $final[] = $p;
                    } else {
                        $symbol[] = $p;
                    }
                    $symbol[] = $v;
                }
            } else {
                $tmp .= $v;
            }
        }
        ($tmp != '') && ($final[] = $tmp);
        while ($p = array_pop($symbol)) {
            $final[] = $p;
        }
        self::computeArithmetic($final, $value);
        return $value;
    }

    private static function precedence($top, $exp)
    {
        if (!isset($top) || $top == '(')
            return false;
        $plusminus = array('-', '+');
        $tLevel = in_array($top, $plusminus) ? 1 : 2;
        $eLevel = in_array($exp, $plusminus) ? 1 : 2;
        if ($eLevel <= $tLevel) {//pull
            return true;
        } else {//push
            return false;
        }
    }

    private static function computeArithmetic($stack, &$value)
    {
        $ar = array_slice(self::$arithmetics, 0, 5);
        for ($i = 0; $i < count($stack); $i++) {
            $c = $stack[$i];
            if (in_array($c, $ar)) {
                if (!is_numeric($stack[$i - 1]) || !is_numeric($stack[$i - 2])) {
                    throw new ExpressionException(Config::$LP_LANG['expression_exception_nan'], 500);
                }
                $n1 = doubleval($stack[$i - 1]);
                $n2 = doubleval($stack[$i - 2]);
                $v = 0;
                switch ($c) {
                    case '+':
                        $v = $n1 + $n2;
                        break;
                    case '-':
                        $v = $n2 - $n1;
                        break;
                    case '*':
                        $v = $n1 * $n2;
                        break;
                    case '/':
                        if ($n1 == 0) {
                            throw new ExpressionException(Config::$LP_LANG['expression_exception_nan'], 500);
                        }
                        $v = $n2 / $n1;
                        break;
                    case '%':
                        $v = $n2 % $n1;
                        break;
                }
                array_splice($stack, $i - 2, 3, $v);
                break;
            }
        }
        if (count($stack) == 1) {
            $value = $stack[0];
        } else {
            self::computeArithmetic($stack, $value);
        }
    }

    private static function parseLogic($expression)
    {
        $symbol = array();
        $final = array();
        $tmp = '';
        for ($i = 0; $i < strlen($expression); $i++) {
            $v = $expression{$i};
            if ($v == '&' || $v == '|') {
                $v .= $v;
                $i++;
            }
            if (in_array($v, self::$logics)) {
                ($tmp != '') && ($final[] = $tmp);
                ($tmp = '');
                if ($v == ')') {
                    $p = array_pop($symbol);
                    while (isset($p) && $p != '(') {
                        $final[] = $p;
                        $p = array_pop($symbol);
                    }
                } elseif ($v == '(') {
                    $symbol[] = $v;
                } else {
                    $p = array_pop($symbol);
                    if (self::precedence($p, $v)) {
                        $final[] = $p;
                    } else {
                        $symbol[] = $p;
                    }
                    $symbol[] = $v;
                }
            } else {
                $tmp .= $v;
            }
        }
        ($tmp != '') && ($final[] = $tmp);
        while ($p = array_pop($symbol)) {
            $final[] = $p;
        }
        self::computeLogic($final, $value);
        return $value;
    }

    private static function computeLogic($stack, &$value)
    {
        for ($i = 1; $i < count($stack); $i++) {
            $c = $stack[$i];
            $prev = $stack[$i - 1];
            if (preg_match("/[-<>=%+*\/]/", $prev)) {
                $prev = self::calculate($prev);
            }
            self::checkLogic($prev);
            $n1 = $i > 0 && $prev ? true : false;
            if ($c === '&&' || $c === '||') {
                $next = $stack[$i - 2];
                if (preg_match("/[-<>=%+*\/]/", $next)) {
                    $next = self::calculate($next);
                }
                self::checkLogic($next);
                $n2 = $next ? true : false;
                $v = false;
                switch ($c) {
                    case '&&':
                        $v = $n1 && $n2;
                        break;
                    case '||':
                        $v = $n1 || $n2;
                        break;
                }
                array_splice($stack, $i - 2, 3, $v);
            } elseif ($c === '!') {
                $v = !$n1;
                array_splice($stack, $i - 1, 2, $v);
            }
        }
        if (count($stack) == 1) {
            $value = $stack[0];
        } else {
            self::computeLogic($stack, $value);
        }
    }

    private static function checkLogic(&$string)
    {
        if (is_bool($string)) {
            $true = true;
        } elseif (is_numeric($string)) {
            $string = intval($string) > 0 ? true : false;
            $true = true;
        } elseif (strcasecmp($string, 'true') == 0) {
            $string = true;
            $true = true;
        } elseif (strcasecmp($string, 'false') == 0) {
            $string = false;
            $true = true;
        }
        if (isset($true)) {
            return $true;
        }
        throw new ExpressionException(Config::$LP_LANG['expression_exception_logic'], 500);
    }

}
