<?php

namespace Cerad\Module\OAuthModule\Provider;

class LiveconnectProvider extends AbstractProvider
{   
  protected $scope = 'wl.basic wl.signin wl.emails';
    
  protected $userInfoUrl      = 'https://apis.live.net/v5.0/me';
  protected $accessTokenUrl   = 'https://login.live.com/oauth20_token.srf';
  protected $authorizationUrl = 'https://login.live.com/oauth20_authorize.srf';
    
  public function getUserInfo($accessToken)
  {
    $data = $this->getUserInfoData($accessToken);
    
    $email = $data['emails']['preferred'];
    
    $nameParts = explode('@',$email);
    
    $nickname = count($nameParts) ? $nameParts[0] : null;
        
    $userInfo = 
    [
      'identifier'     => $data['id'],
      'nickname'       => $nickname,
      'realname'       => $data['name'],
      'email'          => $email,
      'profilepicture' => null,
      'providername'   => $this->name,
    ];
    return $userInfo;
  }
  /*
   * [id] => 2aeb79bd69896b4a 
   * [name] => Arthur Hundiak 
   * [first_name] => Arthur 
   * [last_name] => Hundiak 
   * [link] => https://profile.live.com/ 
   * [gender] => 
   * [emails] => 
   *   [preferred] => ahundiak@gmail.com 
   *   [account]   => ahundiak@gmail.com 
   *   [personal]  => 
   *   [business]  =>
   * [locale] => en_US 
   * [updated_time] => 2015-04-18T23:49:27+0000 
   */
}