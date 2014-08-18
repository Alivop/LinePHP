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
use line\core\exception\IllegalArgumentException;

/**
 * This class implement interface List like an array .
 * @class ArrayList
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see Collection
 * @see AbstractCollection
 * @see Set
 * @package line\core\util
 */
class ArrayList extends AbstractCollection
{
    private $elements;
    private $size;

    public function __construct()
    {
        $this->elements = array();
        $this->size = 0;
    }

//    public function __toString() {
//        //return '[' . implode(',', $this->elements) . ']';
//        return $this->toString($this);
//    }

    public function add($element)
    {
        //if (is_array($element)) {
        //    $array = array_values($element);
        //    $this->elements = array_merge($this->elements, $array);
        //    $this->size = count($this->elements);
        //} else {
        $this->elements[$this->size] = $element;
        $this->size++;
        //}
        return true;
    }

    public function addAll(Collection $collection)
    {
        $array = $collection->toArray();
        $this->elements = array_merge($this->elements, $array);
        $this->size = count($this->elements);
    }

    public function clear()
    {
        $this->elements = array();
        $this->size = 0;
    }

    /**
     * 字符串区分大写
     * @param type $obj
     * @return boolean
     */
    public function contains($obj)
    {
        if ($this->isEmpty())
            return false;
        $key = array_search($obj, $this->elements, true);
        if ($key === false) {
            return false;
        }
        return true;
    }

    public function get($index)
    {
        if ($this->isEmpty())
            return null;
        if (is_numeric($index)) {
            $this->checkRange($index);
            return $this->elements[$index];
        } else {
            throw new IllegalArgumentException();
        }
    }

    public function remove($mix)
    {
        if ($this->isEmpty())
            return false;
        $key = array_search($mix, $this->elements, true);
        if ($key !== false) {
            return $this->remove($key);
        }
        return false;
    }

    public function set($index, $element)
    {
        if (is_numeric($index)) {
            $this->checkRange($index);
            $old = $this->elements[$index];
            $this->elements[$index] = $element;
            return $old;
        } else {
            throw new IllegalArgumentException();
        }
    }

    public function size()
    {
        return $this->size;
    }

    public function toArray()
    {
        return $this->elements;
    }

    public function iterator()
    {
        return new ListIterator($this, $this->elements);
    }

    /**
     * 自然排序,字母区分大小写.
     * @return void
     */
    public function sort()
    {
        //暂使用数组内部排序法
        natsort($this->elements);
    }

    /**
     * 获取元素的索引,返回的是第=第一次出现时的索引.
     * @param type $element 
     * @return int 
     */
    public function indexOf($element)
    {
        if ($this->isEmpty())
            return -1;
        $key = array_search($mix, $this->elements, true);
        if ($key === false) {
            return -1;
        } else {
            return $key;
        }
    }

    private function checkRange($index)
    {
        $index = (int) $index;
        if ($index >= $this->size) {
            throw new IndexOutOfBoundsException();
        }
    }

    /*
     * 
      private function toString($element){
      if($element instanceof Collection){
      $array = $element->toArray();
      $string = '[';
      foreach ($array as $ele){
      $string .= $this->toString($ele).',';
      }
      $string = rtrim($string, ',');
      return $string.']';
      }else if(is_object($element)){
      return get_class($element).'()';
      }else if(is_array($element)){
      return 'Array';
      }else if(is_resource($element)){
      return 'Resource';
      }else{
      return $element;
      }
      }
     * 
     */
}
