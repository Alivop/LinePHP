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

/**
 * 
 * @class Index
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package 
 */
class Index
{

    public function main(array $paras)
    {
        //LineGet / GET  
        //var_dump($paras, $_GET);
        //$s = \DbFactory::getConnection();
        //echo $s;
//        putenv('LC_ALL=zh_CN');
//        setlocale(LC_ALL, 'zh_CN');
//        bindtextdomain("hello", "./locale");
//        textdomain("hello");
//        echo _("Hello"); 
        preg_match_all('/\${([a-zA-Z0-9.]+)}/','${va.rd}', $a);
        //var_dump($a);
        
        //$this->parse();
        //$s = new \line\core\template\Conditional("3>0&&0", NULL);
        //var_dump( $s->parse());
        
        preg_replace_callback('/\${([\sa-zA-Z0-9.]+)}/',function($m){
            //var_dump($m);
        },'${va.rd}vdsfav<>vdsa${cvds}');
        
        //exit();
        $v = new \View();
        $print = true;
        $hello = "你好啊~~~";
        $v->setVariable("print", $print);
        $v->setVariable("hello", $hello);
        $v->setVariable("a", 12);
        $v->setVariable("b", 12);
        return $v;
    }

    function parse()
    {
        $e = "!2||(!1||0)";
        $c = array();
        $l = array();
        $y = array('!', '&&', '||', '(', ')');
        $s = '';
        for ($i = 0; $i < strlen($e); $i++) {
            $v = $e{$i};
            if($v=='&'||$v=='|') {
                $v .= $v;
                $i++;
            }
            if (in_array($v, $y)) {
//                if (in_array($v, array('-', '+')) && (($i == 0) || (in_array($e{$i - 1}, $y) && $e{$i - 1} != ')'))) {
//                    $s .= $v;
//                    continue;
//                } else {
                    ($s!='') && ($l[] = $s) ;
                    ($s = '');
//                }
                if ($v == ')') {
                    $p = array_pop($c);
                    while (isset($p) && $p != '(') {
                        $l[] = $p;
                        $p = array_pop($c);
                    }
                } elseif ($v == '(') {
                    $c[] = $v;
                } else {
                    $p = array_pop($c);
                    if ($this->precedence($p, $v)) {
                        $l[] = $p;
                    } else {
                        $c[] = $p;
                    }
                    $c[] = $v;
                }
            } else {
                $s .= $v;
            }
        }
        !empty($s) && ($l[] = $s);
        while ($p = array_pop($c)) {
            $l[] = $p;
        }
        //echo implode(' ',$l)."<br/>";
        var_dump($l);
        $this->calc($l, $f);
        var_dump($f);
    }

    function precedence($top, $exp)
    {
        if (!isset($top) || $top == '(')
            return false;
        $a1 = array('-', '+');
        $t = in_array($top, $a1) ? 1 : 2;
        $e = in_array($exp, $a1) ? 1 : 2;
        if ($e <= $t) {//弹出
            return true;
        } else {//压入
            return false;
        }
    }

    function calc($l, &$value)
    {
        $y = array('!','&&','||');
        var_dump($l);
        for ($i = 0; $i < count($l); $i++) {
            $c = $l[$i];
            $n1 = $i>0&&$l[$i - 1]?true:false;
            if ($c==='&&'||$c==='||') {
                $n2 = ($l[$i - 2])?true:false;
                $v = 0;
                switch ($c) {
                    case '&&':
                        $v = $n1 && $n2;
                        break;
                    case '||':
                        $v = $n2 || $n1;
                        break;
                }
                array_splice($l, $i - 2, 3, $v);
            }elseif($c==='!'){
                $v = !$n1;
                array_splice($l, $i - 1, 2, $v);
            }
        }
        if (count($l) == 1) {
            $value = $l[0];
        } else {
            $this->calc($l, $value);
        }
    }

    function test()
    {
        phpinfo();
    }

}

class test
{

    public $a = 'ssss';

}
