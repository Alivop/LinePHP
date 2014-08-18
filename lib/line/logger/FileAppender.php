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
namespace line\logger;

//use line\core\exception\IllegalArgumentException;
use line\core\Config;
use line\core\exception\FileOpenException;

/**
 * Save logs in file .
 * @class FileAppender
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\logger
 */
class FileAppender extends LineLogger
{
    private $file;
    private $fileStream;

    public function __construct()
    {
        $this->file = Config::$LP_LOG['file'];
    }

    protected function deal($message)
    {
        $filePattern = Config::$LP_LOG['file_pattern'];
        if (file_exists($this->file)) {
            $now = date($filePattern);
            $fileDate = date($filePattern, filemtime($this->file));
            $fileDate = str_replace(array(
                ':', '|', '?', '/', '\\', '<', '>', '*', '"'), '', $fileDate);
            if (strcasecmp($now, $fileDate)) {
                rename($this->file, $this->file . "$fileDate");
            }
        }
        $this->fileStream = fopen($this->file, 'ab');
        if (!$this->fileStream) {
            throw new FileOpenException(realpath($this->file) . ' open fail.');
        }
        //deal message
        return preg_replace("/^<b>(.*)<\/b>/", "$1", $message);
    }

    protected function rewriteDebug($message)
    {
        $this->append($message);
    }

    protected function rewriteInfo($message)
    {
        $this->append($message);
    }

    protected function rewriteWarn($message)
    {
        $this->append($message);
    }

    protected function rewriteError($message)
    {
        $this->append($message);
    }
    
    protected function rewrite($message){
        $this->append($message);
    }

    private function append($message)
    {
        fwrite($this->fileStream, $message . "\r\n");
        fclose($this->fileStream);
    }

}
