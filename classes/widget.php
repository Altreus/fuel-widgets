<?php

namespace Widgets;

class Widget extends \ViewModel
{
    protected $_type;

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
