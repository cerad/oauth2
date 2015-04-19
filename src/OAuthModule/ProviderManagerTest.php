<?php

namespace Cerad\Module\OAuthModule;

use Cerad\Module\AppModule\App;

use Cerad\Module\OAuthModule\ProviderManager;
use Cerad\Module\OAuthModule\OAuthController;

use Cerad\Component\Jwt\JwtCoder;

use Cerad\Component\HttpMessage\Request;

class ProviderManagerTest extends  \PHPUnit_Framework_TestCase
{  
  protected $secret      = 'secret';
  protected $jwtCoder    = null;
  protected $redirectUri = 'oauth/callback';
  
  protected $providersConfig = 
  [
    [
      'name'          => 'google',
      'class'         => 'Cerad\Module\OAuthModule\Provider\GoogleProvider',
      'clientId'     => '%google_client_id%',
      'clientSecret' => '%google_client_secret%',
    ],            
    [
      'name'          => 'github',
      'class'         => 'Cerad\Module\OAuthModule\Provider\GithubProvider',
      'clientId'     => '%github_client_id%',
      'clientSecret' => '%github_client_secret%',
    ],          
  ];
  public function setUp()
  {
    $this->jwtCoder = new JwtCoder($this->secret);
  }
  public function testProvidersConstruct()
  {
    $providerManager = new ProviderManager($this->jwtCoder,$this->redirectUri,$this->providersConfig);
    
    $providers = $providerManager->getProviders();
    
    $this->assertEquals(2,count($providers));
  }
  public function testCreateFromName()
  {
    $providerManager = new ProviderManager($this->jwtCoder,$this->redirectUri,$this->providersConfig);
    
    $provider = $providerManager->createProviderFromName('google');
    
    $this->assertEquals('google',$provider->getName());
  }
  public function testCreateFromRequest()
  {
    $state = $this->jwtCoder->encode(['name' => 'google']);
    
    $request = new Request('GET /something?state=' . $state);
    
    $providerManager = new ProviderManager($this->jwtCoder,$this->redirectUri,$this->providersConfig);
    
    $provider = $providerManager->createProviderFromRequest($request);
    
    $this->assertEquals('google',$provider->getName());
  }
  public function testAuthorizationUrlGoogle()
  {
    $schemeAuthority = 'http://oauth.zayso.local';
      
    $providerManager = new ProviderManager($this->jwtCoder,$this->redirectUri,$this->providersConfig);
    
    $provider = $providerManager->createProviderFromName('google');
    
    $request = new Request('GET ' . $schemeAuthority . '/oauth/tokens/google');
    
    $url = $provider->getAuthorizationUrl($request);
    
    $urlParts = explode('?',$url);
    $params = [];
    parse_str($urlParts[1],$params);
    
    $this->assertEquals('code',                $params['response_type']);
    $this->assertEquals('openid profile email',$params['scope']);
    $this->assertTrue  (isset($params['client_id']));
    
    $this->assertEquals($request->getBaseHrefAbs() . $this->redirectUri, $params['redirect_uri']);
    
    $statePayload = $this->jwtCoder->decode($params['state']);

    $this->assertEquals('google',$statePayload['name']);
    
    // print_r($params);
  }
  public function testControllerTokensAction()
  {
    $providerManager = new ProviderManager($this->jwtCoder,$this->redirectUri,$this->providersConfig);

    $controller = new OAuthController($providerManager,$this->jwtCoder);

    $schemeAuthority = 'http://oauth.zayso.local';

    $request = new Request('GET ' . $schemeAuthority . '/oauth/tokens/google');  
    
    $response = $controller->tokensAction($request,'google');
    
    $location = $response->getHeaderLine('Location');
    
    $this->assertTrue(strpos($location,'https://accounts.google.com') !== false);
    
  //print_r($location);
  }
  public function testApp()
  {
    $app = new App();
    $app->boot();
    $container = $app->getContainer();
    
    $controller = $container->get('oauth_controller');
    
    $schemeAuthority = 'http://oauth.zayso.local';

    $request = new Request('GET ' . $schemeAuthority . '/oauth/tokens/google');  
    
    $response = $controller->tokensAction($request,'google');
    
    $location = $response->getHeaderLine('Location');
    
    $this->assertTrue(strpos($location,'https://accounts.google.com') !== false);
    
  }
}