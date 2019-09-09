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

use line\core\util\JSON;
use line\core\util\Map;
use line\core\exception\FileNotFoundException;

/**
 *
 * @class View
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\mvc
 */
class View extends Page
{
    public function __construct($page = null, Data $data = null)
    {
        if ($page instanceof Page) {
            $this->setPage($page);
        } else if (is_string($page)) {
            $this->setPage(new Page($page));
        }
        if (isset($data)) {
            $this->dataMap = $data->dataMap;
        } else {
            $this->dataMap = new Map();
        }
    }

    /**
     * 2014-04-15 修复setDate后调用setPage导致data为空。
     * @param \line\core\mvc\Page $page
     */
    public function setPage(Page &$page)
    {
        $this->name = $page->name;
        if (isset($page->dataMap)) {
            $this->dataMap->addAll($page->dataMap);
        }
    }

    /**
     * URL跳转。
     * @param type $url
     * @throws FileNotFoundException
     */
    public function setRedirect($url)
    {
        if (!empty($url)) {
            header("Location: $url");
            exit(0);
        } else {
            throw new FileNotFoundException(str_replace('{}', '', Config::$LP_LANG['file_not_exist'] . ':' . $url));
        }
    }

    public function toJSON(array $ignoreVars = null)
    {
        return JSON::stringify($this->dataMap, $ignoreVars);
    }

}
