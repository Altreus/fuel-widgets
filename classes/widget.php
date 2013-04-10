<?php

namespace Widgets;

class Widget extends \ViewModel
{
    protected $_type;
	protected $_errors = [];

	protected $_data = [];

	/**
	 * Retrieve data filed under $key, or $default if no data.
	 *
	 * This is the raw data before filtering.
	 *
	 * @param 	$key	string	Key to retrieve.
	 * @param	$default	string	Optional. Return value if $key not found.
	 * @return	mixed	Data set under $key or $default
	 */
	public function & get($key = null, $default = null)
	{
		if (isset($this->_data[$key]))
		{
			return $this->_data[$key][0];
		}
		return $default;
	}

	/**
	 * Set data under $key to $value.
	 *
	 * @param	$key	string	Key to set
	 * @param	$value	mixed	Value to set
	 * @param	$filter	bool	Optional. Define whether to filter this value when it is later set on the view.
	 * @return	$this
	 */
	public function set($key, $value, $filter = null)
	{
		is_null($filter) and $filter = $this->_auto_filter;

		$this->_data[$key] = [$value, $filter];

		return $this;
	}

	/**
	 * Lazily sets the data into the real View object.
	 *
	 * Also applies the remembered $filter settings from set().
	 */
	public function after()
	{
		foreach ($this->_data as $key => $value)
		{
			parent::set($key, $value[0], $value[1]);
		}

		parent::set('errors', $this->_errors);
	}

	/**
	 * Create a new Widget that uses widgets/$type as its view.
	 *
	 * The intention is that you subclass Widget and pass its type to this constructor.
	 *
	 * @param 	$type	string	The type of widget.
	*/
    public function __construct($type)
    {
        $this->_type = $type;

        parent::__construct('view', null, "widgets/$type");

        $this->set('widget_type', $this->_type);
    }

	/**
	 * Deal with any GET string data from the request.
	 *
	 * @return	bool	Whether handling was performed.
	 */
	public function handle_get($data)
	{
		return false;
	}

	/**
	 * Deal with any POST data from the request.
	 *
	 * When overriding, remember to return true iff you *handled* the POST data, even if there were errors.
	 *
	 * Only return false if you didn't want to use the data.
	 *
	 * @param	$data	array	POST data associative array.
	 * @return	bool	Whether the data were handled by this widget.
	 */

    public function handle_post($data)
    {
		return false;
	}

	/**
	 * Return any errors from the POST request.
	 *
	 * This is only called if handle_post returned true.
	 *
	 * @return	mixed	At minimum a value that boolifies to indicate whether there were POST errors
	 */
    public function errors()
    {
        return $this->_errors;
    }

	/**
	 * Return a string identifying the widget's type
	 *
	 * This is used to compare the POST value 'widget', to determine that this widget may have POST data for it.
	 *
	 * @return	string	Widget type
	 */
    public function type()
    {
        return $this->_type;
    }
}
