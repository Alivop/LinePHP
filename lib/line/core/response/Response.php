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

namespace line\core\response;

use line\core\LinePHP;
use line\core\Config;
use line\core\exception\UnsupportedException;
use line\core\util\StringUtil;
use line\core\exception\FileNotFoundException;
use line\core\template\Template;
/**
 * The response class
 * @class Response
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package line\core\response
 */
class Response extends LinePHP
{

    const EXT = '.php';
    private $target;
    private $data;
    private $type;
    private $supportType;
    private $charset;

    public function __construct($data = null, $target = null ,$type = null, $charset = null)
    {
        $this->target = $target;
        $this->data = $data;
        $this->supportType = array("html", "json", "xml", "plain");
        if (isset($this->type)) {
            $this->type($type);
        } else {
            $this->type = $this->supportType[0];
        }
        if (isset($charset)) {
            $this->charset($charset);
        } else {
            $this->charset = Config::$LP_SYS[Config::SYS_ENCODE];
        }
    }

    public function target($uri = null)
    {
        if (isset($uri)) {
            $this->target = $uri;
        } else {
            return $this->target;
        }
    }

    public function data($data = null)
    {
        if (isset($data)) {
            $this->data = $data;
        } else {
            return $this->data;
        }
    }

    public function type($type = null)
    {
        if (isset($type)) {
            $str = strtolower($type);
            if (in_array($str, $this->supportType)) {
                $this->type = $str;
            } else {
                throw new UnsupportedException($this->$target . ',' . Config::$LP_LANG['response'] . StringUtil::systemFormat(Config::$LP_LANG['unsupported_type'], $type), 500);
            }
        } else {
            return $this->type;
        }
    }

    public function charset($charset = null)
    {
        if (isset($charset)) {
            $this->charset = $charset;
        } else {
            return $this->charset;
        }
    }

    public function render()
    {
        if (empty($this->target)) {
            $data = $this->data;
            switch ($this->type) {
                case $this->supportType[0]:
                    header("Content-Type: text/html; charset= $this->charset");
                    break;
                case $this->supportType[1]:
                    header("Content-Type: application/json;charset=$this->charset");
                    if (is_array($this->data) || is_object($this->data)) {
                        $data = json_encode($this->data);
                    }
                    break;
                case $this->supportType[2]:
                    header("Content-Type: application/xml;charset=$this->charset");
                    break;
                case $this->supportType[3]:
                    header("Content-Type: text/plain;charset=$this->charset");
                    break;
            }
            echo $data;
        } else {
            $pagePath = LP_ROOT . Config::$LP_PATH[Config::PATH_PAGE] . LP_DS . Config::$LP_SYS[Config::SYS_TEMPLATE_DIR] . LP_DS;
            $pageFile = $pagePath . $this->target;
            $ext = pathinfo($pageFile, PATHINFO_EXTENSION);
            if (empty($ext)) {
                $pageFile .= self::EXT;
            }
            if (is_file($pageFile)) {
                ob_start();
                if (empty($ext)) {//use template parse
                    readfile($pageFile);
                    $content = ob_get_contents();
                    ob_clean();
                    $tp = new Template($content, $this->data, $this->target);
                } else {//php native parse
                    while ($entry = $pageData->entry()) {
                        $name = '$' . $entry->key;
                        $value = $entry->value;
                        eval("${name}=\$value;");
                    }
                    require_once $pageFile;
                    $content = ob_get_contents();
                    ob_clean();
                    $tp = new Template($content, null, $pageFile);
                }
                header("Content-type: text/html; charset= $this->charset");
                echo $tp->parse();
                ob_end_flush();
            } else {
                throw new FileNotFoundException(StringUtil::systemFormat(Config::$LP_LANG['file_not_exist'], $pageFile));
            }
        }
    }

}
