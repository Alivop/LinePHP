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
use line\db\conn\result\MssqlResult;
use line\db\conn\BaseConn;
use line\db\conn\statement\MssqlStatement;

/**
 * Mssql driver
 * @class Mssql
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\driver
 */
class Mssql extends BaseConn implements DB
{

    public function __construct($host, $db, $user, $password)
    {
        if (!isset($this->conn)) {
            $this->conn = sqlsrv_connect($host, array("UID" => $user, "PWD" => $password, "Database" => $db));
        }
    }

    public function autoCommit($autoCommit)
    {
        
    }

    public function beginTransaction()
    {
        return sqlsrv_begin_transaction($this->conn);
    }

    public function close()
    {
        return sqlsrv_close($this->conn);
    }

    public function commit()
    {
        return sqlsrv_commit($this->conn);
    }

    public function prepare($sql)
    {
        if (!empty($sql))
            return new MssqlStatement($this->conn, $sql);
        else
            return false;
    }

    public function query($sql)
    {
        if (!empty($sql)) {
            $result = sqlsrv_query($this->conn, $sql);
            if ($result === false)
                return false;
            $noselect = sqlsrv_rows_affected($result);
            $fn = sqlsrv_num_fields($result);
            if ($fn>0) {
                $fields = array();
                $row = array();
                while ($r = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    if (empty($fields))
                        $fields = array_keys($r);
                    $row[] = $r;
                }
                $return = new MssqlResult($row, count($fields), count($row), $fields);
            } else {
                $return = $noselect>0?true:false;
            }
            sqlsrv_free_stmt($result);
        }
        return $return;
    }

    public function queryError()
    {
        $error = null;
        if (($errors = sqlsrv_errors(SQLSRV_ERR_ERRORS) ) != null) {
            foreach ($errors as $e) {
                $error .= "code: " . $e['code'] . "<br />";
                $error .= "message: " . $e['message'] . "<br />";
            }
        }
        return $error;
    }

    public function rollback()
    {
        return sqlsrv_rollback($this->conn);
    }

}
