<?php
class Index
{
    function main($name){
        $test = "LinePHP Framework";
        $sayHello = true;
        $v = new \View();
        $v->setVariable("test", $test);
        $v->setVariable("name", isset($name)?$name.' say : ':'');
        $v->setVariable("sayHello", $sayHello);
        return $v;
    }
}
