<?php

namespace Cerad\Module\OAuthModule;

class OAuthServices
{
  public function __construct($container)
  {
    if (!$container->has('jwt_coder'))
    {
      $container->set('jwt_coder',function($c)
      {
        return new \Cerad\Component\Jwt\JwtCoder($c->get('secret'));
      });
    }
    $container->set('oauth_providers_config',
    [
      [
        'name'          => 'google',
        'class'         => 'Cerad\Module\OAuthModule\Provider\GoogleProvider',
        'client_id'     => $container->get('oauth_google_client_id'),
        'client_secret' => $container->get('oauth_google_client_secret'),
      ],            
      [
        'name'          => 'github',
        'class'         => 'Cerad\Module\OAuthModule\Provider\GithubProvider',
        'client_id'     => $container->get('oauth_github_client_id'),
        'client_secret' => $container->get('oauth_github_client_secret'),
      ],            
    ]);
    $container->set('oauth_callback_uri','/oauth/callback');
    
    $container->set('oauth_provider_manager',function($container)
    {
      $jwtCoder        = $container->get('jwt_coder');
      $callbackUri     = $container->get('oauth_callback_uri');
      $providersConfig = $container->get('oauth_providers_config');
      
      return new \Cerad\Module\OAuthModule\ProviderManager($jwtCoder,$callbackUri,$providersConfig);
    });
    $container->set('oauth_controller',function($container)
    {
      $jwtCoder        = $container->get('jwt_coder');
      $providerManager = $container->get('oauth_provider_manager');
      
      return new \Cerad\Module\OAuthModule\OAuthController($providerManager,$jwtCoder);
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