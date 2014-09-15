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

use line\core\LinePHP;
use line\core\filter\Filter;
use line\core\util\Map;
use line\core\mvc\Router;

/**
 * Request请求处理类。
 * @class RequestHandler
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\request
 */
class RequestHandler extends LinePHP
{
    public function handler()
    {
        self::console(\line\core\Config::DEBUG, \line\core\Config::$LP_LANG['framework_run']);
        //try {
        $this->filterRequest();
        $request = $this->createRequestObject();
        $router = new Router($request);
        $router->dispatcher();
//        } catch (\Exception $e) {
//            $code = 500;
//            $message = '';
//            if ($e instanceof \line\core\exception\InvalidRequestException) {
//                $code = 400;
//            }else if($e instanceof \line\core\exception\IllegalAccessException){
//                //$code = 500;
//            } else {
//                //$code = 500;
//            }
//            $message = $e->getMessage();
//            self::console(\line\core\Config::ERROR, $message);
//            echo $message;
//            //throw new \Exception($e->getMessage(), $code, $e);
//        }
    }

    private function filterRequest()
    {
        Filter::checkURL();
    }

    /**
     * 2014-05-21 修正获取request url
     * @return \line\core\request\Request
     */
    private function createRequestObject()
    {
        $server = $this->newServer();
        $remote = $this->newRemote();
        $browser = $this->newBrowser();
        $url = Filter::filterURL();
        $request = new Request($remote, $server, $browser, $this->lineGET($url), $this->linePOST(), $this->getUploadFiles());
        $request->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $request->query = filter_input(INPUT_SERVER, 'QUERY_STRING');
        $request->scheme = filter_input(INPUT_SERVER, 'REQUEST_SCHEME');
        $request->protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');
        $request->time = filter_input(INPUT_SERVER, 'REQUEST_TIME');
        $request->uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $request->url = $url;
        $request->documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        return $request;
    }

    private function newServer()
    {
        $addr = filter_input(INPUT_SERVER, 'SERVER_ADDR');
        $port = filter_input(INPUT_SERVER, 'SERVER_PORT');
        $name = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $software = filter_input(INPUT_SERVER, 'SERVER_SOFTWARE');
        $documentRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $scriptName = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
        $scriptFile = filter_input(INPUT_SERVER, 'SCRIPT_FILENAME');
        $map = new Map();
        $map->set('addr', $addr);
        $map->set('port', $port);
        $map->set('name', $name);
        $map->set('software', $software);
        $map->set('documentRoot', $documentRoot);
        $map->set('scriptName', $scriptName);
        $map->set('scriptFile', $scriptFile);
        return new Server($map);
    }

    private function newRemote()
    {
        $addr = filter_input(INPUT_SERVER, 'SERVER_ADDR');
        $port = filter_input(INPUT_SERVER, 'SERVER_PORT');
        $host = filter_input(INPUT_SERVER, 'REMOTE_HOST');
        $user = filter_input(INPUT_SERVER, 'REMOTE_USER');
        $map = new Map();
        $map->set('addr', $addr);
        $map->set('port', $port);
        $map->set('host', $host);
        $map->set('user', $user);
        return new Remote($map);
    }

    private function newBrowser()
    {
        $acceptType = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $acceptEncoding = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING');
        $acceptLanguage = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
        $acceptCharset = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_CHARSET');
        $cookie = filter_input(INPUT_SERVER, 'HTTP_COOKIE');
        $userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        $connection = filter_input(INPUT_SERVER, 'HTTP_CONNECTION');
        $referer = filter_input(INPUT_SERVER, 'HTTP_REFERER');
        $map = new Map();
        $map->set('acceptType', $acceptType);
        $map->set('acceptEncoding', $acceptEncoding);
        $map->set('acceptLanguage', $acceptLanguage);
        $map->set('acceptCharset', $acceptCharset);
        $map->set('cookie', $cookie);
        $map->set('userAgent', $userAgent);
        $map->set('connection', $connection);
        $map->set('referer', $referer);
        return new Browser($map);
    }

    /**
     * 2014-05-15 更改LineGET获取的URI为URL，避免URL中其他字符非规定字符造成的错误。
     * 2014-07-04 加入GET参数
     * @param type $url
     * @return \line\core\util\Map|null
     */
    private function lineGET($url)
    {//2014-05-15
        $get = (explode('/', $url));
        $map = new Map();
        foreach ($_GET as $key => $value) {//2014-07-04
            Filter:: filterParameter($value);
            $map->set($key, $value);
        }
        if (count($get) > 2) {
            $parameter = end($get);
            if (isset($parameter) && $parameter != '') {
                $params = explode('_', $parameter);
                $i = 0;
                foreach ($params as $para) {
                    $arr = explode(':', $para);
                    if (count($arr) == 1) {
                        //continue;
                        Filter::filterParameter($arr[0]);
                        $map->set($i, $arr[0]); //2014-05-14
                        $_GET[] = $arr[0];
                        $i++;
                    } else {
                        Filter::filterParameter($arr[1]);
                        $map->set($arr[0], $arr[1]);
                        $_GET[$arr[0]] = $arr[1];
                    }
                }
            }
        }
        return $map;
    }

    private function getUploadFiles($files = null)
    {
        $files = $_FILES;
        $map = new Map();
        if (!empty($files)) {
            foreach ($files as $key => $value) {
                if (is_array($value['name'])) {
                    $multi = array();
                    $list = new \line\core\util\ArrayList;
                    foreach ($value as $subKey => $subValue) {
                        foreach ($subValue as $k => $v) {
                            $multi[$k][$subKey] = $v;
                        }
                    }
                    for ($i = 0; $i < count($multi); $i++) {

                        $file = new \line\io\upload\Siglefile($multi[$i]);
                        $list->add($file);
                    }
                    $upload = new \line\io\upload\Multifile($list);
                } else {
                    $upload = new \line\io\upload\Siglefile($value);
                }
                $map->set($key, $upload);
            }
        }
        return $map;
    }

    private function linePOST()
    {
        $map = new Map();
        foreach ($_POST as $key => $value) {
            Filter:: filterParameter($value);
            $map->set($key, $value);
            $_POST[$key] = $value;
        }
        return $map;
    }

}
