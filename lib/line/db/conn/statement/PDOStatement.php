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
use line\db\conn\result\PDOResult;
use line\db\DB;

/**
 *  prepare statement
 * @class PDOStatement
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.4
 * @package line\core\db\conn\statement
 */
class PDOStatement extends Statement
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

    /**
     * execute prepare sql and return result
     * @return boolean|\line\db\conn\result\PDOResult
     */
    public function execute()
    {
        $this->statement->execute();
        if ($this->statement->columnCount() <= 0) {
            return $this->statement->rowCount() > 0;
        } else {
            $rows = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
            $fields = isset($rows[0]) ? array_keys($rows[0]) : array();
            $fieldsCount = $this->statement->columnCount();
            $this->statement = null;
            return new PDOResult($rows, $fieldsCount, count($rows), $fields);
        }
    }

    public function setParameter($parameter, $value, $type = DB::STR)
    {
        $dataType = \PDO::PARAM_STR;
        switch ($type) {
            case DB::BOOL :
                $dataType = \PDO::PARAM_BOOL;
                break;
            case DB::NULL :
                $dataType = \PDO::PARAM_NULL;
                break;
            case DB::INT :
                $dataType = \PDO::PARAM_INT;
                break;
            case DB::LOB :
                $dataType = \PDO::PARAM_LOB;
                break;
            default :
                $dataType = \PDO::PARAM_STR;
        }
        $this->statement->bindValue($parameter, $value, $dataType);
    }

    public function getError()
    {
        $errorno = $this->conn->errorCode();
        if (!isset($errorno)||$errorno==\PDO::ERR_NONE)
            return null;
        $errorInfo = $this->conn->errorInfo();
        return 'SQLSTATE Error Code : ' . $errorno . ', DataBase Error Code : ' .$errorInfo[1].', Error Info : '.$errorInfo[2] ;
    }

}
