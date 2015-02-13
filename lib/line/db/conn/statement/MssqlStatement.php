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

namespace line\db\conn\statement;

use line\db\Statement;
use line\db\conn\result\MssqlResult;
use line\db\DB;

/**
 *  prepare statement
 * @class MssqlStatement
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\conn\statement
 */
class MssqlStatement extends Statement
{

    private $parameter = array();

    public function __construct($conn, $sql)
    {
        $this->statement = $conn;
        $this->sql = $sql;
    }

    public function close()
    {
        if (isset($this->statement))
            sqlsrv_free_stmt($this->statement);
    }

    public function execute()
    {
        if (empty($this->parameter)) {
            $result = sqlsrv_prepare($this->statement,  $this->sql);
        } else {
            $result = sqlsrv_prepare($this->statement,  $this->sql, $this->parameter);
        }
        sqlsrv_execute($result);
        if (($errors = sqlsrv_errors(SQLSRV_ERR_ERRORS) ) != null) {
            foreach ($errors as $error) {
                $this->error .= "code: " . $error['code'] . "<br />";
                $this->error .= "message: " . $error['message'] . "<br />";
            }
        }
        $noselect = sqlsrv_rows_affected($result);
        $fn = sqlsrv_num_fields($result);
        if ($fn > 0) {
            $fields = array();
            $row = array();
            while ($r = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                if (empty($fields))
                    $fields = array_keys($r);
                $row[] = $r;
            }
            $return = new MssqlResult($row, count($fields), count($row), $fields);
        } else {
            $return = $noselect > 0 ? true : false;
        }
        sqlsrv_free_stmt($result);
        return $return;
    }

    public function setParameter($parameter, $value, $type = DB::STR)
    {
        if (!is_int($parameter))
            return;
        $this->parameter[] = $value;
    }

    public function getError()
    {
        return $this->error;
    }

}
