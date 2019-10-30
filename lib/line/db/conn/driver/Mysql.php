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
use line\db\conn\result\MysqlResult;
use line\db\conn\BaseConn;
use line\db\conn\statement\MysqlStatement;

/**
 * 
 * @class Mysql
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db\driver
 */
class Mysql extends BaseConn implements DB
{
    public function __construct($host, $user, $password, $db, $port , $charset )
    {
        if (!isset($this->conn)) {
            $this->conn = new \mysqli($host, $user, $password, $db, !empty($port) ? $port : ini_get("mysqli.default_port"), !empty($socket) ? $socket : ini_get("mysqli.default_socket"));
            if (!$this->conn->connect_error) {
                $this->conn->set_charset($charset);
            }
        } 
    }

    public function autoCommit($autoCommit)
    {
        if (is_bool($autoCommit)) {
            $this->conn->autocommit($autoCommit);
        }
    }

    /**
     * 2015-02-05 add return 
     * @return boolean
     */
    public function beginTransaction()
    {
        return $this->conn->begin_transaction();
    }

    /**
     * 2014-08-13 set or get character set for the database connection
     * @param type $charset
     * @return type
     */
    public function charset($charset)
    {
        if (!empty($charset)) {
            return $this->conn->set_charset($charset);
        }
        return $this->conn->character_set_name();//2014-08-13
    }

    public function close()
    {
        $close = $this->conn->close();
        $this->conn = null;
        return $close;
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function connectError()
    {
        $errorno = $this->conn->connect_errno;
        if ($errorno === 0)
            return null;
        return 'Error Code : ' . $errorno . ',' . $this->conn->connect_error;
    }

    public function queryError()
    {
        $errorno = $this->conn->errno;
        if ($errorno === 0)
            return null;
        return 'Error Code : ' . $errorno . ',' . $this->conn->error;
    }

    public function prepare($sql)
    {
        if (!empty($sql))
            return new MysqlStatement($this->conn->prepare($sql), $sql);
        else
            return false;
    }

    /**
     * 2014-04-10 修改fetch_all方法参数MYSQLI_BOTH为MYSQLI_ASSOC
     * @param type $sql
     * @return \line\db\conn\result\MysqlResult|boolean
     */
    public function query($sql)
    {
        if (!empty($sql)) {
            $return = $this->conn->query($sql);
            if ($return === false || $return === true) {
                return $return;
            } else {
                $rows = array();
                if (isset($this->anonymous)) {
                    while ($row = $return->fetch_object($this->anonymous)) {
                        $rows[] = $row;
                    }
                } else {
                    while($row = $return->fetch_assoc()){
                        $rows[] = $row;
                    }
                }
                $result = new MysqlResult($rows, $return->field_count, $return->num_rows, $return->fetch_fields());
                $return->close();
                return $result;
            }
        }
        return false;
    }

    public function rollback()
    {
        return $this->conn->rollback();
    }

    /**
     * 返回INSERT时记录的自增ID
     * @add 2014-04-15
     * @return int
     */
    public function getInsertId()
    {
        return $this->conn->insert_id;
    }

    public function affectedRows(){
        return $this->conn->affected_rows;
    }
}
