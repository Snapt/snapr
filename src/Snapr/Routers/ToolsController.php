<?php
namespace Snapr\Routers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\ControllerProviderInterface;

/**
* The routes controller for various tools
* 
* @package snapr
*/
class ToolsController implements ControllerProviderInterface
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
            '/hostname',
            'Snapr\Routers\ToolsController::hostname'
        );
        
        return $factory;
    }

    
    /**
    * Return the hostname of the system.
    *
    * @param Application $app       The silex app.
    *
    * @return string
    */
    public function hostname(Application $app)
    {
        return $app->json(trim(`hostname`));
    }
    
    
}