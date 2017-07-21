<?php
namespace Awallef\Redis\Auth\Storage;

use Awallef\Redis\Cache\Engine\RedisEngine;
use Cake\Core\InstanceConfigTrait;
use Cake\Utility\Security;
use Cake\Http\Response;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Auth\Storage\MemoryStorage;

class RedisStorage extends MemoryStorage
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [
    'token' => [
      'header' => 'authorization',
      'prefix' => 'bearer',
      'parameter' => 'token',
    ],
    'redis' => [
      'database' => 0,
      'duration' => 3600,
      'groups' => [],
      'password' => false,
      'persistent' => true,
      'port' => 6379,
      'prefix' => 'dev.your-site.com:token:',
      'probability' => 100,
      'host' => null,
      'server' => '127.0.0.1',
      'timeout' => 0,
      'unix_socket' => false,
      'serialize' => true,
    ]
  ];

  protected $_engine = null;

  protected $_token = null;

  public function __construct(ServerRequest $request, Response $response, array $config = [])
  {
    $this->setConfig($config);
    $this->_engine = new RedisEngine();
    $this->_engine->init($this->config()['redis']);
    $this->_token = $this->getToken($request);
  }

  public function read()
  {
    return !$this->_token? $this->_token: $this->_engine->read($this->_token);
  }

  /**
  * {@inheritDoc}
  */
  public function write($user)
  {
    if(!$this->_token && !empty($user['x-token']))
    {
      $this->_token = $user['x-token'];
      unset($user['x-token']);
    }
    return $this->_engine->write($this->_token, $user);
  }

  /**
  * {@inheritDoc}
  */
  public function delete()
  {
    $this->_engine->delete($this->_token);
  }

  public function getToken($request = null)
  {
    $config = $this->_config['token'];

    if (!$request) {
      return $this->_token;
    }

    $header = $request->header($config['header']);
    if ($header && stripos($header, $config['prefix']) === 0) {
      return $this->_token = str_ireplace($config['prefix'] . ' ', '', $header);
    }

    if (!empty($this->_config['parameter'])) {
      $this->_token = $request->query($this->_config['parameter']);
    }

    return $this->_token;
  }
}
