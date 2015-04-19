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
      [
        'name'          => 'facebook',
        'class'         => 'Cerad\Module\OAuthModule\Provider\FacebookProvider',
        'client_id'     => $container->get('oauth_facebook_client_id'),
        'client_secret' => $container->get('oauth_facebook_client_secret'),
      ],            
      [
        'name'          => 'linkedin',
        'class'         => 'Cerad\Module\OAuthModule\Provider\LinkedinProvider',
        'client_id'     => $container->get('oauth_linkedin_client_id'),
        'client_secret' => $container->get('oauth_linkedin_client_secret'),
      ],            
      [
        'name'          => 'twitter',
        'class'         => 'Cerad\Module\OAuthModule\Provider\TwitterProvider',
        'client_id'     => $container->get('oauth_twitter_client_id'),
        'client_secret' => $container->get('oauth_twitter_client_secret'),
      ],        
      [
        'name'          => 'liveconnect',
        'class'         => 'Cerad\Module\OAuthModule\Provider\LiveconnectProvider',
        'client_id'     => $container->get('oauth_liveconnect_client_id'),
        'client_secret' => $container->get('oauth_liveconnect_client_secret'),
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
    /* ======================================================
     * /oauth/tokens
     */
    $tokensRouteAction = function($request) use ($container)
    {
      $controller = $container->get('oauth_controller');
      return $controller->tokensAction($request,$request->getAttribute('providerName'));
    };
    $tokensRouteMatch = function($path) use($tokensRouteAction)
    {  
      $params = 
      [ 
        'providerName' => null,
        '_action' =>  $tokensRouteAction
      ];
      
      $matches = [];
        
      if (!preg_match('#^/oauth/tokens/(\w+$)#', $path, $matches)) return false;

      $params['providerName'] = $matches[1];
      
      return $params;
    };
    $tokensRouteService = function() use ($tokensRouteMatch)
    {
      return $tokensRouteMatch;
    };
    $container->set('oauth_route_tokens',$tokensRouteService,'routes');
    
    /* ======================================================
     * /oauth/callback
     */
    $callbackRouteAction = function($request) use ($container)
    {
      $controller = $container->get('oauth_controller');
      return $controller->callbackAction($request);
    };
    $callbackRouteMatch = function($path) use($callbackRouteAction)
    {  
      $params = 
      [ 
        '_action' =>  $callbackRouteAction
      ];
      if ($path === '/oauth/callback') return $params;
      
      return false;
    };
    $callbackRouteService = function() use ($callbackRouteMatch)
    {
      return $callbackRouteMatch;
    };
    $container->set('oauth_route_callback',$callbackRouteService,'routes');
  }
}