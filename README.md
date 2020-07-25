
# Vitamin框架之PHP

Php版本的Vitamin其实是一个语法糖,并没有抽象出MVC的架构

## 部署
    部署php请修改gulpfile.js

## mysql操作封装 
    src/utils/sql.php

## logger日志操作封装 
    src/utils/logger.php
## request http&https请求操作封装 
    src/utils/request.php
## util 其他操作封装 
    src/utils/util.php

### test.php 页输出
```php
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
```

<b><font color="#888888" size="1px">[LOG] 普通信息打印!</font></b><br/><b><font color="#00CC00" size="1px">[INFO] 成功信息打印!</font></b><br/><b><font color="#CC0000" size="1px">[ERROR] 错误信息打印!</font></b><br/><hr/><b><font color="#00CC00" size="1px">[INFO] Add Success!</font></b><br/><b><font color="#00CC00" size="1px">[INFO] Delete Success!</font></b><br/><b><font color="#888888" size="1px">[LOG] <br/><li/>1<br/><li/>test1<br/><li/>123456<br/><li/>someone<br/><li/>2020-03-31 20:30:54<hr/><br/><li/>19<br/><li/>123<br/><li/>test<br/><li/>test<br/><li/>2020-03-31 19:19:05<hr/></font></b><br/><b><font color="#00CC00" size="1px">[INFO] Add Success!</font></b><br/><b><font color="#00CC00" size="1px">[INFO] Update Success!</font></b><br/><b><font color="#888888" size="1px">[LOG] <br/><li/>1<br/><li/>test1<br/><li/>123456<br/><li/>someone<br/><li/>2020-03-31 20:31:36<hr/><br/><li/>19<br/><li/>123<br/><li/>test<br/><li/>test<br/><li/>2020-03-31 19:19:05<hr/><br/><li/>58<br/><li/>test<br/><li/>test<br/><li/>test<br/><li/>2020-03-31 20:31:36<hr/></font></b><br/>
