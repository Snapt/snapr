Using Snapr
=============

Installation
------------

Snapr is available on Packagist ([Snapt/snapr](http://packagist.org/packages/Snapt/snapr))
and as such installable via [Composer](http://getcomposer.org/).

```bash
composer create-project "snapt/snapr=1.0.*@dev" 
```

Once installed two files will be created in config/ by composer, api_keys.php and server_id.php. 
You can edit api_keys.php to change the key or replace it with a common one used across your 
installs. This is the key used to authenticate.

You can edit these settings and more in config/snapr.php

Running Snapr
-------------

Snapr uses a REST interface with basic HTTP authentication. The first thing you need to do 
is configure your "user list" in config/users.php

Once that is configured you can either run snapr in your own webserver using the public/ 
directory as a base, or run it using php's built in webserver and the handy server.php file 
for testing.

```bash
php -S 0.0.0.0:8081 ./server.php
```

You really should use a mini web server or something like that. However, you can run the 
php server in the backgroud like so

```bash
nohup php -S 0.0.0.0:85 ./server.php > /dev/null &
```

Querying Information
--------------------

You can now navigate to /stats/info on your host and port and append your api key to 
the request, so tools/hostname becomes /tools/hostname?apikey=xxxxxxx. This can be 
disabled in config/snapr.php

If successful you will see a json encoded array of the stats.

Available Calls
---------------

GET /discovery
- No authetication identification URL.

GET /tools/hostname
- Request the plain text hostname of the server.


**Stats**

GET /haproxy/stats/info
- Request the haproxy process information.

GET /haproxy/stats
- Request a full array of all the statistics for all the servers.

GET /haproxy/stats/group/NAME
- Request a full array all the statistics for the frontend, backend or listen group NAME.

GET /haproxy/stats/group/NAME/SERVER
- Request a full array all the statistics for the server SERVER in the frontend, backend or listen group NAME.

GET /haproxy/stats/session
- List all the current sessions. BEWARE this can return an enormous amount of data.

GET /haproxy/stats/session/ID
- Detailed information on a single session ID. ID is obtained from the session list.


**Program Control**

GET /haproxy/program/clear
 - Clear max counters
 
GET /haproxy/program/clear/all
 - Clear all counters

GET /haproxy/program/server/enable/BACKEND/SERVER
- Enable the SERVER in BACKEND

GET /haproxy/program/server/disable/BACKEND/SERVER
- Disable the SERVER in BACKEND
 
CLI Tool
--------

We provide a cli tool for querying the information as well.

./cli stats info
- Request the haproxy process information.

./cli stats all
- Request a full array of all the statistics for all the servers.