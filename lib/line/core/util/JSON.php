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
 * JSON Util
 * @class JSON
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @package line\core\util
 */
class JSON extends LinePHP
{

    private function __construct()
    {
    }

    public static function stringify($data, array $ignoreVars = null, $resolveNull = true)
    {
        if (is_null($ignoreVars)) {
            $ignoreVars = array();
        }
        $arr = self::getArrayData($data, $ignoreVars, $resolveNull);
        return json_encode($arr);
    }

    private static function getArrayData($data, $ignoreVars, $resolveNull, $subVar = '')
    {
        if (is_array($data)) {
            $tmp = array();
            foreach ($data as $key => $value) {
                if (!in_array($subVar . $key, $ignoreVars) && ($resolveNull || !is_null($value))) {
                    $tmp[$key] = self::getArrayData($value, $ignoreVars, $resolveNull, $key . '.');
                }
            }
        } else if ($data instanceof Map) {
            $tmp = array();
            while ($entry = $data->entry()) {
                if (!in_array($subVar . $entry->key, $ignoreVars) && ($resolveNull || !is_null($entry->value))) {
                    $tmp[$entry->key] = self::getArrayData($entry->value, $ignoreVars, $resolveNull, $entry->key . '.');
                }
            }
        } else if (is_object($data)) {
            $tmp = array();
            $methods = get_class_methods($data);
            foreach ($methods as $method) {
                if (StringUtil::startsWith('get', $method)) {
                    $key = lcfirst(substr($method, 3));
                    if (!in_array($subVar . $key, $ignoreVars)) {
                        $val = $data->$method();
                        if ($resolveNull || !is_null($val)) {
                            $tmp[$key] = self::getArrayData($val, $ignoreVars, $resolveNull, $key . '.');
                        }
                    }
                }
            }
        }
        return isset($tmp) ? $tmp : $data;
    }

    public static function parse($string, $class = null)
    {
        $obj = json_decode($string);
        return self::parseAll($obj, $class);
    }

    private static function parseAll($obj, $class) {
        if (is_null($class)) {
            return $obj;
        } else if (is_object($class)
            || (is_string($class) && class_exists($class) && $class = new $class)) {
            $methods = get_class_methods($class);
            foreach ($methods as $method) {
                if (StringUtil::startsWith('set', $method)) {
                    $var = lcfirst(substr($method, 3));
                    if (isset($obj->$var)) {
                        $rp = new \ReflectionParameter(array($class, $method), 0);
                        $type = $rp->getClass();
                        if (!is_null($type)) {
                            $value = self::parseAll($obj->$var, $type->name);
                        } else {
                            $value = $obj->$var;
                        }
                        $class->$method($value);
                    }
                }
            }
            return $class;
        } else {
            return null;
        }
    }
}