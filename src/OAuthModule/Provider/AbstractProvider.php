<?php

namespace Cerad\Module\OAuthModule\Provider;

use GuzzleHttp\Client;

use Cerad\Component\HttpMessage\Request as CeradRequest;

class AbstractProvider
{   
    protected $name;
    protected $state;
    protected $redirectUri;
    
    protected $client;
    protected $clientId;
    protected $clientSecret;
    
    protected $scope;
    protected $userInfoUrl;
    protected $accessTokenUrl;
    protected $revokeTokenUrl;
    protected $authorizationUrl;
    
  public function __construct($params,$redirectUri,$state)
  { 
    $this->name         = $params['name'];
    $this->clientId     = $params['clientId'];
    $this->clientSecret = $params['clientSecret'];
    
    if (isset($params['scope'           ])) $this->scope            = $params['scope'];
    if (isset($params['userInfoUrl'     ])) $this->userInfoUrl      = $params['userInfoUrl'];
    if (isset($params['accessTokenUrl'  ])) $this->accessTokenUrl   = $params['accessTokenUrl'];
    if (isset($params['revertTokenUrl'  ])) $this->revertTokenUrl   = $params['revertTokenUrl'];
    if (isset($params['authorizationUrl'])) $this->authorizationUrl = $params['authorizationUrl'];
    
    $this->state       = $state;
    $this->redirectUri = $redirectUri;
        
    $this->client = new Client();
    $this->client->setDefaultOption('verify', false);
  }
  public function getName() { return $this->name; }
    
  public function getAuthorizationUrl(CeradRequest $ceradRequest)
  {   
    $query = 
    [
      'response_type' => 'code',
      'client_id'     => $this->clientId,
      'scope'         => $this->scope,
      'redirect_uri'  => $this->getRedirectUri($ceradRequest),
      'state'         => $this->state,
    ];
    $guzzleRequest = $this->client->createRequest('GET',$this->authorizationUrl,
    [
      'query' => $query,
    ]);
    return $guzzleRequest->getUrl();
  }
  public function getAccessToken(CeradRequest $ceradRequest)
  {
    $query = 
    [
      'grant_type'    => 'authorization_code',
      'code'          => $ceradRequest->getQueryParams()['code'],
      'client_id'     => $this->clientId,
      'client_secret' => $this->clientSecret,
      'redirect_uri'  => $this->getRedirectUri($ceradRequest),
    ];
    $guzzleResponse = $this->client->post($this->accessTokenUrl,
    [
      'headers' => ['Accept' => 'application/json'],
      'body'    => $query,
    ]);
    $responseData = $this->getResponseData($guzzleResponse);

    return $responseData;
  }
  public function getUserInfoData($accessToken)
  {
    $guzzleResponse = $this->client->get($this->userInfoUrl,
    [
      'headers' => 
      [
        'Accept' => 'application/json',
        'Authorization'  => 'Bearer ' . $accessToken['access_token'],
      ],
    ]);
    return $this->getResponseData($guzzleResponse);
  }    
  // Return array from either json or name-value
  protected function getResponseData($guzzleResponse)
  {
    $content = (string)$guzzleResponse->getBody();
        
    if (!$content) return [];
    
    $json = json_decode($content, true);
    if (JSON_ERROR_NONE === json_last_error()) return $json;
        
    $data = [];
    parse_str($content, $data);
    return $data;
  }
  protected function getRedirectUri(CeradRequest $request)
  {
    // http://local.oauth.zayso.org/oauth/callback
    return $request->getBaseHrefAbs() . $this->redirectUri;
  }
}
