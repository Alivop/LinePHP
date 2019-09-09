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

/**
 * @class ObjectUtil
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @package line\core\util
 */
class ObjectUtil
{

    public static function copyProperty($source, $target) {
        if (!is_object($source)) {
            return null;
        } else if (is_object($target)
            || (is_string($target) && class_exists($target) && $target = new $target)) {
            $methods = get_class_methods($target);
            foreach ($methods as $method) {
                if (StringUtil::startsWith('set', $method)) {
                    $var = lcfirst(substr($method, 3));
                    if (isset($source->$var)) {
                        $rp = new \ReflectionParameter(array($target, $method), 0);
                        $type = $rp->getClass();
                        if (!is_null($type)) {
                            $value = self::copyProperty($source->$var, $type->name);
                        } else {
                            $value = $source->$var;
                        }
                        $target->$method($value);
                    }
                }
            }
            return $target;
        } else {
            return null;
        }
    }
}