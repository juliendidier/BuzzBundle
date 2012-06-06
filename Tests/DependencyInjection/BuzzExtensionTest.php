<?php

namespace Buzz\Bundle\BuzzBundle\Tests\DependencyInjection;

class BuzzExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Buzz\\BuzzManager')) {
            $this->markTestSkipped('Buzz is not available.');
        }

        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');

        $this->container = new ContainerBuilder();
        $this->container->addScope(new Scope('request'));
        $this->container->setParameter('kernel.bundles', array());
        $this->container->setParameter('kernel.cache_dir', __DIR__);
        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->setParameter('kernel.charset', 'UTF-8');
        $this->container->set('kernel', $this->kernel);
    }

    public function testCreateBrowser()
    {
    }
}
