<?php

namespace Cerad\Module\OAuthModule;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\Response;
use Cerad\Component\HttpMessage\ResponseRedirect;

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
    $baseHref   = $request->getBaseHref();
    
    ob_start();
    include dirname(__FILE__) . '/OAuthControllerCallback.html.php';
    $contents = ob_get_clean();
    
    return new Response($contents);
  }
  // /oauth/tokens?provider=providerName
  public function tokensAction(Request $request, $providerName)
  { 
    $provider = $this->providerManager->createProviderFromName($providerName);
    
    $authorizationUrl = $provider->getAuthorizationUrl($request);
  //die($authorizationUrl);
    return new ResponseRedirect($authorizationUrl);
  }
}
