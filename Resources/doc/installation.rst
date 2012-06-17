BuzzBundle
==========

Installation
------------

Add BuzzBundle in your ``deps`` file:

.. code-block:: text

    [buzz]
        git=http://github.com/kriswallsmith/Buzz.git

    [BuzzBundle]
        git=http://github.com/juliendidier/BuzzBundle.git
        target=/bunles/Buzz/Bundle/BuzzBundle
        version=origin/2.0

Run the vendors script to download the bundle:

.. code-block:: bash

    $ php bin/vendors install

Add the ``Buzz`` namespace to your autoloader:

.. code-block:: php

    # app/autoload.php

    $loader->registerNamespaces(array(
        // ...
        'Buzz' => __DIR__.'/../vendor/bundles',
    ));

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
