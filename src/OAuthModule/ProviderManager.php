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

  public function createProviderFromName($name,$state = null)
  {
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
    $info = $this->providers[$name];
    $className = $info['class'];
        
    $instance = new $className(
      $info['name'],
      $info['client_id'],
      $info['client_secret'],
      $state,
      $this->redirectUri
    );
    $this->providers[$name]['instance'] = $instance;
    
    return $instance;
  }
  // Process a redirection from the provider site
  public function createProviderFromRequest(Request $request)
  {
    // OAuth1 will not have state, how to handle twitter?
    $state = $request->getQueryParams()['state'];
        
    // Toss exception if tampered with
    $info = $this->jwtCoder->decode($state);
        
    return $this->createProviderFromName($info['name'],$state);
  }
}