<?php

namespace Widgets;

class Widget extends \ViewModel
{
    protected $_type;

	protected $_data = [];

	public function & get($key = null, $default = null)
	{
		if (isset($this->_data[$key]))
		{
			return $this->_data[$key][0];
		}
		return $default;
	}

	public function set($key, $value, $filter = null)
	{
		is_null($filter) and $filter = $this->_auto_filter;

		$this->_data[$key] = [$value, $filter];

		return $this;
	}

	public function after()
	{
		foreach ($this->_data as $key => $value)
		{
			parent::set($key, $value[0], $value[1]);
		}
	}

    public function __construct($type)
    {
        $this->_type = $type;

        parent::__construct('view', null, "widgets/$type");

        $this->set('widget_type', $this->_type);
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
