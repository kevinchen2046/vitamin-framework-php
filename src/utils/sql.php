<?php
    
    class SQL
    {
        const HOST='127.0.0.1:3306';
        const DB_NAME='jueshihaozhan';
        const USER_NAME='kevinchen';
        const USER_PASSWORD='cc123456';
        static $conn=null;
        /**
         * 接数据库
         */
        public static function connect(){
            //面向对象
            SQL::$conn=new mysqli(SQL::HOST, SQL::USER_NAME, SQL::USER_PASSWORD,SQL::DB_NAME);
            if (SQL::$conn->connect_errno) {
                echo SQL::$conn->connect_error;
                return false;
            }
            return true;
        }

        /**
         * 关闭数据库连接
         */
        public static function close(){
            SQL::$conn->close(); 
            SQL::$conn=null;
        }

        /**
         * 添加数据
         * @param string $table 表名
         * @param object $object 跟当前表匹配的数据对象
         */
        public static function add($table,$object){
            $sql=SQL::sqladd($table,$object);
            $res=SQL::$conn->query($sql);
            return $res;
        }
        
        /**
         * 删除数据
         * @param string $table 表名
         * @param string $whereProperty 条件的字段名称
         * @param any $whereValue 跟条件的字段名称的匹配的值
         */
        public static function del($table,$whereProperty,$whereValue){
            $sql=SQL::sqldel($table,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            return $res;
        }

        /**
         * 更新数据
         * @param string $table 表名
         * @param string $property 需要更新字段名称
         * @param any $value 跟需要更新的字段名称的匹配的值
         * @param string $whereProperty 条件的字段名称
         * @param any $whereValue 跟条件的字段名称的匹配的值
         */
        public static function update($table,$property,$value,$whereProperty,$whereValue){
            $sql=SQL::sqlupdate($table,$property,$value,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            return $res;
        }
        
        /**
         * 查询数据
         * @param string $table 表名
         * @param string 可选 $whereProperty 条件的字段名称
         * @param any 可选 $whereValue 跟条件的字段名称的匹配的值
         * 当前条件默认为null时，则返回整个表数据
         */
        public static function where($table,$whereProperty=null,$whereValue=null){
            $sql=SQL::sqlwhere($table,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            return $res;
        }

        /**
         * 添加数据一次-含有连接和关闭数据库操作
         * @param string $table 表名
         * @param object $object 跟当前表匹配的数据对象
         */
        public static function addOnce($table,$object){
            if(!SQL::connect()) return false;
            $sql=SQL::sqladd($table,$object);
            $res=SQL::$conn->query($sql);
            SQL::close();
            return $res;
        }

        /**
         * 删除数据一次-含有连接和关闭数据库操作
         * @param string $table 表名
         * @param string $whereProperty 条件的字段名称
         * @param any $whereValue 跟条件的字段名称的匹配的值
         */
        public static function delOnce($table,$whereProperty,$whereValue){
            if(!SQL::connect()) return false;
            $sql=SQL::sqldel($table,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            //$res->free();
            SQL::close();
            return $res;
        }

        /**
         * 更新数据一次-含有连接和关闭数据库操作
         * @param string $table 表名
         * @param string $property 需要更新字段名称
         * @param any $value 跟需要更新的字段名称的匹配的值
         * @param string $whereProperty 条件的字段名称
         * @param any $whereValue 跟条件的字段名称的匹配的值
         */
        public static function updateOnce($table,$property,$value,$whereProperty,$whereValue){
            if(!SQL::connect()) return false;
            $sql=SQL::sqlupdate($table,$property,$value,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            //$res->free();
            SQL::close();
            return $res;
        }

        /**
         * 查询数据一次-含有连接和关闭数据库操作
         * @param string $table 表名
         * @param string 可选 $whereProperty 条件的字段名称
         * @param any 可选 $whereValue 跟条件的字段名称的匹配的值
         * 当前条件默认为null时，则返回整个表数据
         */
        public static function whereOnce($table,$whereProperty=null,$whereValue=null){
            if(!SQL::connect()) return null;
            $sql=SQL::sqlwhere($table,$whereProperty,$whereValue);
            $res=SQL::$conn->query($sql);
            //$res->free();
            SQL::close();
            return $res;
        }

        /**
         * 格式化查询的数据
         * @param string $res 查询结果
         */
        public static function format($res){
            if(!$res) return 'Not a sql Valid Result!';
            $result='';
            while ($row=$res->fetch_row()) {
                foreach ($row as $key => $value) {
                    $result=$result."<br/><li/>".$value;
                }
                $result=$result."<hr/>";
            }
            $res->free();
            return $result;
        }
        
        private static function sqladd($table,$object){
            //$sqlInsert = "insert into user (id,name) values('6','hah')";
            $keys=[];
            $values=[];
            foreach ($object as $key => $value) {
                array_push($keys,$key);
                array_push($values,is_numeric($value)?$value:("'".$value."'"));
            }
            $sql="insert into ".$table." (".join(',',$keys).") values(".join(',',$values).")";
            return $sql;
        }

        private static function sqldel($table,$whereProperty,$whereValue){
            //删除数据
            //$sqldelete = "delete from user where id = 6";
            $whereValue=is_numeric($whereValue)?$whereValue:("'".$whereValue."'");
            return "delete from ".$table." where ".$whereProperty." = ".$whereValue;
        }

        private static function sqlupdate($table,$property,$value,$whereProperty,$whereValue){
            //修改数据
            //$sqlUpdate = "update user set name = 'weizhi2' where name = 'iceboy'";
            $value=is_numeric($value)?$value:("'".$value."'");
            $whereValue=is_numeric($whereValue)?$whereValue:("'".$whereValue."'");
            return "update user set ".$property." = ".$value." where ".$whereProperty." = ".$whereValue;
        }

        private static function sqlwhere($table,$whereProperty,$whereValue){
              //查询name字段
              //$sql1 = "select * from user WHERE id = 0";
              if($whereProperty!=null&&$whereValue!=null){
                $whereValue=is_numeric($whereValue)?$whereValue:("'".$whereValue."'");
                return "select * from ".$table." WHERE ".$whereProperty." = ".$whereValue;
              }
              return "select * from ".$table;
        }
    }
