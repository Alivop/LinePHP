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

/**
 * query resultset
 * 2015-02-06 remove abstract functions
 * @class Result
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db
 */
abstract class Result extends \line\core\LinePHP
{

    protected $result;
    protected $columnCount;
    protected $rowCount;
    protected $columns;
    protected $columnNames;

    public function __construct($result, $columnCount, $rowCount, $columns)
    {
        $this->result = $result;
        $this->columnCount = $columnCount;
        $this->rowCount = $rowCount;
        $this->columns = $columns;
        $this->getColumnNames();
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getColumnCount()
    {
        return $this->columnCount;
    }

    public function getRow($index = 0)
    {
        if (is_int($index) && $index < $this->rowCount)
            return $this->result[$index];
        return false;
        //return current($this->result);
    }

    public function getRows()
    {
        if ($this->result && is_array($this->result)) {
            return $this->result;
        }
        return array();
    }

    public function getRowNumber()
    {
        return key($this->result);
    }

    public function getColumn($column)
    {
        $current = current($this->result);
        return $current[$column];
    }

    public function getColumnNames()
    {
        if (!isset($this->columnNames)) {
            foreach ($this->columns as $value) {
                $this->columnNames[] = $value->name;
            }
        }
        return $this->columnNames;
    }

    public function next()
    {
        $row = current($this->result);
        next($this->result);
        return $row;
    }

    public function first()
    {
        return reset($this->result);
    }

    public function last()
    {
        return end($this->result);
    }

    public function previous()
    {
        return prev($this->result);
    }

    public function isFirst()
    {
        $index = key($this->result);
        if ($index === 0)
            return true;
        return false;
    }

    public function isLast()
    {
        $index = key($this->result);
        if ($index === $this->rowCount - 1)
            return true;
        return false;
    }

}
