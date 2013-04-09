<?php

namespace Widgets;

class Widget extends \ViewModel
{
    protected $_type;

    public static function forge($type)
    {
        // Try to autoload the class. If we don't find a widget of that type, construct a default
        $class = 'Widget_' . \Inflector::camelize($type);
        if (\Autoloader::load($class))
        {
            return new $class($type);
        }
        
        return new static($type);
    }

    public function __construct($type)
    {
        $this->_type = $type;
        $this->set('widget_type', $this->_type);

        parent::__construct('view', null, "widgets/$type");
    }

    public function post($data)
    { }

    public function errors()
    {
        return false;
    }

    public function type()
    {
        return $this->_type;
    }
}
