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

/**
 * The super class of all framework class
 * @class LinePHP
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core
 */
class LinePHP
{

    private static $container;

    public function __call($name, $arguments)
    {
        $message = ' method [' . __CLASS__ . '->' . $name . '()] is inaccessible .';
        throw new \Exception('NoSuchMethod:' . $message, ERROR_500);
    }

    public static function __callStatic($name, $arguments)
    {
        $message = ' static method [' . get_called_class() . '::' . $name . '()] is inaccessible .';
        throw new \Exception('NoSuchMethod:' . $message, ERROR_500);
    }

    public function __toString()
    {
        return '[Object]' . __CLASS__;
    }

    /**
     * 系统日志
     * @param string $type
     * @param string $message
     * @return void
     */
    protected static function console($type, $message)
    {
        if (!isset(self::$container)) {
            self::$container = array();
        }
        if (key_exists(Config::LOG_APPENDER, Config::$LP_LOG)) {
            if (key_exists(Config::LOG_LEVEL, Config::$LP_LOG) &&
                    strcasecmp(Config::$LP_LOG[Config::LOG_LEVEL], Config::LOG_LEVEL_OFF) != 0) {
                $logger = \line\logger\Logger::getInstance();
                foreach (self::$container as $console) {
                    //$console = $iterator->next();
                    $logger->log($console[0], $console[1]);
                }
                array_splice(self::$container,0);
                $logger->log($type, $message);
            } else {
                return;
            }
        } else {
            self::$container[] = (array($type, $message));
        }
    }

    protected function compare($ele1, $ele2)
    {
        if (is_object($ele1) && is_object($ele2)) {
            if ($ele1 == $ele2) {
                return true;
            }
        } else {
            if ($ele1 === $ele2) {
                return true;
            }
        }
        return false;
    }

}
