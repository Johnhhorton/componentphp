<?php
namespace ComponentPHP;

use ComponentPHP\TemplateCacher;

class Renderer
{
    private $tagRegex = '~<(?P<tag>[A-Z].*)(?:\s(?<attributes>[^>]*)?)?>(?<content>((?!<(?P=tag))(.|\n))*?)?<\/(?P=tag)>~';
    private $attributesRegex = '~(?<attribute>(?<name>[a-zA-Z-]*)="(?<value>[^"]*)")~';
    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function getDir($class)
    {
        return dirname((new ReflectionClass($class))->getFileName());
    }

    private function parseChildAttributes($attributes)
    {
        $self = $this;
        $returnedAttributes = [];
        preg_replace_callback(
            $this->attributesRegex,
            function ($matches) use (&$returnedAttributes,$self) {
                $name = $matches['name'];
                $value = $matches['value'];
                if ($value[0] === '$') {
                    $variableName = substr($value, 1);
                    $value = $self->$variableName;
                }
                $returnedAttributes[$name] = $value;
                return '';
            }, $attributes);
        return $returnedAttributes;
    }

    private function callComponent($tag, $attributes, $content)
    {
        $componentClass = '\\Components\\' . $tag;
        $templateCacher = TemplateCacher::instance();
        if ($templateCacher->hasTemplate($componentClass)) {
            $templateFromFile = $templateCacher->getTemplate($componentClass);
            $componentInstance = new $componentClass();
        } else {
            ob_start();
            $componentInstance = new $componentClass();
            $templateFromFile = trim(ob_get_clean());
            $templateCacher->addTemplate($componentClass, $templateFromFile);
        }
        $returnedComponent = $componentInstance->make($content, $attributes, $templateFromFile);
        return $returnedComponent;
    }

    public function interpolate($template)
    {
        $self = $this;
        //Method interpolation
        $newTemplate = preg_replace_callback_array([
            '/{{\s*?(\w*)\s*?\((.*)\)\s*?}}/' => function ($matches) use ($self) {
                $command = trim($matches[1]);
                $params = explode(',', $matches[2]);
                return call_user_func_array([$self, $command], $params);
            },
        ], $template);

        //Instance variable interpolation
        $newTemplate = preg_replace_callback_array([
            '/{{\s*?(.*?)\s*?}}/' => function ($matches) use ($self) {
                $command = $matches[1];
                $value = $self->$command;
                return $value;
            },
        ], $newTemplate);
        return $newTemplate;
    }

    public function render($template = '')
    {
        $tagRegex = $this->tagRegex;
        $self = $this;
        $template = $this->interpolate($template);
        $rendered = preg_replace_callback(
            $tagRegex,
            function ($matches) use ($self) {
                $content = $matches['content'];
                $tag = $matches['tag'];
                $attributes = $self->parseChildAttributes($matches['attributes']);
                $componentRendered = $self->callComponent($tag, $attributes, $content);
                return $componentRendered;
            }
            , $template);
        if ($rendered !== $template) {
            $rendered = $this->render($rendered);
        }
        return $rendered;

    }
}
