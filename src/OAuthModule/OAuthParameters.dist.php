<?php

namespace Cerad\Module\OAuthModule;

class OAuthParameters
{
  public function __construct($container)
  {    
    $container->set('oauth_callback_uri','oauth/callback');
    
    $oauthGoogleConfig =
    [ 
      'name'         => 'google',
      'class'        => 'Cerad\Module\OAuthModule\Provider\GoogleProvider',
      'clientId'     => '',
      'clientSecret' => '',
        
      'scope'            => 'openid profile email',
      'userInfoUrl'      => 'https://www.googleapis.com/oauth2/v2/userinfo',
      'accessTokenUrl'   => 'https://accounts.google.com/o/oauth2/token',
      'authorizationUrl' => 'https://accounts.google.com/o/oauth2/auth',
      'revokeTokenUrl'   => null,
    ];
    $oauthGithubConfig =
    [ 
      'name'         => 'github',
      'class'        => 'Cerad\Module\OAuthModule\Provider\GithubProvider',
      'clientId'     => '',
      'clientSecret' => '',
    ];
    $oauthFacebookConfig =
    [ 
      'name'         => 'facebook',
      'class'        => 'Cerad\Module\OAuthModule\Provider\FacebookProvider',
      'clientId'     => '',
      'clientSecret' => '',
    ];
    $oauthLinkedInConfig =
    [ 
      'name'         => 'linkedin',
      'class'        => 'Cerad\Module\OAuthModule\Provider\LinkedInProvider',
      'clientId'     => '',
      'clientSecret' => '',
    ];
    $oauthLiveConnectConfig =
    [ 
      'name'         => 'liveconnect',
      'class'        => 'Cerad\Module\OAuthModule\Provider\LiveConnectProvider',
      'clientId'     => '',
      'clientSecret' => '',
    ];
    $oauthTwitterConfig =
    [ 
      'name'         => 'twitter',
      'class'        => 'Cerad\Module\OAuthModule\Provider\TwitterProvider',
      'clientId'     => '',
      'clientSecret' => '',
    ];
    $container->set('oauth_providers_config',
    [
    //$oauthGoogleConfig,
    //$oauthGithubConfig,
    //$oauthFacebookConfig,
    //$oauthLinkedInConfig,
    //$oauthLiveConnectConfig,
    //$oauthTwitterConfig,
    ]);
  }
}