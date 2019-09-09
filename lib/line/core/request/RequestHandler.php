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
        Filter::checkURL();
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

    /**
     * 2014-05-21 修正获取request url
     * 2015-09-08 Resquet加入新的参数（以其他方式提交：put/delete）
     * 2015-09-08 修改$server参数
     * @return \line\core\request\Request
     */
    private function createRequestObject()
    {
        $server = $_SERVER;
        $url = Filter::filterURL();
        $request = new Request($server, $this->lineGET($url), 
                $this->linePOST(), $this->getUploadFiles(),  $this->lineOtherParameter());
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

    /**
     * 2014-05-15 更改LineGET获取的URI为URL，避免URL中其他字符非规定字符造成的错误。
     * 2014-07-04 加入GET参数
     * 2015-09-09 使用原生数组提升性能
     * @param type $url
     * @return array
     */
    private function lineGET($url)
    {//2014-05-15
        $get = (explode('/', $url));
        $map = array();
        if (count($get) > 2) {
            $parameter = end($get);
            if (isset($parameter) && $parameter != '') {
                $params = explode('_', $parameter);
                $i = 0;
                foreach ($params as $para) {
                    $arr = explode(':', $para);
                    if (count($arr) == 1) {
                        $_GET[] = $arr[0];
                        $i++;
                    } else {
                        $_GET[$arr[0]] = $arr[1];
                    }
                }
            }
        }
        foreach ($_GET as $key => $value) {//2014-07-04
            Filter:: filterParameter($value);
            $map[$key] = $value;
        }
        return $map;
    }

    /**
     * 2015-09-09 优化性能
     * @param array $files
     * @return array
     */
    private function getUploadFiles($files = null)
    {
        $files = $_FILES;
        $map = array();
        if (!empty($files)) {
            foreach ($files as $key => $value) {
                if (is_array($value['name'])) {
                    $multi = array();
                    $list = array();
                    foreach ($value as $subKey => $subValue) {
                        foreach ($subValue as $k => $v) {
                            $multi[$k][$subKey] = $v;
                        }
                    }
                    for ($i = 0; $i < count($multi); $i++) {
                        $file = new \line\io\upload\Siglefile($multi[$i]);
                        $list[] = $file;
                    }
                    $upload = new \line\io\upload\Multifile($list);
                } else {
                    $upload = new \line\io\upload\Siglefile($value);
                }
                $map[$key] = $upload;
            }
        }
        return $map;
    }

    /**
     * 2015-09-09 优化性能
     * @return array
     */
    private function linePOST()
    {
        $map = array();
        foreach ($_POST as $key => $value) {
            Filter:: filterParameter($value);
            $map[$key] = $value;
        }
        return $map;
    }
    
    /**
     * 获取其他形式的请求参数，如PUT,DELETE或者POST json参数或者上传的文件
     * 2015-10-08 增加对上传文件的处理，优化性能
     * @return array
     */
    private function lineOtherParameter(){
        $map = array();
        $raw = file_get_contents("php://input");
        if(empty($raw)){
            return $map;
        }
        $data = json_decode($raw,true);
        if(!$data){
            parse_str($raw,$data);
        }
        if(empty($data)){//可能为上传的文件
            //$temp = tmpfile();//创建临时文件
            //fwrite($temp, $data);
            $map["_temp"] = $data;
        }else{
            foreach ($data as $key => $value){
                Filter:: filterParameter($value);
                $map[$key] = $value;
            }
        }
        return $map;
    }

}
