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
namespace line\io\upload;

/**
 * 
 * @class Multifile
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\io\upload
 */
class Multifile extends \FileArray
{
    private $list;
    private $count;

    public function __construct( $list = null)
    {
        $this->list = $list?$list : array();
        $this->count = count($this->list);
    }

    public function count()
    {
        return $this->count;
    }

    public function file($index)
    {
        if (is_int($index) && $index < $this->count) {
            return $this->list[$index];
        }
        return null;
    }

    public function iterator()
    {
        $file = each($this->list);
        if($file){
            return $file['value'];
        }else{
            return FALSE;
        }
    }

}
