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

namespace line\core\template;

use line\core\LinePHP;
use line\core\Config;
use line\core\template\Expression;
use line\core\exception\TemplateException;

/**
 * The template class
 * @class Template
 * @link  http://linephp.com
 * @author Alivop[alivop.liu@gmail.com]
 * @since 1.3
 * @package line\core\template
 */
class Template extends LinePHP
{

    private $content;
    private $data;
    private $description;
    const ID = "LP_TEMPLATE_TEMP_ID";

    public function __construct($content = null, $data = null, $description = '')
    {
        $this->content = $content;
        $this->data = $data;
        $this->description = $description;
    }

    public function parse()
    {
        if (!isset($this->content)) {
            throw new TemplateException($this->description . ':' . Config::$LP_LANG['empty_template'],500);
        }
        if (isset($this->data)) {
            $content = $this->parseComment($this->content);
            $content = $this->parseNote($content);
            $content = $this->parseExpression($content, $this->description, $this->data);
            $content = $this->parseIf($content);
        } else {
            $content = $this->content;
        }
        return $this->parseConstant($content);
    }

    private function parseComment($content)
    {
        return preg_replace_callback('/#{([^}]*)}/s', function($match) {
            if (count($match) == 2 && !empty($match[0])) {
                return '<!-- ' . $match[1] . ' -->';
            }
        }, $content);
    }

    private function parseNote($content)
    {
        return preg_replace('/\s*@{([^}]*)}\s*/s', '', $content);
    }

    private function parseExpression($content, $desc, $data)
    {
        return preg_replace_callback('/\${([\s!\sa-zA-Z0-9.+\-*\/%\s]+)}/', function($match) use ($desc, $data) {
            if (count($match) == 2 && !empty($match[0])) {
                $var = str_replace(' ', '', $match[1]);
                $exp = new Expression($var, $data, $desc);
                $value = $exp->parse();
                if ($value === true) {
                    $value = 1;
                } elseif ($value === false) {
                    $value = 0;
                }
                return $value;
            }
        }, $content);
    }

    private function parseIf($content)
    {
        var_dump($content);
        $callback = function($matchs){
            var_dump($matchs);
        };
        preg_replace_callback('/<\s*if\s*test\s*=\s*([^>]+)>(.*)<\s*\/if\s*>/is', $callback, $content);
        
        
        
        
        exit();        
        $html = new \SimpleXMLElement($content);
        //$html = new \DOMDocument();
        //$html->loadXML($content);
        //$tempNode = $html->createElement("div")->setAttribute("id",self::ID);
        //$result = $html->getElementsByTagName('if');
        $result = $html->xpath('body/If');
        foreach ($result as $if) {
            $ifRaw = $if->asXML();
            var_dump($ifRaw);
            $attrTest = $if->getAttribute('test');
            if (!empty($attrTest)) {
                $value = $this->transform($attrTest, $this->data, $this->description);
                if(is_bool($value)||  is_numeric($value)){
                    $value = intval($value);
                    $elseNode = $if->getElementsByTagName('else');
                    if($value>0) {
                        if($elseNode->length>0)$if->removeChild($elseNode->item(0));
                        
                    }else{
//                        preg_match('/<\s*else[^>]+>((\s|.)*)<\s*\/else\s*>/',$ifRaw ,$match);
//                        if(isset($match[1])) {
//                            $ifFalse = $match[1];
//                        }else{
//                            $ifFalse = '';
//                        }
//                        $content = str_replace($ifRaw, $ifFalse , $content);
                    }
                }else{
                    throw new TemplateException($this->description . ':' . Config::$LP_LANG['template_exception_if'],500);
                }
            }else{
                throw new TemplateException($this->description . ':' . Config::$LP_LANG['template_exception_if'],500);
            }
            
        }
        return $content;
        //exit();
        $html = new \DOMDocument();
        $html->loadXML($content);
        //echo $html->saveHTML();
        $elements = $html->getElementsByTagName("if");
        foreach ($elements as $element) {
            $test = trim($element->getAttribute("test"));
            $inverse = false;
            if (stripos($test, "!") === 0)
                $inverse = true;
            $test = substr($test, 1);
            if (stripos($test, ">") > 0) {
                $expression = explode(">", $test);
            } elseif (stripos($test, "<") > 0) {
                $expression = explode("<", $test);
            } elseif (stripos($test, "<>") > 0) {
                $expression = explode("<>", $test);
            } elseif (stripos($test, "=") > 0) {
                $expression = explode("=", $test);
            } else {
                $expression = array($test);
            }
            $leftValue = explode(".", $test);
            if ($test == '')
                continue;
            if (count($leftValue) > 0) {
                $object = $pageData->get($leftValue[0]);
                if (empty($object) || !is_object($object)) {
                    throw new TemplateException(Config::$LP_LANG['template_exception'] . ': "' . $test . '" is invalid.');
                } else {
                    $value = $object;
                    for ($i = 1; $i < count($leftValue); $i++) {
                        $v = $leftValue[$i];
                        if (property_exists($value, $v)) {
                            $value = $value->$v;
                        } else if (method_exists($value, $v)) {
                            $value = $value->$v();
                        } else {
                            throw new TemplateException(Config::$LP_LANG['template_exception'] . ': "' . $test . '" is invalid.');
                        }
                    }
                }
            } elseif (strcasecmp($test, 'true') == 0 || (is_numeric($test) && $test > 0)) {
                $condition = true;
            } else {
                $condition = false;
            }
            if ($condition) {
                $element->removeChild($element->getElementsByTagName("else")->item(0));
            }
            $childs = $element->childNodes;
            foreach ($childs as $sub) {
                
            }
        }
        $content = $html->saveHTML();
    }

    private function parseFor()
    {
        
    }

    private function parseConstant($content)
    {
        return preg_replace(array('/\{LP\}/', '/\{LP_A\}/', '/\{LP_P\}/', '/\{LP_T\}/'), array(LP, LP_A, LP_P, LP_T), $content);
    }

    private function transform($content, $data, $desc)
    {
        $exp = new Expression($content, $data, $desc);
        return $exp->parse();
    }

}
