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

use line\core\LinePHP;
use line\db\Statement;
use line\db\conn\result\MysqlResult;

/**
 * 
 * @class MysqlStatement
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db\conn\statement
 */
class MysqlStatement extends LinePHP implements Statement
{
    private $statement;
    private $parameters;
    private $type;
    private $ref;

    public function __construct($statement, $sql)
    {
        $this->statement = $statement;
        $this->parameters = array();
        $this->type[] = '';
        $this->ref = array();
    }

    /**
     * 绑定参数
     * @param int $parameter 索引，从1开始
     * @param mixed $value
     * @param int $type
     * @return void
     */
    public function setParameter($parameter, $value, $type = self::DB_STR)
    {
        if (!is_int($parameter))
            return;
        switch ($type) {
            case self::DB_BOOL:
                if ($value === true || $value === 'true' || $value === '1' || $value === 1)
                    $value = 1;
                else
                    $value = 0;
                $this->type[0] .= 'i';
                $this->parameters[$parameter] = $value;
                break;
            case self::DB_NULL :
                $this->type[0] .= 's';
                $this->parameters[$parameter] = null;
                break;
            case self::DB_INT:
                $this->type[0] .= 'i';
                $this->parameters[$parameter] = $value;
                break;
            case self::DB_STR :
                $this->type[0] .= 's';
                $this->parameters[$parameter] = $value;
                break;
            case self::DB_LOB :
                $this->type[0] .= 'b';
                $this->parameters[$parameter] = $value;
                break;
            default :
                $this->type[0] .= 's';
                $this->parameters[$parameter] = $value;
        }
    }

    /**
     * 2014-04-11 修改fetch_all方法参数MYSQLI_BOTH为MYSQLI_ASSOC
     * @return \line\db\conn\result\MysqlResult|boolean
     */
    public function execute()
    {
        $n = ($this->statement->param_count);
        if ($n > 0) {
            if ($n == count($this->parameters)) {
                $ref = array();
                foreach ($this->parameters as $key => $value)
                    $ref[$key] = &$this->parameters[$key];
                call_user_func_array(array($this->statement, 'bind_param'), array_merge($this->type, $ref));
            } else {
                return false;
            }
        }

        if ($this->statement->execute()) {
            $return = $this->statement->get_result();
            if ($return) {
                $result = new MysqlResult($return->fetch_all(MYSQLI_ASSOC), $return->field_count, $return->num_rows, $return->fetch_fields());
                $return->close();
                return $result;
            }
            //$this->parameters = array();
            $this->type = [''];
            return true;
        } else {
            return false;
        }
    }

    public function close()
    {
        return $this->statement->close();
    }

    private function changeReference($params)
    {
        $ref = array();
        foreach ($params as $key => $value)
            $ref[$key] = &$params[$key];
        return $ref;
    }

    /**
     * 返回INSERT时记录的自增ID
     * @add 2014-04-15
     * @return int
     */
    public function getInsertId()
    {
        return $this->statement->insert_id;
    }

}
