<?php

namespace Rakit\Session;

use SessionHandlerInterface;

class CookieSessionHandler implements SessionHandlerInterface {

    const IV_SEPARATOR = '::';

    protected $cookie_name;

    protected $configs;

    protected $encrypt = false;

    public function __construct($cookie_name = 'rksess', array $configs = array())
    {
        $this->cookie_name = $cookie_name;

        $configs = array_merge(array(
            'expire' => 0,
            'path' => null,
            'domain' => null,
            'secure' => false,
            'httponly' => false,
            'mcrypt_key' => null
        ), $configs);

        if(is_string($configs['mcrypt_key'])) {
            $this->encrypt = true;

            $configs = array_merge(array(
                'mcrypt_chiper' => MCRYPT_BLOWFISH,
                'mcrypt_mode' => MCRYPT_MODE_CBC,
            ), $configs);
        }

        $this->configs = $configs;
    }

    public function open($save_path, $sess_name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sess_id)
    {
        $data = $this->getCookiedata();

        if($this->encrypt) {
            if(!empty($data)) {
                $explode = explode(static::IV_SEPARATOR, $data, 2);
                $this->configs['mcrypt_iv'] = $explode[0];
                $data = $this->decrypt(base64_decode($explode[1]));
            } else {
                $this->configs['mcrypt_iv'] = base64_encode($this->generateIV());
                $data = '';
            }
        }

        return $data? $data : '';
    }

    public function write($sess_id, $data)
    {
        if($this->encrypt) {
            $data = base64_encode($this->encrypt($data));
            $data = $this->configs['mcrypt_iv'].static::IV_SEPARATOR.$data;
        }

        setcookie(
            $this->cookie_name, 
            $data,
            $this->configs['expire'],
            $this->configs['path'],
            $this->configs['domain'],
            $this->configs['secure'],
            $this->configs['httponly']
        );

        return true;
    }

    public function destroy($sess_id)
    {
        if(isset($_COOKIE[$this->cookie_name])) {
            unset($_COOKIE[$this->cookie_name]);
            setcookie($this->cookie_name, '', (-1*24*60*60));
        }

        return true;
    }

    public function gc($lifetime)
    {
        return true;
    }


    protected function getCookiedata()
    {
        return isset($_COOKIE[$this->cookie_name])? $_COOKIE[$this->cookie_name] : '';
    }

    protected function encrypt($plain) {
    
        return mcrypt_encrypt(
            $this->configs['mcrypt_chiper'], 
            $this->configs['mcrypt_key'], 
            $plain, 
            $this->configs['mcrypt_mode'], 
            base64_decode($this->configs['mcrypt_iv'])
        );
        
    }

    protected function decrypt($encrypted) {
        
        return rtrim(mcrypt_decrypt(
            $this->configs['mcrypt_chiper'], 
            $this->configs['mcrypt_key'], 
            $encrypted, 
            $this->configs['mcrypt_mode'], 
            base64_decode($this->configs['mcrypt_iv'])
        ));
        
    }

    protected function generateIV()
    {
        $iv_size = mcrypt_get_iv_size($this->configs['mcrypt_chiper'], $this->configs['mcrypt_mode']);
        return mcrypt_create_iv($iv_size);
    }

    protected function validEncryptedCookie($data)
    {
        $base64_regex = "[a-zA-Z0-9\/\=\_\@\-]+";
        $regex = '/^'.$base64_regex.static::IV_SEPARATOR.$base64_regex.'$/';
        return preg_match($regex, $data);
    }


}
