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

namespace line\db\conn\driver;

use line\db\DB;
use line\db\conn\result\PDOResult;
use line\db\conn\BaseConn;
use line\db\conn\statement\PDOStatement;

/**
 * PDO driver
 * @class PDO
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\driver
 */
class PDO extends BaseConn implements DB
{

    private $driver = array(
        "mysql" => "mysql:",
        "mssql" => "sqlsrv:",
        "sybase" => "sybase:",
        "oracle" => "oci:",
        "sqlite" => "sqlite:",
        "pgsql" => "pgsql:"
    );
    private $errorInfo;

    public function __construct($type, $host, $user, $password, $db, $port, $charset)
    {
        if (!isset($this->conn)) {
            $prefix = strtolower($type);
            $dsn = "";
            $usePort = "";
            if(!empty($port))  $usePort = ";port=$port";
            try {
                switch ($prefix) {
                    case "mysql" :
                        $dsn = "mysql:host=$host;$usePort;dbname=$db";
                        $this->conn = new \PDO($dsn, $user, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $charset));
                        break;
                    case "mssql" :
                        $msp = "";
                        if(!empty($port)) $msp = ",$port";
                        $dsn = "sqlsrv:Server=$host $msp;Database=$db";
                        $this->conn = new \PDO($dsn, $user, $password);
                        break;
                    case "sqlite" :
                        $dsn = "sqlite:".$host; //when the database is sqlite , this $host was value of sqlite file path.
                        $this->conn = new \PDO($dsn);
                        break;
                    case "pgsql" : 
                        $dsn = "pgsql:host=$host;$usePort;dbname=$db;user=$user;password=$password";
                        $this->conn = new \PDO($dsn);
                        break;
                    
                    default :
                        $this->errorInfo = "unsupport database.";
                        return $this;
                }
            } catch (\Exception $e) {
                $this->errorInfo = 'Connection failed: ' . $e->getMessage();
                return $this;
            }
        } 
    }

    public function autoCommit($autoCommit)
    {
        if (is_bool($autoCommit)) {
            try {
                $this->conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, $autoCommit);
            } catch (\Exception $e) {
                throw new \Exception(' DataBase autoCommit : ' . $e->getMessage());
            }
        }
    }

    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    public function close()
    {
        $this->conn = null;
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function prepare($sql)
    {
        if (!empty($sql))
            return new PDOStatement($this->conn->prepare($sql,array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY)), $sql);
        else
            return false;
    }

    public function query($sql)
    {
        if (!empty($sql)) {
            $stat = $this->conn->query($sql);
            if ($stat) {
                if ($stat->columnCount() <= 0) {
                    return $stat->rowCount() > 0;
                } else {
                    $rows = $stat->fetchAll(\PDO::FETCH_ASSOC);
                    $fields = isset($rows[0]) ? array_keys($rows[0]) : array();
                    $fieldsCount = $stat->columnCount();
                    $stat = null;
                    return new PDOResult($rows, $fieldsCount, count($rows), $fields);
                }
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    public function queryError()
    {
        $errorno = $this->conn->errorCode();
        if (!isset($errorno)||$errorno==\PDO::ERR_NONE)
            return null;
        $errorInfo = $this->conn->errorInfo();
        return 'SQLSTATE Error Code : ' . $errorno . ', DataBase Error Code : ' .$errorInfo[1].', Error Info : '.$errorInfo[2] ;
    }

    public function rollback()
    {
        return $this->conn->rollBack();
    }

    public function errorInfo()
    {
        return $this->errorInfo;
    }
    
    public function getInsertId()
    {
        return $this->conn->lastInsertId();
    }

}
