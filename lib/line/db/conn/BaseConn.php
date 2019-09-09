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
namespace line\db\conn;

/**
 * 
 * @class Base
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.0
 * @package line\core\db\conn
 */
abstract class BaseConn extends \line\core\LinePHP
{
    protected $conn;
    protected $domain;
    protected $anonymous;

    function setDomain($domain)
    {
        if (!isset($domain)) {
            return;
        }
        $this->domain = $domain;
        $this->anonymous = 'LpAnonymousClass'.mt_rand();
        $tmp = <<<EOF
class $this->anonymous extends $domain {
    public function __set(\$name, \$value)
    {
        \$name = preg_replace_callback('/[-_]+([a-z]{1})/i',function(\$matches){
            return strtoupper(\$matches[1]);
        }, \$name);
        \$setMethod = 'set'.ucfirst(\$name);
        if (method_exists(\$this, \$setMethod)) {
            \$this->\$setMethod(\$value);
        }
    }
    
    public function __get(\$name)
    {
        \$n =  preg_replace_callback('/[-_]+([a-z]{1})/i',function(\$matches){
            return strtoupper(\$matches[1]);
        }, \$name);
        \$getMethod = 'get'.ucfirst(\$name);
        if (method_exists(\$this, \$getMethod)) {
            return \$this->\$getMethod();
        }
        return null;
    }
}
EOF;
        eval($tmp);
    }

    abstract function close();

    function __destruct() {
        $this->close();
    }

}
