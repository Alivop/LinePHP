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
 * 
 * @abstract AbstractCollection
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see Collection
 * @package line\core\util
 */
abstract class AbstractCollection extends LinePHP implements Collection
{
    public function __toString()
    {
        return $this->toString($this);
    }

    //public abstract function add($element);
    //public abstract function addAll(Collection $collection);
    public function clear()
    {
        $iterator = $this->iterator();
        while ($iterator->hasNext()) {
            $iterator->next();
            $iterator->remove();
        }
    }

    public function contains($obj)
    {
        $iterator = $this->iterator();
        while ($iterator->hasNext()) {
            if ($obj === $iterator->next()) {
                return true;
            }
        }
        return false;
    }

    public function isEmpty()
    {
        return $this->size() == 0;
    }

    public function remove($mix)
    {
        $iterator = $this->iterator();
        while ($iterator->hasNext()) {
            if ($mix === $iterator->next()) {
                $iterator->remove();
            }
        }
    }

    //public abstract function size();
    public function toArray()
    {
        $array = array();
        $i = 0;
        $iterator = $this->iterator();
        while ($iterator->hasNext()) {
            $array[$i] = $iterator->next();
            $i++;
        }
        return $array;
    }

    //public abstract function iterator();
    //public abstract function sort();
    protected function toString($element)
    {
        if ($element instanceof Collection) {
            //$array = $element->toArray();
            $string = '[';
            $iterator = $element->iterator();
            while ($iterator->hasNext()) {
                $ele = $iterator->next();
                $string .= $this->toString($ele) . ',';
            }
            $string = rtrim($string, ',');
            return $string . ']';
        } else if (is_object($element)) {
            return get_class($element) . '()';
        } else if (is_array($element)) {
            return 'Array';
        } else if (is_resource($element)) {
            return 'Resource';
        } else {
            return $element;
        }
    }

}
