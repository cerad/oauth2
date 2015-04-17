<?php

namespace Cerad\Module\AppModule;

use Cerad\Module\KernelModule\KernelApp;
use Cerad\Module\KernelModule\KernelServices;

use Cerad\Component\DependencyInjection\Container;

class App extends KernelApp
{
  public function boot()
  {
    if ($this->booted) return;
    
    $this->container = $container = new Container();
    
    new KernelServices($container);
    
    new AppServices($container);
    
    $this->booted = true;
  }
}