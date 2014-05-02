<?php
class Encryption {
 	/**
 	* The below crypting functions are credited to niczak from Github.
 	**/
    public function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($value) { 
        $skey = get_setting('hash_salt');
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $skey, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this -> safe_b64encode($crypttext)); 
    }
 
    public function decode($value)
    {
        $skey = get_setting('hash_salt');
        $crypttext = $this ->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
    /**
    * End of functions by niczak
    **/

    public function hashPassword($pass) {
        return hash('sha512', get_setting('hash_salt') . $pass . get_setting('hash_salt'));
    }

    public function hashActivation($mail) {
        return md5(get_setting('hash_salt') . $mail . get_setting('hash_salt'));
    }
}