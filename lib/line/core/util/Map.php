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

use line\core\LinePHP;

/**
 * 一个键-值映射关系容器，无重复key（键），允许null，一个key对应一个value（值）。
 * @class Map
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see Collection
 * @package line\core\util
 */
class Map extends LinePHP
{
    const KEY_MODE = 0;
    const VALUE_MODE = 1;

    private $map;
    private $size;

    /**
     * 初始化一个键-值映射容器。
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 清空此键-值映射容器。
     * @return void
     */
    public function clear()
    {
        $this->init();
    }

    /**
     * 判断此键-值映射容器中是否包含指定的键。
     * @param mixed $key 需要检测是否存在的键。
     * @return boolean 存在返回true，否则false。
     */
    public function containsKey($key)
    {
        if (is_null($this->getEntry($key, self::KEY_MODE))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 判断此键-值映射容器中是否包含指定的值。
     * @param mixed $value 需要检测是否存在的值。
     * @return boolean 存在返回true，否则false。
     */
    public function containsValue($value)
    {
        if (is_null($this->getEntry($value, self::VALUE_MODE))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 取得键对应的值。
     * @param mixed $key 要返回映射值的键。
     * @return Entry|null 键对应的值，如果没有则返回null。
     */
    public function get($key)
    {
        $entry = $this->getEntry($key, self::KEY_MODE);
        if (!is_null($entry)) {
            return $entry->value;
        }
        return null;
    }

    /**
     * 此键-值映射容器是否是空的容器。
     * @return boolean 如果是没有键-值映射则返回true，否则返回false。
     */
    public function isEmpty()
    {
        return $this->size == 0;
    }

    /**
     * 设置键-值对。如果键存在则更改映射值，如果不存在则加入。
     * @param mixed $key 设置的键。
     * @param mixed $value 设置的值。
     * @return mixed 如果是一个新的键-值映射则返回映射值，如果是已存在的键-值则返回更新前的映射值。
     */
    public function set($key, $value)
    {
        $index = $this->getIndex($key);
        if ($index < 0) {
            $entry = new Entry($key, $value);
            $this->map[$this->size] = $entry;
            $this->size++;
            return $value;
        } else {
            $entry = $this->map[$index];
            $old = $entry->value;
            $entry->value = $value;
            return $old;
        }
    }

    /**
     * 将容器中所有键-值映射添加到此容器中。如果有相同的键，则更改其映射值。
     * 2014-04-15 修复当添加空Map时出错。
     * 2014-04-18 支持添加一个数组到Map中。
     * @param line\core\util\Map $map 需要加入的键值映射容器。
     * @return void
     */
    public function addAll($map)
    {
        if (!isset($map))
            return;   //2014-04-15
        if ($map instanceof Map) {
            while ($entry = $map->entry()) {
                if (!$this->containsKey($entry->key)) {
                    array_push($this->map, $entry);
                } else {
                    $index = $this->getIndex($entry->key);
                    if ($index >= 0) {
                        $this->map[$index]->value = $entry->value;
                    }
                }
            }
        } else if (is_array($map)) {//2014-04-18
            foreach ($map as $key => $value) {
                if (!$this->containsKey($key)) {
                    $entry = new Entry($key, $value);
                    array_push($this->map, $entry);
                } else {
                    $index = $this->getIndex($key);
                    if ($index >= 0) {
                        $this->map[$index]->value = $value;
                    }
                }
            }
        }
        $this->size = count($this->map);
    }

    /**
     * 移除键-值映射关系。
     * @param mixed $key 要移除的键。
     * @return boolean 成功返回true，否则false。
     */
    public function remove($key)
    {
        $index = $this->getIndex($key);
        if ($index < 0) {
            return false;
        } else {
            array_splice($this->map, $index, 1);
            $this->size--;
            return true;
        }
    }

    /**
     * 返回此键-值映射容器的关系数。
     * @return int 返回键值关系数。
     */
    public function size()
    {
        return $this->size;
    }

    public function keySet()
    {
        $set = new Set();
        while ($element = $this->entry()) {
            $set->add($element->key);
        }
        return $set;
    }

    /**
     * 此键-值映射容器的迭代器，可通过此方法遍历键-值对。每一个键-值对是一个Entry对象。
     * @return Entry|boolean 如果有可迭代的键-值对就返回Entry对象，否则返回false。
     */
    public function entry()
    {
        if ($this->isEmpty())
            return false;
        $element = each($this->map);
        if ($element) {
            //$key = $element['key'];
            $value = $element['value'];
            return $value;
        }
        return false;
    }

    private function getEntryByIndex($index)
    {
        return $this->map[$index];
    }

    private function getEntry($match, $mode)
    {
        if ($this->isEmpty())
            return null;
        $mapTmp = $this->map;
        $property = 'key';
        if ($mode === self::VALUE_MODE) {
            $property = 'value';
        }
        if (is_object($match)) {
            foreach ($mapTmp as $entry) {
                if ($match == $entry->$property) {
                    return $entry;
                }
            }
        } else {
            foreach ($mapTmp as $entry) {
                if ($match === $entry->$property) {
                    return $entry;
                }
            }
        }
        return null;
    }

    private function getIndex($key)
    {
        if ($this->isEmpty())
            return -1;
        $mapTmp = $this->map;
        while ($entry = each($mapTmp)) {
            if ($this->compare($key, $entry['value']->key)) {
                return $entry['key'];
            }
        }
        return -1;
    }

//    public function values() {
//        
//    }

    private function init()
    {
        $this->map = array();
        $this->size = 0;
    }

}
