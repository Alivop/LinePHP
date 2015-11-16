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
use line\db\conn\result\ODBCResult;
use line\db\conn\BaseConn;
use line\db\conn\statement\ODBCStatement;

/**
 * ODBC driver
 * @class ODBC
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\driver
 */
class ODBC extends BaseConn implements DB
{

    public function __construct($dsn, $user, $password)
    {
        if (!isset($this->conn)) {
            $this->conn = odbc_connect($dsn, $user, $password);
        }
    }

    public function autoCommit($autoCommit)
    {
        return odbc_autocommit($this->conn, $autoCommit);
    }

    public function beginTransaction()
    {
        
    }

    public function close()
    {
        odbc_close($this->conn);
    }

    public function commit()
    {
        return odbc_commit($this->conn);
    }

    public function prepare($sql)
    {
         if (!empty($sql))
            return new ODBCStatement(odbc_prepare($this->conn,$sql), $sql);
        else
            return false;
    }

    public function query($sql)
    {
        $result = odbc_exec($this->conn, $sql);
        if($result===false) return false;
        $noselect = odbc_num_rows($result);
        $fields = array();
        $row = array();
        $return  = false;
        if ($noselect === -1) {
            $return = false;
        } else {
            $fn = odbc_num_fields($result);
            if ($fn > 0) {
                for ($i = 1; $i <= $fn; $i++) {
                    $fields[] = odbc_field_name($result, $i);
                }
                while ($row[] = odbc_fetch_array($result));
                array_pop($row);
                $return = new ODBCResult($row, $i - 1, count($row), $fields);
            } else {
                $return = $noselect > 0 ? true : false;
            }
        }
        odbc_free_result($result);
        return $return;
    }

    public function queryError()
    {
        if (odbc_error()) {
            return odbc_errormsg();
        }
        return null;
    }

    public function rollback()
    {
        return odbc_rollback($this->conn);
    }

}
