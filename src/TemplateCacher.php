<?php

namespace ComponentPHP;

final class TemplateCacher
{
    private $templates = [];
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new TemplateCacher();
        }
        return $inst;
    }
    private function __construct()
    {

    }

    public function addTemplate($class, $template)
    {
        $this->templates[$class] = $template;
    }

    public function getTemplate($class)
    {
        if (isset($this->templates[$class])) {
            return $this->templates[$class];
        }

        return false;
    }

    public function hasTemplate($class)
    {
        if (isset($this->templates[$class])) {
            return true;
        }

        return false;
    }
}
