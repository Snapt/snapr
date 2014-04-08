<?php
namespace Snapr\Collectors;
                      
use Snapr\Tools\HAProxy;

/**
* The socket controller for haproxy socket commands
* 
* @package snapr
*/
class Socket
{
    // haproxy socket path
    public $path;
    
    
    
    public function __construct() {
        // Fill our path variables
        $this->path = HAProxy::detectSocketPath();
    }
    
    
    
    /**
    * Return the show info process info from haproxy
    *
    * @return string
    */      
    public function getInfo() {
        return HAProxy::format(
            $this->socketCommand("show info")
        );
    }
    
    
    
    /**
    * Return all the stats from show stat
    *
    * @return string
    */      
    public function getStats() {
        return HAProxy::format(
            $this->socketCommand("show stat"),
            "stat"
        );
    } 
    
    
    /**
    * Return all the stats from show stat
    *
    * @return string
    */      
    public function getSession($id = '') {
        if ($id == '') $format = 'sessions';
        else $format = 'session';
        
        return HAProxy::format(
            $this->socketCommand("show sess " . $id),
            $format
        );
    } 
        
    
    
    /**
    * Send a raw message to the socket
    * 
    * @return string 
    */
    public function rawMessage($msg) {
        return $this->socketCommand($msg);
    }
    
    
    
    
    /**
    * Send a command to an haproxy socket.
    *
    * @param string $command 
    *
    * @return string
    */
    private function socketCommand($msg)
    {
        $msg = $msg . "\n";
        
        // Create the socket
        $socket = @socket_create(AF_UNIX, SOCK_STREAM, 0);
        $sock = @socket_connect($socket, $this->path);

        // Measure the length
        $len = strlen($msg);

        // Send the msg above
        // socket_sendto works on bsd, not linux
        @socket_send($socket, $msg, $len, 0);

        
        // Accept the response
        if (@socket_recvfrom($socket, $response, 1024000, MSG_WAITALL, $sock)) 
        {
            @socket_shutdown($socket, 0);
            @socket_close($socket);    
            
            return $response;
        } else {
            throw new \RuntimeException("Could not connect to socket.");
        }
        
        return false;
    }    
    

}