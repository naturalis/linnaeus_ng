<?php
class Db
{
    private static $instance = array();

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

        // Problems with strict mode in Traits mode; force enable "loose" mode
        mysqli_query(self::$instance[$id],
            'SET SESSION sql_mode = "NO_ENGINE_SUBSTITUTION"');

        return true;
    }

    public static function getInstance ($id)
    {
        if (!isset(self::$instance[$id])) {
            return false;
        }
        return self::$instance[$id];
    }

    private function __clone ()
    {

    }

}