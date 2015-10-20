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
namespace line\core\request;

use line\core\util\Map;

/**
 * 
 * @class Request
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\request
 */
class Request extends \Request
{
    /** @var string Request type;i.e.'http'. */
    public $scheme;

    /** @var string Name and revision of the information protocol; i.e.'HTTP/1.1'. */
    public $protocol;

    /** @var string Request method ;i.e.'GET','POST','PUT','HEAD'. */
    public $method;

    /** @var string The timestamp of the start of the request. */
    public $time;

    /** @var string The URI of this request to access this page;i.e.'/control/get?id=1' */
    public $uri;

    /** @var string Request uri except query string. */
    public $url;

    /** @var string The query string. */
    public $query;
    public $documentRoot;
    private $remote;
    private $server;
    private $browser;
    private $getParamMap;
    private $uploadFiles;
    private $postParamMap;
    private $otherParamMap;
    private $allParam;

    public function __construct($server,$getParamMap, $postParamMap, $uploadFiles,$otherParamMap)
    {
        $this->server = $server;
        $this->getParamMap = $getParamMap;
        $this->uploadFiles = $uploadFiles;
        $this->postParamMap = $postParamMap;
        $this->otherParamMap = $otherParamMap;
        $this->allParam = array_merge($getParamMap, $postParamMap, $otherParamMap,$uploadFiles);
    }

    /**
     * 2015-09-08 添加获取其他请求方式的参数
     * 2015-09-09 $name为null时返回所有的请求参数
     * 2015-10-16 简化获取参数值
     * @param string $name
     * @return null|string 
     */
    public function parameter($name=null)
    {
        if(is_null($name)) {
            return $this->allParam;
        }else{
            if(isset($this->allParam[$name])){
                return $this->allParam[$name];
            }
        }
        return null;
    }

    /**
     * 2014-05-13 过滤转移
     * @param string $name
     * @return null|string
     */
    public function inputGet($name)
    {
        return $this->getParameter($name, 0); //2014-05-14
    }

    /**
     * 2014-04-12 允许值为空的
     * 2014-04-28 修改POST值获取，取消filter过滤。（数组参数无法通过filet_input获取）
     * 2014-05-09 对POST提交的数据进行HTML编码，防止特殊字符引起的错误
     * 2014-05-13 过滤转移
     * @param string $name
     * @return null
     */
    public function inputPost($name)
    {
        return $this->getParameter($name, 1); //2014-05-14
    }

    public function getServer()
    {
        return $this->server;
    }

    public function server($var)
    {
        if (key_exists($var,$this->server)) {
            return $this->server[$var];
        } else {
            return null;
        }
    }

    public function getUploadFile($name)
    {
        //if (isset($name))
        return $this->uploadFiles->get($name);
        //return null;
    }
    
    /**
     * 2015-09-08 获取其他请求方式的参数
     * @param type $name
     * @return type null|string
     */
    public function inputOther($name){
        return $this->getParameter($name, 2);
    }

    /**
     * 2014-05-14 GET/POST参数统一使用MAP获取
     * 2015-10-16 更改参数获取方式
     * @param string $name
     * @param int $type
     * @return null|string
     */
    private function getParameter($name, $type)
    {
        if (!isset($name))
            return null;
        if ($type == 0) {
            if (isset($this->getParamMap[$name])) {
                return $this->getParamMap[$name];
            }
        } else if($type == 1) {
            if (isset($this->postParamMap[$name])) {
                return $this->postParamMap[$name];
            }
        }else{
            if (isset($this->otherParamMap[$name])) {
                return $this->otherParamMap[$name];
            }
        }
        return null;
    }
    
}
