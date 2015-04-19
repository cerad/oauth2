<?php

namespace Cerad\Module\AppModule;

class AppServices
{
  public function registerServices($container)
  {
    $container->set('app_controller',function()
    {
      return new \Cerad\Module\AppModule\AppController();
    });
    $appRouteAction = function($request) use ($container)
    {
      $controller = $container->get('app_controller');
      return $controller->indexAction($request);
    };
    $appRouteMatch = function($path) use($appRouteAction)
    {  
      $params = [ '_action' =>  $appRouteAction];
      if ($path === '/') return $params;
      return false;
    };
    $appRouteService = function() use ($appRouteMatch)
    {
      return $appRouteMatch;
    };
    $container->set('route_app',$appRouteService,'routes');

  }
}