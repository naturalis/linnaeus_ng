<?php

/**
 * Class Db2: provides a persistent connection!
 *
 * This class has been added as a means to circumvent dropping connections
 * using the regular connection class. While creating an index for the traits,
 * the connection kept dropping even if the number of queries was pretty limited.
 *
 * The regular DB class is a singleton, meaning that the class was inaccessible after
 * it was created. This class simply extends the regular mysqli class and copies the
 * required settings from the original Db class.
 *
 * Using this class means using a bit of a hack:
 *
 * Use the switchToPersistentConnection method in your class to switch connectors. Each
 * model used in the class is initiated with the regular Db connector. What this method does
 * is create the Db2 connector and replace the connection in all loaded models with
 * the new one.
 *
 * Note that if your controller loads other Linnaeus controllers, this process has to be
 * repeated for those controllers, as otherwise you may/will still end up with dropped connections.
 *
 */

class Db2 extends mysqli
{
    private $host;
    private $user;
    private $password;
    private $database;
    private $characterSet;

    public function __construct ($config)
    {
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->characterSet = $config['characterSet'] ?? false;

        parent::init();
        parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 1);
        @parent::real_connect('p:' . $this->host, $this->user, $this->password, $this->database);

        if ($this->connect_errno) {
            die("Cannot connect to database!");
        }

        if ($this->characterSet) {
            $this->set_charset($this->characterSet);
        }

        // Problems with strict mode in Traits module; force enable "loose" mode
        $this->query('SET SESSION sql_mode = "NO_ENGINE_SUBSTITUTION"');
    }

    public function ping ()
    {
        @parent::query('SELECT LAST_INSERT_ID()');

        if ($this->errno == 2006) {
            $this->__construct([
                'host' => $this->host,
                'user' => $this->user,
                'password' => $this->password,
                'database' => $this->database,
                'characterSet' => $this->characterSet,
            ]);
        }
    }

    public function disconnect ()
    {
        $this->close();
    }

}