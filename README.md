BuzzBundle
==========

BuzzBundle for Buzz library by Kris Wallsmith


Configuration
=============

You can configure your browsers (and sisters) like that:

    buzz:
        browsers:
            hello:
                client: curl
                message_factory: hello
                host: http://julbox.local/app_dev.php

That config creates a Browser service named `hello`, with all services to work (MessageFactory, Client).

Inheritance
===========

`Browsers`, `Clients`, and `MessageFactories` are services.
You can redefine those services with tags.

You can redifine classes, by set your services on container, with appropriate service tags and aliases.

For example, an `HelloBrowser` can be defined with the service definition:

     <service id="my.service.id" class="my.service.class">
        <argument /> <!-- Host -->
        <argument /> <!-- ClientInterface -->
        <argument /> <!-- FactoryInterface -->
        <tag name="buzz.browser" alias="hello" />
    </service>

Same for `MessageFactory` with:

     <service id="my.service.id" class="my.service.class">
        <tag name="buzz.message_factory" alias="hello" />
    </service>

How to use
==========

A service named `buzz` is available with this bundle.
It permits to get `Browser` by name, like that:

    $helloBrowser = $this->get('buzz')->getBrowser('hello');
