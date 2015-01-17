<?php

namespace Rakit\Session;

class Flash {

    const KEY_LIFE = 'n';
    const KEY_VALUE = 'v';

    protected $session_manager;

    protected $flash_key;

    public function __construct(SessionManager $session_manager, $flash_key = '__flash')
    {
        $this->session_manager = $session_manager;
        $this->flash_key = $flash_key;

        $flash = $this->raw();

        foreach($flash as $key => $data) {
            $flash[$key][static::KEY_LIFE] = intval(@$data[static::KEY_LIFE]) - 1;
            
            if($flash[$key][static::KEY_LIFE] < 0) {
                unset($flash[$key]);
            }
        }

        $this->session_manager->set($this->flash_key, $flash);
    }

    public function getSessionManager()
    {
        return $this->session_manager;
    }

    public function raw()
    {
        return $this->session_manager->get($this->flash_key, array());
    }

    public function all()
    {
        $raw_flash = $this->raw();

        $flash = array();
        foreach($raw_flash as $key => $data) {
            $flash[$key] = $data[static::KEY_VALUE];
        }

        return $flash;
    }

    public function has($key)
    {
        $flash = $this->raw();
        return array_key_exists($key, $flash);
    }

    public function get($key, $default = null)
    {
        $flash = $this->raw();

        return array_key_exists($key, $flash)? @$flash[$key][static::KEY_VALUE] : $default;
    }

    public function set($key, $value, $life = 1)
    {
        $flashdata = array();
        $flashdata[static::KEY_LIFE] = $life;
        $flashdata[static::KEY_VALUE] = $value;

        $flash = $this->raw();
        $flash[$key] = $flashdata;

        $this->session_manager->set($this->flash_key, $flash);
    }

    public function keep($key, $life = 1)
    {
        if(0 === func_get_args()) {
            $flash = $this->raw();
            
            foreach($flash as $key => $data) {
                $this->set($key, $this->get($key), 1);
            }
        } else {
            $this->set($key, $this->get($key), $life);
        }
    }

    public function remove($key)
    {
        $flash = $this->raw();
        unset($flash[$key]);

        $this->session_manager->set($this->flash_key, $flash);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }

    public function __unset($key)
    {
        return $this->remove($key);
    }

}