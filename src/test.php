<?php
    require_once dirname(__FILE__).'/config/config.php';
    require_once dirname(__FILE__).'/utils/sql.php';
    require_once dirname(__FILE__).'/utils/logger.php';
    
    Logger::log("普通信息打印!");
    Logger::info("成功信息打印!");
    Logger::error("错误信息打印!");
    Logger::line();
    //操作一次后就关闭数据库连接推荐用法
    SQL::addOnce('user',array(
        "account"=>'test',
        "password"=>'test',
        "nickname"=>'test',
        "logindate"=>date_create()->format('Y-m-d H:i:s')
     ))?Logger::info('Add Success!'):Logger::error('Add Fail!');
    SQL::delOnce('user',"account",'test')?Logger::info('Delete Success!'):Logger::error('Delete Fail!');
    //通常只有调试情况下才需要打开
    Logger::log(SQL::format(SQL::whereOnce('user')));
    

    //连接一次后需要对数据库多次操作,推荐以下用法可以减少数据库连接
    if(SQL::connect()){
        SQL::add('user',array(
            "account"=>'test',
            "password"=>'test',
            "nickname"=>'test',
            "logindate"=>date_create()->format('Y-m-d H:i:s')
        ))?Logger::info('Add Success!'):Logger::error('Add Fail!');

        SQL::update('user',"logindate",date_create()->format('Y-m-d H:i:s'),'account','test1')?Logger::info('Update Success!'):Logger::error('Update Fail!');;

        Logger::log(SQL::format(SQL::where('user')));

        SQL::close();  
    }
     
