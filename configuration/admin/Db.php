<?php
class Db
{
    private static $instance = array();

    /**
     * The constructor is set to private so
     * nobody can create a new instance using new
      */
    private function __construct ()
    {
        //mysqli_report(MYSQLI_REPORT_STRICT);
    }

    public static function createInstance ($id, array $config)
    {
        if (isset(self::$instance[$id])) {
            return false;
        }

        self::$instance[$id] = @mysqli_connect(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']
        );

        if (mysqli_connect_error()) {
             return false;
        }

        if (isset($config['characterSet'])) {
            mysqli_query(self::$instance[$id],
                'SET NAMES ' . $config['characterSet']);
            mysqli_query(self::$instance[$id],
                'SET CHARACTER SET ' . $config['characterSet']);
        }

        return true;
     }

    /**
     * Return DB instance or create intitial connection
     *
     * @return object (PDO)
     * @access public
     */
    public static function getInstance ($id)
    {
        if (!isset(self::$instance[$id])) {
            return false;
        }
        return self::$instance[$id];
    }

    /**
     * Like the constructor, we make __clone private
     * so nobody can clone the instance
     */
    private function __clone ()
    {

    }

}