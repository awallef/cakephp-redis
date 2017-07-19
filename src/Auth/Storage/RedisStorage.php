<?php
namespace Awallef\Redis\Auth\Storage;

use Cake\Core\Configure;
use Awallef\Redis\Cache\Engine\RedisEngine;
use Cake\Core\InstanceConfigTrait;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Auth\Storage\MemoryStorage;

class RedisStorage extends MemoryStorage
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [
    'header' => 'authorization',
    'header-prefix' => 'bearer',
    'parameter' => 'token',

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
  ];

  protected $_engine = null;

  protected $_token = null;

  public function __construct(ServerRequest $request, Response $response, array $config = [])
  {
    try {
      Configure::load('auth', 'default');
      $this->setConfig(Configure::read('Awallef.Redis.auth'));
    } catch (Exception $ex) {
      throw new Exception(__('Missing configuration file: "auth/{0}.php"!!!', 'auth'), 1);
    }
    $this->_engine = new RedisEngine();
    $this->_engine->init($this->config());
    $this->_token = $this->getToken($request);
  }

  public function read()
  {
    return $this->_engine->read($this->_token);
  }

  /**
  * {@inheritDoc}
  */
  public function write($user)
  {
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
    $config = $this->_config;

    if (!$request) {
      return $this->_token;
    }

    $header = $request->header($config['header']);
    if ($header && stripos($header, $config['header-prefix']) === 0) {
      return $this->_token = str_ireplace($config['header-prefix'] . ' ', '', $header);
    }

    if (!empty($this->_config['parameter'])) {
      $this->_token = $request->query($this->_config['parameter']);
    }

    return $this->_token;
  }
}
