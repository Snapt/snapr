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
class InternalCommand extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('internal')
            ->setDescription('Internal commands used by snapr')
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Which module do you wish to run?'
            )            
        ;
    }
    

    
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $input->getArgument('module');
        
        switch ($module) {
            case "genkey":
                $key = $this->generateKeys();
                if ($key !== false) {
                    $output->writeln('<comment>Important!</comment> Your API key is <info>' . $key . '</info>');
                    $output->writeln('Whenever you request an object you must use this key.');
                    $output->writeln('You can edit config/api_keys.php to alter this.');
                }
                
                $id = $this->generateId();
            break;
        
        }        

    }
    
    
    private function generateKeys()
    {
        if (file_exists(__DIR__.'/../../../config/api_keys.php')) return false;
        
        $string = $this->getKey();
        $keys = "<?php\n\nreturn array(\n'" . $string . "'\n);";
        file_put_contents(__DIR__.'/../../../config/api_keys.php', $keys);
        
        return $string;
    }
    

    private function generateId()
    {
        if (file_exists(__DIR__.'/../../../config/server_id.php')) return false;
        
        $string = $this->v4_uuid();
        $id = "<?php\n\nreturn '" . $string . "';";
        file_put_contents(__DIR__.'/../../../config/server_id.php', $id);
        
        return $string;
    }    
    
    
    
    private function getKey($bit_length = 128) {
        $fp = @fopen('/dev/random','rb');
        if ($fp !== FALSE) {
            $key = sha1(substr(base64_encode(@fread($fp,($bit_length + 7) / 8)), 0, (($bit_length + 5) / 6)  - 2));
            @fclose($fp);
            return $key;
        }
        return null;
    }
    
    
    private function v4_uuid() 
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
 
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
 
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
 
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
 
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
 
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
 
    
}