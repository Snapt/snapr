<?php
namespace Snapr\Tools;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Silex\Application;
use Silex\ControllerProviderInterface;

/**
* The tools controller for various haproxy functions
* 
* @package snapr
*/
class HAProxy
{

    /**
    * Autodetect the path of the haproxy config and the socket
    * This is arguably not the best way of doing it but we want 
    * this to be extremely easy and flexible to deploy.
    *
    * @param string $command 
    *
    * @return string
    */
    public function detectSocketPath() 
    {      
        if (file_exists('/etc/haproxy/haproxy.cfg')) $config = '/etc/haproxy/haproxy.cfg';
        else if (file_exists('/etc/haproxy/haproxy.conf')) $config = '/etc/haproxy/haproxy.conf';
        else if (file_exists('/usr/local/etc/haproxy.cfg')) $config = '/usr/local/etc/haproxy.cfg';
        else if (file_exists('/usr/local/etc/haproxy.conf')) $config = '/usr/local/etc/haproxy.conf';
        else throw new \RuntimeException("Could not find the haproxy config path.");
        
        // load the haproxy config
        $contents = file_get_contents($config);
        
        // escape special characters in the query
        $pattern = preg_quote("stats socket", '/');
        
        // finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
        
        // search, and store all matching occurences in $matches
        if(preg_match($pattern, $contents, $matches)){
            $match = preg_split("/[\s,]+/", $matches[0]);
            
            foreach ($match as $i => $m) {                
                if ($m == 'socket') {                    
                    $path = $match[$i+1];
                    return $path;
                }
            }
        }
        
        if ($this->path != '') return array(
            "config" => $this->config,
            "path" => $this->path
        );
        
        throw new \RuntimeException("Could not find the haproxy socket path. Please make sure you have at least this line in your config global section: stats socket /tmp/haproxy.sock mode 700.");
    }
    
    
        
    /**
    * Format the various responses haproxy gives into a nice 
    * PHP array that can be output in whatever way later
    *
    * @param string $data   The raw response from haproxy
    * @param string $type   The type of request being formatted
    *
    * @return string
    */    
    public function format($data, $type = "default") {
        $data = split("\n", $data);   
        $responseArray = array();
        
        switch ($type) {
            // A stat request needs to be smarter
            case "stat":
                $names = explode(',', 'pxname,svname,qcur,qmax,scur,smax,slim,stot,bin,bout,dreq,dresp,ereq,econ,eresp,wretr,wredis,status,weight,act,bck,chkfail,chkdown,lastchg,downtime,qlimit,pid,iid,sid,throttle,lbtot,tracked,type');
                unset($data[0]);

                foreach ($data as $line) {

                    $line = explode(',', $line);
                    for ($i = 0; $i < count($names); $i++) {
                        // Put into an ordered array, e.g. [group-name][servername]=>details
                        if (isset($line[1])) $responseArray[$line[0]][$line[1]][$names[$i]] = $line[$i];
                    }


                }
            break;
            
            // Sessions [all]
            case "sessions":                
                foreach ($data as $line) {
                    
                    $line = split(': ', $line);
                    
                    if (isset($line[1])) {                 
                        $return = array();
                               
                        $line[1] = explode(' ', $line[1]);
                        
                        foreach ($line[1] as $item) {
                            list($lhs, $rhs) = explode('=', $item);
                            $return[$lhs] = $rhs;
                        }
                        
                        $responseArray[$line[0]] = $return;
                    }
                }
            break;          
            
            case "session":
                return $data;  
            break;
            
            
            // By default we just split it into LHS | RHS
            default:
                foreach ($data as $line) {
                    $line = split(': ', $line);
                    if (isset($line[1])) $responseArray[$line[0]] = $line[1];
                }
            break;
        }
        
        return $responseArray;        
    }
    
}