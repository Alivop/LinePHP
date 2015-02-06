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
namespace line\db;

use line\core\LinePHP;
use line\core\Config;

/**
 * 
 * @class DbFactory
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db
 */
class DbFactory extends LinePHP
{
    public static $error;
    private static $driver;
    private static $type;
    private static $host;
    private static $port;
    private static $name;
    private static $user;
    private static $password;
    private static $conn;
    private static $charset;
    private static $isInit;

    private static function init()
    {
        if (!self::$isInit) {
            self::$host = Config::$LP_DB[Config::DB_HOST];
            self::$port = Config::$LP_DB[Config::DB_PORT];
            self::$type = Config::$LP_DB[Config::DB_TYPE];
            self::$name = Config::$LP_DB[Config::DB_NAME];
            self::$user = Config::$LP_DB[Config::DB_USER];
            self::$password = Config::$LP_DB[Config::DB_PASSWORD];
            self::$driver = Config::$LP_DB[Config::DB_DRIVER];
            self::$charset = Config::$LP_DB[Config::DB_CHARSET];
            self::$isInit = true;
        }
    }

    /**
     * get database connection
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param string $type
     * @param string $driver
     * @param string $charset
     * @return object
     */
    public static function getConnection($host='',$port='',$user='',$password='',$dbname='',$type='',$driver='',$charset='')
    {
        self::init();
        $host = empty($host)?self::$host:$host;
        $port = empty($port)?self::$port:$port;
        $user = empty($user)?self::$user:$user;
        $password = empty($password)?self::$password:$password;
        $dbname = empty($dbname)?self::$name:$dbname;
        $type = empty($type)?self::$type:$type;
        $driver = empty($driver)?self::$driver:$driver;
        $charset = empty($charset)?self::$charset:$charset;
        if (!self::$conn) {
            $driver = strtolower($driver);
            switch ($driver) {
                case 'mysqli':
                    self::$conn = new conn\driver\Mysql($host, $user, $password, $dbname, $port, $charset);
                    self::$error = self::$conn->connectError();
                    break;
                case 'pdo' :
                    self::$conn = new conn\driver\PDO($type, $host, $user, $password, $dbname, $port, $charset);
                    self::$error = self::$conn->errorInfo();
                    break;
            }
        }
        return self::$conn;
    }

}
