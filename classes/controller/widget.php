<?php

namespace Widgets;

class Controller_Widget extends \Controller_Template
{
    protected $widgets = [];

    public function after($response)
    {
        $assets = \Asset::instance('widget');
        $is_post = \Input::method() == 'POST';
        $posted_for = \Input::post('widget');

        $widgets = [];

        foreach ($this->widgets as $key => $widget)
        {
            if (is_array($widget))
            {
                // Here we allow widgets to be provided in sections
                foreach ($widget as $w)
                {
                    $widgets[$key][] = $this->_ensure_object($w);
                }
            }
            else
            {
                $widgets['content'][] = $this->_ensure_object($widget);
            }
        }


        foreach ($widgets as $group => $items)
        {
            foreach ($items as $widget)
            {
                // Will explode if doesn't support type(), post() or errors() - allow duck typing though
                if ($is_post and $posted_for and $posted_for == $widget->type())
                {
                    // If we've posted a widget, we should perform the GET redirect as is usual
                    if ($widget->handle_post(\Input::post()) and ! $widget->errors())
                    {
                        \Response::redirect(\Request::main()->uri);
                    }

                    // With errors we simply let the page render as usual, assuming the widget handles
                    // its own error conditions.
                }
				$widget->handle_get(\Input::get());
            }

            $view = \View::forge('widgets');
            $view->set('widgets', $items, false);

            $this->template->set($group, $view);
        }

        return parent::after($response);
    }

    protected function _ensure_object($thing)
    {
        if (!is_object($thing))
        {
            return Widget::forge($thing);
        }

        return $thing;
    }
}
