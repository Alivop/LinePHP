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
 * @abstract Iterator
 * @description The collection iterator.
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @see ListIterator
 * @package line\core\util
 */
abstract class Iterator extends LinePHP
{
    /**
     * 判断集合里面是否还有元素。
     * @return bool 如果还有元素返回true，否则返回false。
     */
    public abstract function hasNext();
    /**
     * 返回迭代的下一个元素，迭代元素从集合的第一个元素开始。
     * @return mixed 集合元素。
     * @throws NoSuchElementException 没有可迭代的元素。 
     */
    public abstract function next();
    /**
     * 移除集合元素，在调用next方法后调用，并且每次next后只能调用一次。
     * @return boolean 移除成功返回true，否则false。
     * @throws IllegalStateException 如果没有调用next前调用此方法会抛出该异常。
     */
    public abstract function remove();
}
