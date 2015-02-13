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
    private static $dsn;

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
            self::$dsn = Config::$LP_DB[Config::DB_DSN];
            self::$isInit = true;
        }
    }

    /**
     * get database connection
     * 2015-02-09 add $dsn param
     *            use array param,the first param $host can be an array,then all params are in the array.
     * @param mixed $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param string $type
     * @param string $driver
     * @param string $charset
     * @param string $dsn
     * @return object
     */
    public static function getConnection($host = '', $port = '', $user = '', $password = '', $dbname = '', $type = '', $driver = '', $charset = '', $dsn = '')
    {
        self::init();
        if (is_array($host)) {
            $params = $host;
            $host = isset($params[Config::DB_HOST]) ? $params[Config::DB_HOST] : "";
            $port = isset($params[Config::DB_PORT]) ? $params[Config::DB_PORT] : "";
            $user = isset($params[Config::DB_USER]) ? $params[Config::DB_USER] : "";
            $password = isset($params[Config::DB_PASSWORD]) ? $params[Config::DB_PASSWORD] : "";
            $dbname = isset($params[Config::DB_NAME]) ? $params[Config::DB_NAME] : "";
            $type = isset($params[Config::DB_TYPE]) ? $params[Config::DB_TYPE] : "";
            $driver = isset($params[Config::DB_DRIVER]) ? $params[Config::DB_DRIVER] : "";
            $charset = isset($params[Config::DB_CHARSET]) ? $params[Config::DB_CHARSET] : "";
            $dsn = isset($params[Config::DB_DSN]) ? $params[Config::DB_DSN] : "";
        }
        $host = empty($host) ? self::$host : $host;
        $port = empty($port) && empty($driver) ? self::$port : $port;
        $user = empty($user) ? self::$user : $user;
        $password = empty($password) ? self::$password : $password;
        $dbname = empty($dbname) ? self::$name : $dbname;
        $type = empty($type) ? self::$type : $type;
        $driver = empty($driver) ? self::$driver : $driver;
        $charset = empty($charset) ? self::$charset : $charset;
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
                case 'odbc' :
                    if (!empty($dsn)) {
                        self::$conn = new conn\driver\ODBC($dsn, $user, $password);
                    }
                    if (odbc_error()) {
                        self::$error = odbc_errormsg();
                    }
                    break;
                case 'mssql' :
                    if (!empty($port))
                        $host = $host . ",$port";
                    self::$conn = new conn\driver\Mssql($host, $dbname, $user, $password);
                    if (($errors = sqlsrv_errors(SQLSRV_ERR_ERRORS) ) != null) {
                        foreach ($errors as $error) {
                            self::$error .= "code: " . $error['code'] . "<br />";
                            self::$error .= "message: " . $error['message'] . "<br />";
                        }
                    }
                    break;
            }
        }
        return self::$conn;
    }

}
