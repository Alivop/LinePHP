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
namespace line\logger;

use line\core\Config;

/**
 * Common logger class.
 * @class Logger
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\logger
 */
final class Logger
{
    /** @var Logger $logger  The singleton object of Logger */
    private static $logger;

    private function __construct()
    {
        
    }

    final private function __clone()
    {
        
    }

    public static function getInstance()
    {
        if (!isset(self::$logger)) {
            $class = '\\line\\logger\\' . Config::$LP_LOG[Config::LOG_APPENDER];
            if (class_exists($class)) {
                if (strcasecmp(Config::$LP_LOG[Config::LOG_APPENDER], Config::LOG_APPENDER_CONSOLE) == 0) {
                    header("content-type:text/html;charset=".Config::$LP_SYS[Config::SYS_ENCODE]);
                }
                self::$logger = new $class;
            } else {
                self::$logger = new self;
            }
        }
        return self::$logger;
    }

    final public function debug($message)
    {
        
    }

    final public function info($message)
    {
        
    }

    final public function warn($message)
    {
        
    }

    final public function error($message)
    {
        
    }

    final public function log($type, $message)
    {
        
    }

}
