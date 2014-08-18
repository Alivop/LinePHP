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

/**
 * Show logs on console .
 * @class ConsoleAppender
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\logger
 */
class ConsoleAppender extends LineLogger
{
    protected function rewriteDebug($message)
    {
        echo $message;
    }

    protected function rewriteInfo($message)
    {
        echo $message;
    }

    protected function rewriteWarn($message)
    {
        echo $message;
    }

    protected function rewriteError($message)
    {
        echo $message;
    }
    
    protected function rewrite($message){
        echo $message;
    }

    protected function deal($message)
    {
        return "<span style='color:red;font-weight:bold;'>[LineLog]</span>".$message.'<br/>';
    }

}
