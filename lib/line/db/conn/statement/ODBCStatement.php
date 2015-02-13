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
use line\db\conn\result\ODBCResult;
use line\db\DB;

/**
 *  prepare statement
 * @class ODBCStatement
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\conn\statement
 */
class ODBCStatement extends Statement
{

    public function __construct($statement, $sql)
    {
        if (isset($statement)) {
            $this->statement = $statement;
            $this->sql = $sql;
        }
    }

    public function close()
    {
        $this->statement = null;
    }

    public function execute()
    {
        if (isset($this->parameters)) {
            odbc_execute($this->statement, $this->parameters);
        } else {
            odbc_execute($this->statement);
        }
        $noselect = odbc_num_rows($this->statement);
        $fields = array();
        $row = array();
        $return  = false;
        if ($noselect === -1) {
            $return = false;
        } else {
            $fn = odbc_num_fields($this->statement);
            if ($fn > 0) {
                for ($i = 1; $i <= $fn; $i++) {
                    $fields[] = odbc_field_name($this->statement, $i);
                }
                while ($row[] = odbc_fetch_array($this->statement));
                array_pop($row);
                $return = new ODBCResult($row, $i - 1, count($row), $fields);
            } else {
                $return = $noselect > 0 ? true : false;
            }
        }
        odbc_free_result($this->statement);
        return $return;
    }

    public function setParameter($parameter, $value, $type = DB::STR)
    {
        if (!is_int($parameter))
            return;
        $this->parameters[$parameter] = $value;
    }

    public function getError()
    {
        if (odbc_error()) {
            return odbc_errormsg();
        }
    }

}
