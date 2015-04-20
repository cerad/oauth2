<?php

namespace Cerad\Module\AppModule;

use Cerad\Module\KernelModule\KernelApp;
use Cerad\Module\KernelModule\KernelServices;

use Cerad\Module\OAuthModule\OAuthServices;

class App extends KernelApp
{
  protected function registerServices($container)
  { 
    new AppParameters($container);
    
    $kernelServices = new KernelServices();
    $kernelServices->registerServices($container);
    
    new OAuthServices($container);
    
    $appServices = new AppServices();
    $appServices->registerServices($container);
  }
}