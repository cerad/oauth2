<?php

namespace Cerad\Module\AppModule;

use Cerad\Component\HttpMessage\Response;

class AppController
{
  public function indexAction($request)
  {
    $response = new Response('OAuth2 Index');
    return $response;
  }
}