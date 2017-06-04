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

    const CONFIG_OFFLINE = array(
        'DATABASE_NAME'=>'zeus',
        'HOST'=>'localhost',
        'USER'=>'root',
        'PASSWORD'=>'',
        'PORT'=>33012
    );

    const CONFIG_ONLINE = array(
        'DATABASE_NAME'=>'11111',
        'HOST'=>'1111.org',
        'USER'=>'1111',
        'PASSWORD'=>'11111',
        'PORT'=>33012
    );

    const CONNECTION_TYPE = ['pdo'=>'PDO', 'mysqli'=>'MYSQLI'];

    /**
     * Start Connection constructor.
     * @param string $connection_type
     * @throws ErrorException
     */
    public function __construct(String $connection_type)
    {
        if(in_array($connection_type,self::CONNECTION_TYPE)) {
            switch ($connection_type) {
                case 'PDO':
                    $this->pdoConnection();
                    break;
                case 'MYSQLI':
                    $this->mysqliConnection();
                    break;
                default:
                    throw new ErrorException('This Connection Type does not exist');
                    break;
            }
        } else{
            throw new ErrorException('Connection Type not Found');
        }
    }

    /**
     * This is used to connect using mysqli
     */
    private function mysqliConnection() {
        try{
        return $this->connection = new mysqli(
                self::CONFIG_OFFLINE['HOST'],
                self::CONFIG_OFFLINE['USER'],
                self::CONFIG_OFFLINE['PASSWORD'],
                self::CONFIG_OFFLINE['DATABASE_NAME'],
                self::CONFIG_OFFLINE['PORT']
            );
        }catch (Exception $e){
            throw new ErrorException("Connection failed  with Error Message: " . $e->getMessage());
        }
    }

    /**
     * This is used to connect using PDO
     */
    private function pdoConnection() {
        try {
            $this->connection = new PDO(
                "mysql:host=".self::CONFIG_OFFLINE['HOST'].
                    ";dbname=".self::CONFIG_OFFLINE['DATABASE_NAME'].
                    ";port=".self::CONFIG_OFFLINE['PORT'],
                    self::CONFIG_OFFLINE['USER'],
                    self::CONFIG_OFFLINE['PASSWORD']
            );
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connection;
        }
        catch(PDOException $e)
        {
            throw new ErrorException("Connection failed  with Error Message: " . $e->getMessage());
        }
    }

    /**
     * End connection Destructor
     */
/*    public function __destruct(){
        $this->connection->close();
    }*/
}