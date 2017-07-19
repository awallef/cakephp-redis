<?php
namespace Awallef\Redis\Auth\Storage;

use Cake\Auth\Storage\MemoryStorage

/**
* Memory based non-persistent storage for authenticated user record.
*/
class RedisStorage implements MemoryStorage
{
  protected $_defaultConfig = [
    'key' => 'Auth.User',
    'redirect' => 'Auth.redirect'
  ];

  public function read()
  {
    return $this->_user;
  }

  /**
  * {@inheritDoc}
  */
  public function write($user)
  {
    $this->_user = $user;
  }

  /**
  * {@inheritDoc}
  */
  public function delete()
  {
    $this->_user = null;
  }
}
