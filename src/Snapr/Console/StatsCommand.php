<?php
namespace Snapr\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Snapr\Collectors\Socket;

/**
* The command controller for stats
* 
* @package snapr
*/
class StatsCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('stats')
            ->setDescription('Get a stats item from the API')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Which stats item do you want? [info|all]'
            )            
        ;
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stat = $input->getArgument('type');
        
        switch ($stat) {
            case "all":
                $data = $this->getStats();
                foreach ($data as $group => $servers) {              
                    foreach ($servers as $server) {
                        $output->writeln(implode(",", $server));
                    }
                }
            break;
            
            case "info": 
                $data = $this->getInfo();
                foreach ($data as $key => $line) {
                    $output->writeln('<comment>' . $key . '</comment>: ' . $line);
                }
            break;
        }        

    }
    
    
    /**
    * Get the process info.
    *
    * @return string
    */
    public function getInfo()
    {
        $socket = new Socket;
        
        return $socket->getInfo();
    }
    
    
    /**
    * Get all the stats.
    *
    * @return string
    */
    public function getStats()
    {
        $socket = new Socket;
        
        return $socket->getStats();
    }
    
    
        
    
    /**
    * Session information manager.
    *
    * @param Application $app       The silex app.
    * @param $id       A session id [optionally].
    *
    * @return string
    */
    public function getSession(Application $app, $id = '')
    {
        $socket = new Socket;
        $result = $socket->getSession($id);
        
        return $app->json($result);
    } 
    
}