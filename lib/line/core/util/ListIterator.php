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
namespace line\core\util;

use line\core\exception\IndexOutOfBoundsException;

/**
 * 集合迭代器
 * @class ListIterator
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see Iterator
 * @package line\core\util
 */
class ListIterator extends Iterator
{
    private $cursor;
    private $current;
    private $size;
    private $obj;
    private $listArray;

    /**
     * 使用集合进行初始化。
     * @param \line\core\util\Collection $obj
     */
    public function __construct(Collection &$obj, &$array)
    {
        $this->cursor = 0;
        $this->current = 0;
        $this->size = $obj->size();
        $this->obj = $obj;
        $this->listArray = $array; //$obj->toArray();
    }

    /**
     * 判断集合里面是否还有元素。
     * @return bool 如果还有元素返回true,否则返回false
     */
    public function hasNext()
    {
        return $this->cursor != $this->size;
    }

    /**
     * 返回迭代的下一个元素，迭代元素从集合的第一个元素开始。
     * @return mixed 集合元素。
     * @throws NoSuchElementException
     */
    public function next()
    {
        //$listArray = $this->obj->toArray();
        $element = each($this->listArray);
        if ($element) {
            $this->current = $element['key'];
            $this->cursor = $this->current + 1;
            return $element['value'];
        } else {
            throw new NoSuchElementException();
        }
    }

    /**
     * 移除集合元素，在调用next方法后调用，并且每次next后只能调用一次。
     * @return boolean 移除成功返回true，否则false。
     * @throws IllegalStateException 如果没有调用next前调用此方法会抛出该异常。
     */
    public function remove()
    {
        if ($this->cursor == 0) {
            throw new IllegalStateException();
        }
        if ($this->current >= 0 && $this->current < $this->size) {
            $this->obj->remove($this->current);
            reset($this->listArray);
            $this->size--;
            $this->cursor = 0;
            return true;
        }
        return false;
    }

}
