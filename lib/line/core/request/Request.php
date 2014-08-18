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

    public function __construct(Remote $remote, Server $server, Browser $browser, $getParamMap, $postParamMap, Map $uploadFiles)
    {
        $this->remote = $remote;
        $this->browser = $browser;
        $this->server = $server;
        $this->getParamMap = $getParamMap;
        $this->uploadFiles = $uploadFiles;
        $this->postParamMap = $postParamMap;
    }

    public function parameter($name)
    {
        $get = $this->get($name);
        if (!is_null($get)) {
            return $get;
        }
        return $this->post($name);
    }

    /**
     * 2014-05-09 对POST提交的数据进行HTML编码，防止特殊字符引起的错误
     * 2014-05-13 过滤转移
     * @param string $name
     * @return null|string
     */
    public function get($name)
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
    public function post($name)
    {
        return $this->getParameter($name, 1); //2014-05-14
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getRemote()
    {
        return $this->remote;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function server($var)
    {
        if (property_exists($this->server, $var)) {
            return $this->server->$var;
        } else {
            return null;
        }
    }

    public function remote($var)
    {
        if (property_exists($this->remote, $var)) {
            return $this->remote->$var;
        } else {
            return null;
        }
    }

    public function browser($var)
    {
        if (property_exists($this->browser, $var)) {
            return $this->browser->$var;
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
     * 2014-05-14 GET/POST参数统一使用MAP获取
     * @param string $name
     * @param int $type
     * @return null|string
     */
    private function getParameter($name, $type)
    {
        if (!isset($name))
            return null;
        if ($type == 0) {
            if ($this->getParamMap instanceof Map) {
                $val = $this->getParamMap->get($name);
                return $val;
            }
        } else {
            if ($this->postParamMap instanceof Map) {
                $val = $this->postParamMap->get($name);
                return $val;
            }
        }
        return null;
    }
    
}
