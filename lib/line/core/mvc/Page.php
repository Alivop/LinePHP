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
namespace line\core\mvc;

/**
 * 
 * @class Page
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\mvc
 */
class Page extends Data
{
    protected $name;

    //protected $data;
    public function __construct($name = null, Data $data = null)
    {
        $this->name = $name;
        if (isset($data))
            $this->dataMap = $data->dataMap;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 2014-04-15 setDate调用后之前的data数据将会保留，新data将加入Data中。
     * @param \line\core\mvc\Data $data
     */
    public function setData(Data &$data)
    {
        if (isset($data))
            $this->dataMap->addAll($data->dataMap);
        //$this->data = $data;
    }

}
