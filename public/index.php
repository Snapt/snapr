<?php
/**
 * snapr
 *
 * @package  snapr
 * @author   Snapt <sales@snapt.net>
 */

 
/*
|--------------------------------------------------------------------------
| Include composer autoloader
|--------------------------------------------------------------------------
|
| This allows the app to load all the composer libraries that may 
| be included.
|
*/
require_once __DIR__.'/../vendor/autoload.php';



/*
|--------------------------------------------------------------------------
| Include snapr config
|--------------------------------------------------------------------------
|
| The config file which controls various settings.
|
*/
$config = require_once __DIR__.'/../config/snapr.php';




/*
|--------------------------------------------------------------------------
| Start up Silex
|--------------------------------------------------------------------------
|
| Silex is our REST API router, and processes/dispatches all the queries
|
*/
$app = new Silex\Application();



/*
|--------------------------------------------------------------------------
| Configure application debug
|--------------------------------------------------------------------------
|
| This should be set to false in production environments
|
*/
$app['debug'] = $config['debug'];



/*
|--------------------------------------------------------------------------
| Include any authentication methods
|--------------------------------------------------------------------------
|
| We might want to authenticate in order to prevent anonymous access.
|
*/
if ($config['auth']) require_once __DIR__.'/../bootstrap/auth.php';



/*
|--------------------------------------------------------------------------
| Provide all of our various routes
|--------------------------------------------------------------------------
|
| These are the various post authentication paths that will return results.
| We prefer to use mount points instead of routes right in this file.
|
*/
$app->get('/discovery', function() use ($app) {
    return $app->json(
        array( 
            "server_id" => require_once __DIR__.'/../config/server_id.php',
            "hostname" => trim(`hostname`)
        )
    );
});

if (!in_array('/tools', $config['disabled']))           $app->mount('/tools', new Snapr\Routers\ToolsController());   
if (!in_array('/haproxy/stats', $config['disabled']))   $app->mount('/haproxy/stats', new Snapr\Routers\HAProxyStatsController());   
if (!in_array('/haproxy/program', $config['disabled'])) $app->mount('/haproxy/program', new Snapr\Routers\HAProxyProgramController());   


/*
|--------------------------------------------------------------------------
| Run the app
|--------------------------------------------------------------------------
|
| Let Silex do it's thing
|
*/
$app->run();