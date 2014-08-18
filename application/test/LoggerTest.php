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
namespace test;

/**
 * 
 * @class LoggerTest
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package test
 */
use line\logger\Logger;
class LoggerTest
{
    public function main()
    {
        $l = Logger::getInstance();
        $l->error("error");
        $l->warn("warn");
        $l->info("info");
        $l->debug("debug");
    }

}
