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
class HAProxyStatsController implements ControllerProviderInterface
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
            '/info',
            'Snapr\Routers\HAProxyStatsController::getInfo'
        );
        
        $factory->get(
            '/',
            'Snapr\Routers\HAProxyStatsController::getStats'
        );
        
        $factory->get(
            '/group/{name}',
            'Snapr\Routers\HAProxyStatsController::getGroup'
        );
        
        $factory->get(
            '/group/{name}/{server}',
            'Snapr\Routers\HAProxyStatsController::getGroupServer'
        );         
                
        $factory->get(
            '/session/{id}',
            'Snapr\Routers\HAProxyStatsController::getSession'
        )->value('id', '');          
        
        return $factory;
    }

    
    /**
    * Get the process info.
    *
    * @param Application $app       The silex app.
    *
    * @return string
    */
    public function getInfo(Application $app)
    {
        $socket = new Socket;
        
        return $app->json($socket->getInfo());
    }
    
    
    
    /**
    * Get all the stats.
    *
    * @param Application $app       The silex app.
    *
    * @return string
    */
    public function getStats(Application $app)
    {
        $socket = new Socket;
        
        return $app->json($socket->getStats());
    }      
    
    
    
    /**
    * Get a "groups" stats, group being a named listen, frontend or backend
    *
    * @param Application $app       The silex app.
    * @param $name       The group name
    *
    * @return string
    */
    public function getGroup(Application $app, $name)
    {
        $socket = new Socket;
        $stats = $socket->getStats();
        
        return $app->json($stats[$name]);
    }    

    
    /**
    * Get a single server from a "groups" stats, group being a named listen, frontend or backend
    *
    * @param Application $app       The silex app.
    * @param $name       The group name
    *
    * @return string
    */
    public function getGroupServer(Application $app, $name, $server)
    {
        $socket = new Socket;
        $stats = $socket->getStats();
        
        return $app->json($stats[$name][$server]);
    }    
    
    
        
    /**
    * Get a session or all the sessions information
    * Warning! This can return a huge amount of data
    *
    * @param Application $app       The silex app.
    * @param Application $app       The silex app.
    *
    * @return string
    */
    public function getSession(Application $app, $id = '')
    {
        $socket = new Socket;
        
        return $app->json($socket->getSession($id));
    }
    
}