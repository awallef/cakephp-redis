# cakephp-cache plugin for CakePHP
This plugin allows you cache and store data in redis engine

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

	composer require awallef/cakephp-redis

Load it in your config/boostrap.php

	Plugin::load('Awallef/Redis');

## Redis caching
This plugin provides a very little bit different redis engine based on cakephp's RedisEngine.
differences are:

- Engine config comes with a bool 'serialize' option ( default is true )
- Read and wirte fct use config 'serialize' option
- Keys are stored/read/deleted in order to uses : and :* redis skills!

Configure the engine in app.php like follow:

	'Cache' => [
	    ...
	    'redis' => [
	      'className' => 'Awallef/Redis.Redis',
	      'prefix' => 'www.your-site.com:',
	      'duration' => '+24 hours',
	      'serialize' => true
	    ],
	    ...
	]
