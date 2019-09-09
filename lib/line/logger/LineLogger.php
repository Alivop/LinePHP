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

use line\core\Config;

/**
 * Super logger of Line.
 * @class LineLogger
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\logger
 */
abstract class LineLogger
{
    final public function fatal($message)
    {
        $this->log(Level::FATAL, $message);
    }

    final public function error($message)
    {
        $this->log(Level::ERROR, $message);
    }

    final public function warn($message)
    {
        $this->log(Level::WARN, $message);
    }

    final public function info($message)
    {
        $this->log(Level::INFO, $message);
    }

    final public function debug($message)
    {
        $this->log(Level::DEBUG, $message);
    }
    
    final public function system($message)
    {
        $this->log(Level::SYSTEM, $message);
    }

    final public function log($type, $message, $file = '')
    {
        $level = Level::intValue(Config::$LP_LOG['level']);
        if ($level == Level::OFF) return;
        if ($type <= $level) {
            $this->write(Level::stringValue($type), $type, $message, $file);
        }
    }

    private function write($type, $level, $message, $file = '')
    {
        $layout = Config::$LP_LOG['layout'];
        $head = "<b>[{$type}]" . date($layout)."</b>";
        if (!empty($file)) {
            $head .= "[$file]";
        }
        $message = $this->deal($head.$message);
        switch ($level) {
            case Level::FATAL :
                $this->rewriteFatal($message);
                break;
            case Level::ERROR :
                $this->rewriteError($message);
                break;
            case Level::WARN :
                $this->rewriteWarn($message);
                break;
            case Level::INFO :
                $this->rewriteInfo($message);
                break;
            case Level::DEBUG :
                $this->rewriteDebug($message);
                break;
            case Level::SYSTEM :
                $this->rewrite($message);
                break;
        }
    }
    
    abstract protected function deal($message);
    abstract protected function rewriteDebug($message);
    abstract protected function rewriteInfo($message);
    abstract protected function rewriteWarn($message);
    abstract protected function rewriteError($message);
    abstract protected function rewrite($message);
}
