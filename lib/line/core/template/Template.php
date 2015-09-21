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
use line\core\template\exception\TemplateException;

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
    private $domcument;

    public function __construct($content = null, $data = null, $description = '')
    {
        $this->content = $content;
        $this->data = $data;
        $this->description = $description;
        $this->domcument = null;
    }

    public function parse()
    {
        if (!isset($this->content)) {
            throw new TemplateException($this->description . ':' . Config::$LP_LANG['empty_template'], ERROR_500);
        }
        $content = $this->parseConstant($this->content);
        if (isset($this->data)) {
            //$content = $this->parseExpression($content, $this->description, $this->data);
            $content = $this->includeContent($content);
            $content = $this->parseNode($content);
            $content = $this->parseComment($content);
            $content = $this->parseNote($content);
        }
        return $content;
    }

    public function includePath(&$name)
    {
        if (!$this->isEmptyString($name)) {
            $name = $this->transformPath($name);
            return true;
        }
        return false;
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
        return preg_replace('/\s*@{[^}]*}/s', '', $content);
    }

    private function parseExpression($content, $desc, $data)
    {
        $content = $this->parseConstant($content);
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

    private function includeContent($content)
    {
        $desc = $this->description;
        $obj = $this;
        return preg_replace_callback('/<lp:include\s+content\s*=\s*(("([\/\\\\s\w\-\.+=(){}~!@#$%^&]*)")'
                . '|(\'[\/\\\\s\w\-\.+=(){}~!@#$%^&]*\'))\s*\/\s*>/i', function($match) use ($obj, $desc) {
            if (isset($match[1])) {
                $name = substr($match[1], 1, strlen($match[1]) - 2);
                $name = str_replace(" ", "", $name);
                if ($obj->includePath($name)) {
                    if (is_file($name)) {
                        return file_get_contents($name);
                    }
                }
            }
            throw new TemplateException($desc . ':' . Config::$LP_LANG['template_exception_include'] . ',' . htmlspecialchars($match[0]), ERROR_500);
        }, $content);
    }

    private function parseLayout($dom, &$content)
    {
        $fragment = $dom->getElementsByTagNameNS('http://www.linephp.com', "layout");
        if ($fragment && $fragment->length == 1) {
            $fragNode = $fragment->item(0);
            $target = $fragNode->getAttribute("target");
            if (!$this->isEmptyString($target)) {
                //$target = $this->parseExpression($target, $this->description, $this->data);
                $fileName = $this->transformPath($target);
                if (is_file($fileName)) {
                    $layout = file_get_contents($fileName);
                    preg_match('/<\s*lp:layout[^>]+>(.*)<\s*\/lp:layout\s*>/Uis', $fragNode->C14N(), $matches);
                    $layout = preg_replace('/\s*<\s*lp:content\s*\/\s*>\s*/i', $matches[1], $layout);
                    $content = $this->includeContent($layout);
                    return true;
                }
            }
            throw new TemplateException($this->description . ':' . Config::$LP_LANG['template_exception_layout'] . ',' . $fileName, ERROR_500);
        }
        return false;
    }

    private function isSkipNode($node)
    {
        if ($node->nodeType == 3) {
            if (preg_match('/^\s*$/', $node->textContent)) {
                return true;
            }
        }
        return false;
    }

    private function isEmptyString(&$str)
    {
        if (!isset($str))
            return true;
        $str = str_replace(" ", "", $str);
        if ($str == '')
            return true;
        return false;
    }

    private function transformPath($path)
    {
        $path = $this->parseExpression($path, $this->description, $this->data);
        $path = str_replace("\\", "/", $path);
        return preg_replace_callback('/^[\/\\\]+/', function($match) {
            return '';
        }, $path);
    }

    /**
     * 2014-09-02 convert any special symbol
     * @param type $content
     * @param \DOMDocument $dom
     * @param type $tag
     * @param type $temp
     * @return type
     * @throws TemplateException
     */
    private function parseNode(&$content, $dom = null, $tag = false, $temp = null)
    {
        if (isset($dom)) {
            $html = $content;
        } else {
            $content = str_replace(array("&"), array("&amp;"), $content);
            $dom = new \DOMDocument();
            $dom->loadXML($content);
            if ($this->parseLayout($dom, $content)) {
                return $this->parseNode($content);
            }
            $htmls = $dom->getElementsByTagName("html");
            if ($htmls && $htmls->length == 1) {
                $html = $htmls->item(0);
            } else {
                throw new TemplateException($this->description . ':' . Config::$LP_LANG['template_exception_html'], ERROR_500);
            }
        }
        if ($html->nodeType == 8) {
            return;
        }
        $childs = $html->childNodes;
        //foreach ($html->childNodes as $node) {
        for ($i = 0; $i < $childs->length; $i++) {
            $node = $childs->item($i);
            if (!$this->isSkipNode($node)) {
                //process attributes
                $attributes = $node->attributes;
                if ($attributes) {
                    foreach ($attributes as $attribute) {
                        $attribute->value = $this->parseExpression($attribute->value, $this->description, $this->data);
                    }
                }
                if ($node->nodeType == 3) {//process text node
                    $node->nodeValue = $this->parseExpression($node->nodeValue, $this->description, $this->data);
                } elseif ($node->nodeName == 'lp:if') {//process 'if' tag
                    $this->parseIf($node, $html, $dom, $temp);
                } elseif ($node->nodeName == 'lp:for') {//process 'for' tag
                    $this->parseFor($node, $html, $dom, $temp);
                } else {
                    $node = $this->parseNode($node, $dom);
                }
            }
        }
        if ($tag) {
            if (!isset($temp))
                $temp = $dom->createElement("lp:temp");
            $length = $childs->length;
            for ($i = 0; $i < $length; $i++) {
                if (!($this->isSkipNode($childs->item($i)) && $i + 1 < $length && !$this->isSkipNode($childs->item($i + 1)))) {
                    $temp->appendChild($childs->item($i)->cloneNode(true));
                }
            }
            return $temp;
        }
        if (is_string($content)) {
            //return $dom->saveXML();
            return preg_replace(array("/\s*<lp:temp>/", "/\s*<\/lp:temp>/", "/&amp;/"), array("", "", "&"), $dom->saveHTML());
        } else {
            return $html;
        }
    }

    private function parseIf($node, $html, $dom, &$temp)
    {
        $test = $node->getAttribute("test");
        $value = $this->transform($test, $this->data, $this->description);
        if (is_bool($value) || is_numeric($value)) {
            $value = intval($value);
            $else = $node->getElementsByTagName('else');
            $else = $else->length == 1 ? $else->item(0) : null;
            if ($value > 0) {
                if ($else) {
                    $node->removeChild($else);
                }
                $temp = $this->parseNode($node, $dom, true);
                //$tmp = $dom->getElementsByTagName("lp:temp")->item(0);
                $html->replaceChild($temp, $node);
            } else {
                if ($else) {
                    $temp = $this->parseNode($else, $dom, true);
                    $html->replaceChild($temp, $node);
                } else {
                    $html->removeChild($node);
                }
            }
        }
    }

    private function parseFor($node, $html, $dom, &$temp)
    {
        $source = $node->getAttribute("source");
        $value = $node->getAttribute("value");
        $index = $node->getAttribute("index");
        if (!isset($source) || !isset($value)) {
            throw new TemplateException($this->description . ':' . Config::$LP_LANG['template_exception_for'], ERROR_500);
        }
        $obj = $this->data->get($source);
        if (is_object($obj) || is_array($obj)) {
            $i = 1;
            foreach ($obj as $item) {
                $itemNode = $node->cloneNode(true);
                $this->data->set($index, $i);
                $this->data->set($value, $item);
                $i++;
                $temp = $this->parseNode($itemNode, $dom, true, $temp);
            }
        }
        if (!isset($temp))
            $temp = $dom->createElement("lp:temp");
        $html->replaceChild($temp, $node);
    }

    private function parseConstant($content)
    {
        return preg_replace(array('/\{LP\}/', '/\{LP_A\}/', '/\{LP_P\}/', '/\{LP_T\}/'), array(LP, LP_A, LP_P, LP_T), $content);
    }

    private function transform($content, $data, $desc)
    {
        if (!isset($content))
            return null;
        $exp = new Expression($content, $data, $desc);
        return $exp->parse();
    }

}
