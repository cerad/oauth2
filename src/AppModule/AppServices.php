<?php

namespace Cerad\Module\AppModule;

class AppServices
{
  public function __construct($container)
  {
    $container->set('app_controller',function()
    {
      return new \Cerad\Module\AppModule\AppController();
    });
    $appAction = function($request) use ($container)
    {
      $controller = $container->get('app_controller');
      return $controller->indexAction($request);
    };
    $appRoute = function($path) use($appAction)
    {  
      $params = [ '_action' =>  $appAction];
      if ($path === '/') return $params;
    };
    $appRouteService = function() use ($appRoute)
    {
      return $appRoute;
    };
    $container->set('route_app',$appRouteService,'routes');

  }
}