<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Use API Key Authentication
    |--------------------------------------------------------------------------
    |
    | This enables/disables the need to pass ?apikey=xxxx on every request. We 
    | obviously advise this remains enabled. Edit them in config/api_keys.php
    |
    */

    'auth' => true,
    
    /*
    |--------------------------------------------------------------------------
    | Enable Debug Mode
    |--------------------------------------------------------------------------
    |
    | Debug mode lets you see errors instead of simply a blank error message. 
    |
    */

    'debug' => true,
    
    
    /*
    |--------------------------------------------------------------------------
    | Disabled Modules
    |--------------------------------------------------------------------------
    |
    | You can optionally remove certain modules from running here. Enter '/haproxy/stats' 
    | to disable /haproxy/stats calls, etc.
    |
    */

    'disabled' => array
    (
        
    ),
    
    
);