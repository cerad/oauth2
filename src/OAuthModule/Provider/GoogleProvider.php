<?php

namespace Cerad\Module\OAuthModule\Provider;

class GoogleProvider extends AbstractProvider
{   
  protected $scope = 'openid profile email';
    
  protected $userInfoUrl      = 'https://www.googleapis.com/oauth2/v2/userinfo';
  protected $accessTokenUrl   = 'https://accounts.google.com/o/oauth2/token';
  protected $authorizationUrl = 'https://accounts.google.com/o/oauth2/auth';
    
  /* ===========================================================
   * http://openid.net/specs/openid-connect-core-1_0.html#Claims
   */
  public function getUserInfo($accessToken)
  {
    $data = $this->getUserInfoData($accessToken);
        
    $sub = isset($data['id'  ]) ? $data['id']   : 'Missing Subject ID'; // Toss exception?
    
    $nameFull   = isset($data['name'       ]) ? $data['name'       ] : 'Missing Name';
    $nameGiven  = isset($data['given_name' ]) ? $data['given_name' ] : null;
    $nameFamily = isset($data['family_name']) ? $data['family_name'] : null;
    
    $email         = isset($data['email' ]) ? $data['email']  : null;
    $emailVerified = isset($data['verified_email' ]) ? $data['verified_email']  : false;
    
    $emailParts = explode('@',$email);
    $nameUser = count($emailParts) ? $emailParts[0] : null;
        
    $userInfo = 
    [
      'sub'      => $sub,
      'iss'      => 'oauth.zayso.org',
      'provider' => $this->name,
      
      'name'        => $nameFull,
      'given_name'  => $nameGiven,
      'family_name' => $nameFamily,
      
      'email'         => $email,
      'emailVerified' => $emailVerified,
      
      'perferred_username' => $nameUser,
       
      'iat' => time(),
      'exp' => time() + 3600, // Should be shorter?
    ];
    return $userInfo;
  }
  /* 
   * [id] => averylongnumber 
   * [email] => ahundiak@example.org 
   * [verified_email] => 1 
   * [name]        => Arthur Hundiak 
   * [given_name]  => Arthur
   * [family_name] => Hundiak
   * [link]    => https://plus.google.com/averylongnumber
   * [picture] => https://lh3.googleusercontent.com/-KDDHRXRz07U/AAAAAAAAAAI/AAAAAAAAAAo/_e8-j-zb2os/photo.jpg 
   * [gender]  => male 
   * [locale]  => en 
   * [hd]      => zayso.org 
   */
}