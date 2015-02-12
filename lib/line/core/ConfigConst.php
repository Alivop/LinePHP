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

require_once __DIR__ . DIRECTORY_SEPARATOR . 'LinePHP.php';

/**
 * 
 * @class ConfigConst
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core
 */
class ConfigConst extends LinePHP
{

    //system config
    const SYS_MODE = 'mode';
    const SYS_LANGUAGE = 'language_default';
    const SYS_LANGUAGE_MULTIPLE = 'language_multiple';
    const SYS_ENCODE = 'encode';
    const SYS_CLASS_PREFIX = 'class_prefix';
    const SYS_CLASS_SUFFIX = 'class_suffix';
    const SYS_METHOD_PREFIX = 'method_prefix';
    const SYS_METHOD_SUFFIX = 'method_suffix';
    const SYS_TEMPLATE_DIR = 'template_dir';
    const SYS_MODE_DEVELOPMENT = 'development';
    const SYS_MODE_PRODUCTION = 'production';
    const SYS_LANGUAGE_DEFAULT = 'zh-CN';
    const SYS_LANGUAGE_MULTIPLE_ON = 'ON';
    const SYS_LANGUAGE_MULTIPLE_OFF = 'OFF';
    const SYS_ENCODE_DEFAULT = 'UTF-8';
    const SYS_TEMPLATE_DIR_DEFAULT = 'default';
    //path config
    const PATH_APP = 'app';
    const PATH_PAGE = 'page';
    const PATH_APP_DEFAULT = 'application';
    const PATH_PAGE_DEFAULT = 'page';
    //logger cofig
    const LOG_LOGGER = 'logger';
    const LOG_LEVEL = 'level';
    const LOG_APPENDER = 'appender';
    const LOG_LAYOUT = 'layout';
    const LOG_FILE = 'file';
    const LOG_FIEL_PATTERN = 'file_pattern';
    const LOG_LOGGER_ON = 'ON';
    const LOG_LOGGER_OFF = 'OFF';
    const LOG_LEVEL_OFF = 'OFF';
    const LOG_LEVEL_DEBUG = 'DEBUG';
    const LOG_LEVEL_INFO = 'INFO';
    const LOG_LEVEL_WARN = 'WARN';
    const LOG_LEVEL_ERROR = 'ERROR';
    const LOG_APPENDER_FILE = 'FileAppender';
    const LOG_APPENDER_CONSOLE = 'ConsoleAppender';
    const LOG_LAYOUT_DEFAULT = '[Y-m-d H:i:s,B]';
    const LOG_FILE_DEFAULE = 'linephp.log';
    const LOG_FIEL_PATTERN_DEFAULT = '.Y-m-d';
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    //database config
    const DB_DRIVER = 'driver';
    const DB_TYPE = 'type';
    const DB_HOST = 'host';
    const DB_PORT = 'port';
    const DB_NAME = 'name';
    const DB_USER = 'user';
    const DB_CHARSET = 'charset';
    const DB_PASSWORD = 'password';
    const DB_DRIVER_DEFAULT = 'Mysqli';
    const DB_TYPE_DEFAULT = 'mysql';
    const DB_HOST_DEFAULT = '127.0.0.1';
    const DB_PORT_DEFAULT = '3306';
    const DB_DSN = 'dsn';

}
