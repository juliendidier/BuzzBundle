BuzzBundle
==========

Installation
------------

Step 1: Add BuzzBundle in your composer.json
............................................

.. code-block:: js

    {
        "require": {
            "juliendidier/buzz-bundle": "*"
        }
    }

### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

.. code-block:: php

    # app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Buzz\Bundle\BuzzBundle\BuzzBundle(),
        );
    }
