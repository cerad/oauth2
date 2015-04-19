<?php

namespace Cerad\Module\OAuthModule\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Cerad\Component\HttpMessage\Request as CeradRequest;

/* ================================================================
 * Twitter basically does not support user login via oauth2 - very sad
 * Nor does it provide email even with oauth1
 * 
 * It also requires maintaining some state on the server which means sessions
 */
class TwitterProvider extends AbstractProvider
{   
  protected $userInfoUrl      = 'https://api.twitter.com/1.1/account/verify_credentials.json?include_entities=false&skip_status=true';
  protected $accessTokenUrl   = 'https://api.twitter.com/oauth/access_token';
  protected $requestTokenUrl  = 'https://api.twitter.com/oauth/request_token';
  protected $authorizationUrl = 'https://api.twitter.com/oauth/authenticate';
    
  // Called by getAuthorizationUrl
  protected function getRequestToken(CeradRequest $ceradRequest)
  {
    session_name('oauth_session');
    session_start();
      
    $requestTokenClient = new Client();

    $oauth = new Oauth1([
      'consumer_key'    => $this->clientId,
      'consumer_secret' => $this->clientSecret,
      'callback'        => $this->getRedirectUri($ceradRequest),
    ]);
    
    $requestTokenClient->getEmitter()->attach($oauth);

    $requestTokenResponse = $requestTokenClient->post($this->requestTokenUrl,[
      'auth'   => 'oauth',
      'debug'  => false,
      'verify' => false,
    ]);
    // oauth_token => 7Q..., oauth_token_secret => f2..., oauth_callback_confirmed => true 
    $requestTokenResponseData = $this->getResponseData($requestTokenResponse);
        
    return $requestTokenResponseData;
  }
  public function getAuthorizationUrl(CeradRequest $ceradRequest)
  {
    $requestToken = $this->getRequestToken($ceradRequest);
                
    $_SESSION['oauth_token']        = $requestToken['oauth_token'];
    $_SESSION['oauth_token_secret'] = $requestToken['oauth_token_secret'];
      
    $authorizationClient = new Client();
    $authorizationRequest = $authorizationClient->createRequest('GET',$this->authorizationUrl,
    [
      'query' => ['oauth_token' => $requestToken['oauth_token']]
    ]);
    return $authorizationRequest->getUrl();
  }
  public function getAccessToken(CeradRequest $ceradRequest)
  {
    session_name('oauth_session');
    session_start();
    
    $requestToken       = $_SESSION['oauth_token'];
    $requestTokenSecret = $_SESSION['oauth_token_secret'];
    
    session_destroy();
    
    $accessTokenClient = new Client();
        
    $oauth = new Oauth1(
    [
      'consumer_key'    => $this->clientId,
      'consumer_secret' => $this->clientSecret,
      'token'           => $requestToken,
      'token_secret'    => $requestTokenSecret,
      'verifier'        => $ceradRequest->getQueryParams()['oauth_verifier'],
    ]);
    $accessTokenClient->getEmitter()->attach($oauth);

    $accessTokenResponse = $accessTokenClient->post($this->accessTokenUrl,
    [
      'auth'   => 'oauth',
      'debug'  => false,
      'verify' => false,
    ]);
    $accessToken = $this->getResponseData($accessTokenResponse);
        
    return $accessToken;
  }
    public function getUserInfo($accessToken)
    {
        $userInfoClient = new Client();
        
        $oauth = new Oauth1([
            'consumer_key'    => $this->clientId,
            'consumer_secret' => $this->clientSecret,
            'token'           => $accessToken['oauth_token'],
            'token_secret'    => $accessToken['oauth_token_secret'],
        ]);
        $userInfoClient->getEmitter()->attach($oauth);

        $userInfoResponse = $userInfoClient->get($this->userInfoUrl,[
            'auth'   => 'oauth',
            'debug'  => false,
            'verify' => false,
            // Does not seem to help
            'query'  => ['include_entities' => 'false', 'skip_status' => 'false'],
        ]);
        $data = $this->getResponseData($userInfoResponse);
        
        $userInfo = array(
            'identifier'     => $data['id'],
            'nickname'       => $data['screen_name'],
            'realname'       => $data['name'],
            'email'          => null, // Not possible with twitter
            'profilepicture' => null,
            'providername'   => $this->name,
        );
        return $userInfo;
    }
    /*
     * Array ( 
     * [{"id":49477179,
     *   "id_str":"49477179",
     *   "name":"Art_Hundiak",
     *   "screen_name":"ahundiak",
     *   "location":"","description":"","url":null,
     *   "entities":{"description":{"urls":] => 
     *     Array ( [0] => ) [Canada)","geo_enabled":false,"verified":false,"statuses_count":0,"lang":"en",
     *    "contributors_enabled":false,"is_translator":false,"is_translation_enabled":false,
     *    "profile_background_color":"C0DEED",
     *    "profile_background_image_url":"http:\/\/abs_twimg_com\/images\/themes\/theme1\/bg_png",
     *    "profile_background_image_url_https":"https:\/\/abs_twimg_com\/images\/themes\/theme1\/bg_png",
     *    "profile_background_tile":false,
     *    "profile_image_url":"http:\/\/abs_twimg_com\/sticky\/default_profile_images\/default_profile_3_normal_png",
     *    "profile_image_url_https":"https:\/\/abs_twimg_com\/sticky\/default_profile_images\/default_profile_3_normal_png",
     *    "profile_link_color":"0084B4","profile_sidebar_border_color":"C0DEED",
     *    "profile_sidebar_fill_color":"DDEEF6",
     *    "profile_text_color":"333333","profile_use_background_image":true,
     *    "default_profile":true,
     *    "default_profile_image":true,"following":false,
     *    "follow_request_sent":false,
     *    "notifications":false}] => )
     */
}