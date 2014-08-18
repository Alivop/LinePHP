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

/**
 * Levels used for logging.The priority from most specific to least : FATAL,ERROR,WARN,INFO,DEBUG
 * 
 * @class Level
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package line\logger
 */
final class Level
{
    const OFF = 0;
    const FATAL = 10;
    const ERROR = 20;
    const WARN = 30;
    const INFO = 40;
    const DEBUG = 50;
    const SYSTEM = 1;

    public function __toString()
    {
        return "logger levels";
    }

    public static function intValue($stringLevel)
    {
        if (!empty($stringLevel)) {
            if (strcasecmp($stringLevel, "OFF") === 0) {
                return 0;
            } elseif (strcasecmp($stringLevel, "FATAL") === 0) {
                return 10;
            } elseif (strcasecmp($stringLevel, "ERROR") === 0) {
                return 20;
            } elseif (strcasecmp($stringLevel, "WARN") === 0) {
                return 30;
            } elseif (strcasecmp($stringLevel, "INFO") === 0) {
                return 40;
            } elseif (strcasecmp($stringLevel, "DEBUG") === 0) {
                return 50;
            } elseif (strcasecmp($stringLevel, "SYSTEM") === 0) {
                return 1;
            }
        }
        return 0;
    }

    public static function stringValue($intLevel)
    {
        switch ($intLevel) {
            case 0:
                return "OFF";
            case 10:
                return "FATAL";
            case 20:
                return "ERROR";
            case 30:
                return "WARN";
            case 40:
                return "INFO";
            case 50:
                return "DEBUG";
            case 1:
                return "SYSTEM";
        }
    }

}
