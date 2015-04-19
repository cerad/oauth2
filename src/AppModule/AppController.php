<?php

namespace Cerad\Module\AppModule;

use Cerad\Component\HttpMessage\Request;
use Cerad\Component\HttpMessage\Response;

class AppController
{
  public function indexAction(Request $request)
  {
    ob_start();
    
    include \dirname(__FILE__) . '/AppControllerIndex.html.php';
    
    $contents = ob_get_clean();
    
    $response = new Response($contents);
    
    return $response;
  }
}