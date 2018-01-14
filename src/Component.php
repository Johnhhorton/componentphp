<?php
namespace ComponentPHP;
use ComponentPHP\Renderer;
class Component extends Renderer{

    public $content = '';
    public $attributes = [];
    public $template = '';
    public $props = [];

    public function parseProps($props){
        return $props;
    }

    public function getFile(){
        $a = new \ReflectionClass(get_class($this));
         return $a->getFileName();
    }

    public function mergeAttributesIntoProps($attributes, $props){
        return array_merge($props, $attributes);
    }

    public function make($content, $attributes, $templateFromFile){
        $this->props = $this->mergeAttributesIntoProps($attributes, $this->props);
        $this->props = $this->parseProps($attributes);
        $this->content = $content;
        $this->created();
        return $this->render($templateFromFile);
    }

    public function created(){

    }
}