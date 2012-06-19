# BuzzBundle

## Installation

Add BuzzBundle in your `deps` file:

```text
[buzz]
   git=http://github.com/kriswallsmith/Buzz.git

[BuzzBundle]
    git=http://github.com/juliendidier/BuzzBundle.git
    target=/bundles/Buzz/Bundle/BuzzBundle
    version=origin/2.0
```

Enable the bundleRun the vendors script to download the bundle:

```` bash
$ php bin/vendors install
````

Add the `Buzz` namespace to your autoloader:

```` php
# app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Buzz' => __DIR__.'/../vendor/bundles',
));
````

Finally, enable the bundle in the kernel:

``` php
# app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Buzz\Bundle\BuzzBundle\BuzzBundle(),
    );
}
```
