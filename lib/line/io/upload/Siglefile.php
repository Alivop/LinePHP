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

use line\core\Config;

/**
 * 
 * @class Siglefile
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\io\upload
 */
class Siglefile extends \UploadFile
{
    private $upload;
    private $errorMessage;

    public function __construct(array $upload = array())
    {
        $this->upload = $upload? : array('name' => '', 'size' => 0, 'type' => '', 'tmp_name' => '',
            'error' => null);
        $this->errorMessage = array(Config::$LP_LANG['upload_err_ok'], Config::$LP_LANG['upload_err_ini_size']
            , Config::$LP_LANG['upload_err_form_size'], Config::$LP_LANG['upload_err_partial']
            , Config::$LP_LANG['upload_err_no_file'], Config::$LP_LANG['upload_err_no_tmp_dir']
            , Config::$LP_LANG['upload_err_cant_write']);
    }

    public function name()
    {
        return $this->upload['name'];
    }

    public function size()
    {
        return $this->upload['size'];
    }

    public function type()
    {
        return $this->upload['type'];
    }

    public function tmpName()
    {
        return $this->upload['tmp_name'];
    }

    public function errorCode()
    {
        return $this->upload['error'];
    }

    public function errorMessage()
    {
        return $this->errorMessage[$this->errorCode()];
    }

    public function isUpload()
    {
        if (key_exists('tmp_name', $this->upload)) {
            return is_uploaded_file($this->upload['tmp_name']);
        }
        return false;
    }

    /**
     * 2014-04-25 修复没有上传文件的情况返回TRUE的问题
     * 2014-05-01 修复上传出错返回TRUE
     * @return boolean 
     */
    public function hasFile()
    {
        return isset($this->upload['error']) && $this->upload['error'] == 0 ? true : false;
    }

    /**
     * 保存过程中会检测目录是否存在，如果不存在则新建目录，包括子目录。
     * 2014-04-12 更改创建中文目录
     * 2014-06-16 增加创建文件夹的权限
     * @param string $destination 保存的路径，包含文件名。
     * @return boolean
     */
    public function save($destination)
    {
        $destination = iconv('UTF-8', 'GBK', $destination); //2014-04-12
        if (empty($destination))
            return false;
        $dir = dirname($destination);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //2014-06-16
        }
        return move_uploaded_file($this->tmpName(), $destination);
    }

    public function getExt()
    {
        return '.' . pathinfo($this->upload['name'], PATHINFO_EXTENSION);
    }

}
