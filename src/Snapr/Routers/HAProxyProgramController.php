<?php
namespace Snapr\Routers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Snapr\Collectors\Socket;

/**
* The routes controller for stats
* 
* @package snapr
*/
class HAProxyProgramController implements ControllerProviderInterface
{
    private $stats = array();
    
    
    /**
    * Connect function is used by Silex to mount the controller to the application.
    *
    * @param Application $app Silex Application Object.
    *
    * @return Response Silex Response Object.
    */
    public function connect(Application $app)
    {
        /**
        * @var \Silex\ControllerCollection $factory
        */        
        $factory = $app['controllers_factory'];
        
        $factory->get(
            '/clear/{what}',
            'Snapr\Routers\HAProxyProgramController::clearCounters'
        )->value('what', 'max');
        
        $factory->get(
            '/server/{action}/{backend}/{server}',
            'Snapr\Routers\HAProxyProgramController::serverControl'
        )->value('what', 'max');        
     
        
        
        return $factory;
    }

    
    
    /**
    * Clear the haproxy counters.
    *
    * @param Application $app       The silex app.
    * @param $what       What to clear, max or all.
    *
    * @return string
    */
    public function clearCounters(Application $app, $what = 'max')
    {
        $socket = new Socket;
        if ($what == 'all') $result = $socket->rawMessage("clear counters all");
        else echo $result = $socket->rawMessage("clear counters");
        
        return $app->json($result);
    }
    
    
    
    /**
    * Send server enable and disable messages.
    *
    * @param Application $app       The silex app.
    * @param $action                disable or enable
    * @param $backend                backend
    * @param $server                server
    *
    * @return string
    */
    public function serverControl(Application $app, $action, $backend, $server)
    {
        $socket = new Socket;
        $result = $socket->rawMessage($action . " server " . $backend . "/" . $server);
        
        return $app->json($result);
    }   
    
}