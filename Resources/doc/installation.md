# BuzzBundle

## Installation

### Step 1: Add BuzzBundle in your composer.json

```js
{
    "require": {
        "juliendidier/buzz-bundle": "*"
    }
}
```

### Step 2: Enable the bundle

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
