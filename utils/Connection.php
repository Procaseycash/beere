<?php

/**
 * Created by PhpStorm.
 * User: Kazeem Olanipekun <kezyolanipekun@gmail.com> <08135061846>
 * Date: 12/12/2016
 * Time: 03:25 AM
 */
class Connection
{
protected $connection;

    const CONFIG_OFFLINE=array(
        'DATABASE_NAME'=>'zeus',
        'HOST'=>'localhost',
        'USER'=>'root',
        'PASSWORD'=>'mysql001'
    );

    const CONFIG_ONLINE=array(
        'DATABASE_NAME'=>'11111',
        'HOST'=>'1111.org',
        'USER'=>'1111',
        'PASSWORD'=>'11111'
    );

    /**
     * Start Connection.
     */
    public function __construct()
    {
        try{
            $this->connection=new mysqli(self::CONFIG_OFFLINE['HOST'],self::CONFIG_OFFLINE['USER'],self::CONFIG_OFFLINE['PASSWORD'],self::CONFIG_OFFLINE['DATABASE_NAME']);
        }catch (Exception $e){
            echo"Connection Failed with Error Message: ".$e->getMessage();
        }
    }

    /**
     * End Connection
     */
    public function __destruct(){
    $this->connection->close();
}
}