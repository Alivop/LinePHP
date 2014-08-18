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
 * 
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

    public function __construct($result, $columnCount, $rowCount, $columns)
    {
        $this->result = $result;
        $this->columnCount = $columnCount;
        $this->rowCount = $rowCount;
        $this->columns = $columns;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getColumnCount()
    {
        return $this->columnCount;
    }

    abstract public function getRow($index = 0);
    abstract public function getRowNumber();
    abstract public function getColumn($column);
    abstract public function getColumnNames();
    abstract public function next();
    abstract public function first();
    abstract public function last();
    abstract public function previous();
    abstract public function isFirst();
    abstract public function isLast();
}
