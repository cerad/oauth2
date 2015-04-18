<?php

namespace Cerad\Module\AppModule;

use Cerad\Module\KernelModule\KernelApp;
use Cerad\Module\KernelModule\KernelServices;

use Cerad\Component\DependencyInjection\Container;

use Cerad\Module\OAuthModule\OAuthServices;

class App extends KernelApp
{
  public function boot()
  {
    if ($this->booted) return;
    
    $this->container = $container = new Container();
    
    new AppParameters($container);
    
    new KernelServices($container);
    
    new OAuthServices($container);
    
    new AppServices($container);
    
    $this->booted = true;
  }
}