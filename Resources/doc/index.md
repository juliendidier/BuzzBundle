# BuzzBundle

## Installation

If you have not yet installed BuzzBundle, see the [Installation](./Resources/doc/installation.md) guide.

## How to use

I want to set Curl requests on Google.

The `config` should like this:

``` yaml
buzz:
    google:
        client: curl
        host: http://www.google.com
```

And the code which execute a request to `http://www.google.com/`:

``` php
$buzz = $this->container->get('buzz');
$browser = $buzz->get('google');
$response = $browser->get('/');

echo $browser->getLastRequest()."\n";
echo $response;
```

## Configuration

``` yaml
buzz:
    profiler:  %kernel.debug%
    listeners:
        # Example:
        foo:    some.service.id
    browsers:
        # Example:
        foo:
            client: curl
            message_factory: ~
            host: 'http://localhost'
            listeners: [ foo ]
```

### profiler:

**type**: `boolean` **default**: `%kernel.debug%`

If `profiler` is `true`, a buzz panel will be available in the ``Symfony Profiler``.

### listeners:

**type**: ``array`` **default**: ``array()``

Listeners are used to hook the method ``send`` in `Buzz\\Browser`.

### listener:

**type**: ``string``

The ``listener`` (`Buzz\\Listener\\ListenerInterface`) configuration
accepts only a `service` identifier (see [Custom listener](#custom-listener) section).

### browsers:

**type**: `array` **default**: `array()`

It is a collection of browsers you can instanciate without creating dedicated services.

### browser:

**type**: `array` **default**: `array()`

For every `browser` (`Buzz\\Browser`), you can define:

- a `client` (`Buzz\\Client\\ClientInterface`)
- a `factory_message` (`Buzz\\Message\\Factory\\FactoryInterface`)
- a `host` (`Buzz\\Listener\\HostListener`)
- a `listener` (`Buzz\\Listener\\ListenerInterface`) (see [Custom listener](#custom-listener) section)


### client:

**type**: `string`

You can use the default clients (`Buzz\\Client\\ClientInterface`),
defined by
the [Buzz](https://github.com/kriswallsmith/Buzz) library.

The defined clients are:

- `curl` (see `Buzz\\Client\\Curl`)
- `multi_curl` (see `Buzz\\Client\\MultiCurl`)
- `file_get_contents` (see `Buzz\\Client\\FileGetContents`)


### message_factory:

**type**: `string` default `null`

A `message_factory` (`Buzz\\Message\\Factory\\FactoryInterface`) is a factory to create:

- `request` (see `Buzz\\Message\\RequestInterface`)
- `response` (see `Buzz\\Message\\MessageInterface`)

If you don't configure a ,
the `browser` create a generic `message_factory` (see `Buzz\\Message\\Factory\\Factory`).


### host:

**type**: `string` default `null`

The `host` configuration is to set a preconfigured host for your requests
(see `Buzz\\Message\\Factory\\Factory:setHost`).

This configuration adds a `Buzz\\Bundle\\Listener\\HostListener`
in the `browser` (see `Buzz\\Browser:setListener`).


## Customs

### Custom browser:

You can redefine the class of your browser, by creating a service tags with
`buzz.browser` :

``` xml
<services>
    <service id="some.service.id" class="My\Custom\Class">
        <argument /> <!-- ClientInterface -->
        <argument /> <!-- FactoryInterface -->
        <tag name="buzz.browser" alias="foo" />
    </service>
</services>
```

The initial configuration is used for your custom service. You don't have to
redefine `client` and `message_factory` arguments
(see `Buzz\\Browser`).

Your custom class must implement `Buzz\\Browser`.

### Custom listener:

Custom listener can be used for authenticated requests.
An example of a listener service, with `%my_token%` dependency:

The `config`:

``` yaml
buzz:
    listeners:
        token: acme_client.buzz.listener.token
    browsers:
        google:
            client: curl
            host: http://www.google.com
            listeners: [ token ]
```

The `service` definition:

``` xml
<services>
    <service id="acme_client.buzz.listener.token" class="Acme\Bundle\ClientBundle\Buzz\Listener\TokenListener">
        <argument>%my_token%</argument>
    </service>
</services>
```

The `listener` class:

``` php
# Acme\Bundle\ClientBundle\Buzz\Listener\TokenListener

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Cookie;
use Buzz\Util\CookieJar;

class TokenListener implements ListenerInterface
{

    // ...

    public function preSend(RequestInterface $request)
    {
        $jar = new CookieJar();
        $cookie = new Cookie();
        $cookie->setName('token');
        $cookie->setValue($this->token);
        $cookie->setAttribute('domain', parse_url($request->getHost(), PHP_URL_HOST));

        $jar->addCookie($cookie);
        $jar->addCookieHeaders($request);
    }

    function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}
```
