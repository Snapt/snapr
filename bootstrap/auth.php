<?php
       
/*
|--------------------------------------------------------------------------
| Basic API Key Authentication
|--------------------------------------------------------------------------
|
| This validates the either POST or GET apikey is set and that its in the 
| config/api_keys.php array. This might become a more advanced Silex auth 
| system but it appears to be overkill. HTTP basic auth was done away with 
| because command line scripts should easily access this.
|
*/
                              
$app->before(function(Symfony\Component\HttpFoundation\Request $request) use ($app)
{    
    if ($request->getRequestUri() != '/discovery') 
    {        
        $keys = require_once __DIR__.'/../config/api_keys.php';
        
        if (isset($_GET['apikey'])) $apikey = $_GET['apikey'];
        else if (isset($_POST['apikey'])) $apikey = $_POST['apikey'];
        else throw new \RuntimeException('You must send an apikey argument');
        
        if (!in_array($apikey, $keys)) throw new \RuntimeException('You could not be authenticated.');
    
    }
});