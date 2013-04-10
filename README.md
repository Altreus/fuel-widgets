# Widgets

Widgets is a Fuel controller that allows you to easily create widget-based views.

The widget controller is based on the builtin template controller (or whatever template controller
you override that with). It allows you to set any number of widget groups to suit your template, but
by default it creates a single view with all the widgets in and outputs it as the template's
"content" variable.

## What counts as a widget?

There are three ways to define widgets for this controller:

1. A Widget object you constructed yourself
2. A View object
3. Any object, within limitations - see below about GETs and POSTs

Using strings was originally going to be supported, but PHP will not let me override ViewModel's
forge method. So I can't. Plus it made no sense.

## Creating widgeted actions

To create a widgeted action you'll need a controller, obviously, that extends `Controller_Widget`.
In that, you simply add widgets to `$this->widgets`, an array of anything valid according to the
rules.

Widgets defined thus will be put into a view, which will itself be put into the `content` part of
your template.

If your template requires or supports multiple areas of content you can also make this a 2D array,
and have `$this->widgets` be an associative array whose keys will become the variables in your
template. Thus adding to `$this->widgets` is equivalent to adding to `$this->widgets['content']`.

Widgets will be output in the order they are added.

## GETs and POSTs

Widgets extend ViewModel because ViewModels are expected to fetch their own data.

The thinking behind this is that most widgets on a page will do something, but the interactions
between them can be a bit complicated. Thus it is probably sensible to have the things they do
happen via AJAX instead of regular GET/POST. Since, in turn, it is not up to me how you do your
AJAX, I leave it open to the user to determine how to send and retrieve updates to the widget
itself.

The exception to this rule is for standard POST requests.

### POST requests

A widget is allowed to contain a form. The form is expected to post to the URL in question; i.e.
itself. This is the standard way of ensuring the same page is rendered on error.

For a widget to support POST in this manner it must:

1. Extend Widget; or
2. Support `type()`, `handle_get()`, `handle_post()` and `errors()` methods

Furthermore, the form in the widget must send as part of the POST body a value by the key of
`widget`. This identifies the type of the widget being posted to.

A future update may have this done by name but currently widgets are not named. Give me half a
chance eh?

The widget controller's behaviour will then be to pass the post data to the widget's `post()` method
if the value of `widget` in the post data matches the type of widget. Once done, the widget's
`errors()` method is called, and if a falsey value is returned the POST is considered successful.

On POST success, the controller redirects back to the same page. The widget should therefore
probably set some session variable to show success state on the refreshed page.

On POST failure, the page rendering continues. Since you cannot click two buttons on two forms, no
other widget will receive the post data. Since rendering continues, the widget with the errors will
be output to the page, and is assumed to have rendered its own problems with it.

## Widget class

The Widget class is an extension of the ViewModel class. It adds some extra methods required to work
as a widget:

* `handle_get($data)` - Give the widget an opportunity to handle GET data or register assets
* `handle_post($data)` - Accepts post data and handles it in any way necessary. Returns true if handled
* `errors()` - Needs only return whether there were any errors during `post()`
* `type()` - Returns the type of the widget, which should match the value of the `widget` POST parameter

Widget is not abstract because it is possible (albeit of limited use) to create a get-only widget.
In this case it will follow the normal behaviour of ViewModel, which is to look for a view by the
defined name of the widget.

### Extending Widget

The base constructor accepts the type of widget. Feel free to omit this from your constructor, but
be sure to provide a hard-coded value when you call the parent constructor.

The type parameter is used for several things:

1. It is passed to the rendered View as `$widget_type`
2. It constructs the default view name (`"widgets/$type"`)
3. It identifies the widget for POST requests (the thing 1. is for)

It is saved in the `$_type` property of the object and is returned in the default `type()` method.

In general it is easiest to marry up the type passed to the constructor, the class name of the
widget and the view. That way, the default behaviour should work fine. You can still use the methods
available on ViewModel to amend the Widget afterwards.

Widgets are expected to be able to fetch their own data. There is no provision for rendering
a Widget standalone. The reason for this is that most things that communicate with the server via
AJAX or Comet will do so using some RESTful controller (or should). That means that the ability to
render a widget standalone is not globally useful. It wasn't added as a default option because it is
trivial to write a controller that will do so anyway, since the widgets are already standard
ViewModels.

Hence, to keep the package size down, I recommend using the same source of data for your ViewModel's
`view()` method as you do for the web-facing RESTful controller; in fact I might even go so far as
to suggest that, if you want Javascript functionality in your widget, you write the REST stuff
first, return arrays from that, and use HMVC to internally fetch the same data in pure PHP array
format for the widget itself.

## Assets

The controller forges an instance of Asset by the name of 'widget'. Widgets are encouraged to add
either their paths or their individual files to this instance when forged. Note that it is too late
to inject these assets when `view()` is called because templates are rendered progressively.

It is not generally considered friendly to include `<script>` and `<link>` tags inside the widgets
themselves, but sometimes you just gots ta.

Since Asset works in instances, your template should look for `Asset::instance('widget')` rather
than expecting a variable to be passed through.

## The widget view

I'll describe this here for completeness. Each widget object is rendered sequentially inside a
simple view that literally wraps a `<section>` around each one.

You can override this view in your app if you really want to; simply iterate the `$widgets` variable
inside it, and echo out each element.
