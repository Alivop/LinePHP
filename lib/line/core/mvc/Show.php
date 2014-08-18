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
namespace line\core\mvc;

use line\core\util\Map;
use line\core\Config;
use line\core\exception\FileNotFoundException;

/**
 * 
 * @class Show
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\mvc
 */
final class Show extends BaseMVC
{
    public function __construct($pageName, Map $pageData)
    {
        $this->execute($pageName, $pageData);
    }

    private function execute($pageName, Map $pageData)
    {
        $pagePath = Config::$LP_PATH[Config::PATH_PAGE] . LP_DS;
        $pageFile = $pagePath . $pageName;
        if (is_file($pageFile)) {
            while ($entry = $pageData->entry()) {
                $name = '$' . $entry->key;
                $value = $entry->value;
                eval("{$name}=\$value;");
            }
            //var_dump($test);
            header("Content-type: text/html; charset=" . Config::$LP_SYS[Config::SYS_ENCODE]);
            require_once $pageFile;
        } else {
            throw new FileNotFoundException(Config::$LP_LANG['file_not_exist'] . ':' . $pageFile);
        }
    }

}
