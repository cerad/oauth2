<?php

use Cerad\Component\HttpMessage\Request;

use Cerad\Module\AppModule\App;

call_user_func(function()
{
  require '../vendor/autoload.php';

  $api = new App('prod',false);

  $request = new Request($_SERVER);
  $response = $api->handle($request);
  $response->send();
});