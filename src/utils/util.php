<?php

class AesUtil{

    public static function encrypt($data, $key){
        $iv=$key;
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);  
        return base64_encode($encrypted);
    }

    public static function decrypt($encrypted, $publicKey,$iv){
        $encryptedData = base64_decode(base64_encode($encrypted));  
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $publicKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        return $decrypted;
    }
}



Class Util{
 
    public static function msecTime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    public static function getIp(){
        $ip='';
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }
}