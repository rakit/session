<?php namespace Rakit\Session;

use SessionHandlerInterface;
use Exception;
use Rakit\Session\CookieSessionHandler;

class SessionManager {
    
    protected $handler;
    
    public $flash;
    
    protected $flash_key = array();
    
    public function __construct(SessionHandlerInterface $handler = null, $flash_key = 'flash') {
        if (session_status() == PHP_SESSION_ACTIVE) {
            throw new Exception("You don't need to manually use session_start() if you want to use 'rakit/session' library");
        }
        
        $this->handler = $handler;
        $this->flash_key = $flash_key;
        
        if ($this->handler) session_set_save_handler($handler);
        
        session_start();
        
        $this->flash = new Flash($this, $flash_key);
    }
    
    public function getHandler() {
        return $this->handler;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }
    
    public function all($include_flash = false) {
        $sess = $_SESSION;
        if (!$include_flash) unset($sess[$this->flash_key]);
        
        return $sess;
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public function __get($key) {
        return $this->get($key);
    }
    
    public function __set($key, $value) {
        return $this->set($key, $value);
    }
    
    public function __isset($key) {
        return $this->has($key);
    }
    
    public function __unset($key) {
        return $this->remove($key);
    }
}
