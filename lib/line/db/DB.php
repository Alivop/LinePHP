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

namespace line\db;

/**
 * 
 * @interface DB
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db
 */
interface DB
{

    const BOOL = 0;
    const NULL = 1;
    const INT = 2;
    const STR = 3;
    const LOB = 4;

    function commit();

    function close();

    function prepare($sql);

    function autoCommit($autoCommit);

    function rollback();

    //2015-02-05 cancel charset function
    //function charset($charset);
    function connectError();

    function queryError();

    function beginTransaction();

    function query($sql);
}
