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

/**
 * 一个无重复元素的集合。
 * @class Set
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see Collection
 * @see AbstractCollection
 * @see ArrayList
 * @package line\core\util
 */
class Set extends AbstractCollection
{
    private $set;
    private $size;

    /**
     * Constructs an empty Set collection.
     */
    public function __construct()
    {
        $this->set = array();
        $this->size = 0;
    }

    public function add($element)
    {
        if (!$this->contains($element)) {
            $this->set[$this->size] = $element;
            $this->size++;
        }
    }

    public function addAll(Collection $collection)
    {
        $iterator = $collection->iterator();
        while ($iterator->hasNext()) {
            $element = $iterator->next();
            $this->add($element);
        }
    }

    public function contains($obj)
    {
        $iterator = $this->iterator();
        while ($iterator->hasNext()) {
            $element = $iterator->next();
            if ($this->compare($obj, $element)) {
                return true;
            }
        }
        return false;
    }

    public function iterator()
    {
        return new ListIterator($this, $this->set);
    }

    public function size()
    {
        return $this->size;
    }

    public function sort()
    {
        return natsort($this->set);
    }

    public function toArray()
    {
        return $this->set;
    }

}
