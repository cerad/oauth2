<?php
namespace Cerad\Module\OAuthModule;

use Cerad\Component\HttpMessage\Request;

class ProviderManager
{   
  protected $providers;
  protected $redirectUri;
  protected $jwtCoder;
    
  public function __construct($jwtCoder,$redirectUri,$providers)
  {
    $this->jwtCoder    = $jwtCoder;
    $this->redirectUri = $redirectUri;
        
    foreach($providers as $provider) 
    {
      $provider['instance'] = null;
      $this->providers[$provider['name']] = $provider;
    }
  }
  public function getProviders() { return $this->providers; }

  public function createProviderFromName($nameArg,$state = null)
  {
    $name = strtolower($nameArg);
    
    if (!isset($this->providers[$name])) 
    {
      throw new \InvalidArgumentException(sprintf("Cerad Oauth Provider not found: %s",$name));
    }
    if (isset( $this->providers[$name]['instance'])) 
    { 
      return $this->providers[$name]['instance']; 
    }
    // The signed state token
    if (!$state) 
    {
      $state = $this->jwtCoder->encode(['name' => $name,'random' => uniqid()]);
    }
    // Create it
    $params = $this->providers[$name];
    
    $className = $params['class'];
        
    $instance = new $className($params,$this->redirectUri,$state);
    
    $this->providers[$name]['instance'] = $instance;
    
    return $instance;
  }
  // Process a redirection from the provider site
  public function createProviderFromRequest(Request $request)
  {
    // OAuth1 will not have state, how to handle twitter?
    $queryParams = $request->getQueryParams();
    if (isset($queryParams['state']))
    {
      $state = $queryParams['state'];
      $info = $this->jwtCoder->decode($state);
      $providerName = $info['name'];
    }
    else 
    {
      $state = 'twitter_fake_state';
      $providerName = 'twitter';
    }   
    return $this->createProviderFromName($providerName,$state);
  }
}