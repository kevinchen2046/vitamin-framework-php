<?php

class Code{
    public const NONE=-1;
    public const SUCESS=0;
    public const ERROR=1;
    
    public static function getName($code){
        switch($code){
            case Code::SUCESS:
                return '操作成功';
            case Code::ERROR:
                return '操作失败';
        }
        return '';
    }
}

class OutType{
    public const ALL=-1;
    public const SCREEN=0;
    public const FILE=1;
}

class Logger{

    public static function log($msg,$code=-1,$outtype=-1){
        ($outtype==OutType::ALL||$outtype==OutType::SCREEN)&&Logger::outscreen($msg,$code,'LOG');
        ($outtype==OutType::ALL||$outtype==OutType::FILE)&&Logger::outfile($msg,$code,'LOG');
        return true;
    }

    public static function error($msg,$code=-1,$outtype=-1){
        ($outtype==OutType::ALL||$outtype==OutType::SCREEN)&&Logger::outscreen($msg,$code,'ERROR');
        ($outtype==OutType::ALL||$outtype==OutType::FILE)&&Logger::outfile($msg,$code,'ERROR');
        return true;
    }

    public static function info($msg,$code=-1,$outtype=-1){
        ($outtype==OutType::ALL||$outtype==OutType::SCREEN)&&Logger::outscreen($msg,$code,'INFO');
        ($outtype==OutType::ALL||$outtype==OutType::FILE)&&Logger::outfile($msg,$code,'INFO');
        return true;
    }

    public static function line(){
        echo "<hr/>";
    }

    public static function outscreen($msg,$code,$logtype=''){
        Logger::__output($code,$msg,$logtype,OutType::SCREEN);
        return true;
    }

    public static function outfile($msg,$code,$logtype=''){
        Logger::__output($code,$msg,$logtype,OutType::FILE);
        return true;
    }

    private static function __output($code,$msg,$logtype,$outtype){
        if(is_object($msg)||is_array($msg)){
            $msg = json_encode($msg);
        }else{
            $msg =$msg.'';
        }
        $content = '['.$logtype.'] '.($code>=0?'['.Code::getName($code).'] ':'').$msg;
        $color='#888888';
        switch($logtype){
            case 'ERROR':
                $color='#CC0000';   
            break;
            case 'INFO':
                $color='#00CC00';   
            break;
        }
        if($outtype==OutType::SCREEN) echo '<b><font color="'.$color.'" size="1px">'.$content.'</font></b><br/>';
        if($outtype==OutType::FILE) (error_log('\n\r'.$content));
        return true;
    }
}
