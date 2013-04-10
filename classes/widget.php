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

	public function handle_get($data)
	{
		return true;
	}

    public function handle_post($data)
    {
		return true;
	}

    public function errors()
    {
        return false;
    }

    public function type()
    {
        return $this->_type;
    }
}
