<?php

namespace Cerad\Module\OAuthModule;

use Cerad\Component\HttpMessage\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as ResponseRedirect;

class OAuthController
{
  private $jwtCoder;
  private $providerManager;
  
  public function __construct($providerManager,$jwtCoder)
  {
    $this->jwtCoder        = $jwtCoder;
    $this->providerManager = $providerManager;
  }
  public function callbackAction(Request $request)
  {
    $provider = $this->providerManager->createProviderFromRequest($request);

    $accessTokenData = $provider->getAccessToken($request);
        
    $userInfo = $provider->getUserInfo($accessTokenData);
    
    $oauthToken = $this->jwtCoder->encode($userInfo);
    
    $html = include dirname(__FILE__) . '/oauth-callback.html.php';
    
    return new Response($html);
  }
  // /oauth/tokens?provider=providerName
  public function tokensAction(Request $request, $providerName)
  { 
    $provider = $this->providerManager->createProviderFromName($providerName);
    
    $authorizationUrl = $provider->getAuthorizationUrl($request);
    
    return new ResponseRedirect($authorizationUrl);
  }
}
