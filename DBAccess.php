<?php
namespace KA;
/*
 * Singleton DBAccess class for Mysql.
 *  require >= PHP7.
 *
*/
class DBAccess {
    private static $instance;
    private $db;
    private $stmt;

    /*
        Initialize.
        @ds = [
                "host" => "your dbhost",            // default: localhost
                "db_name" => "your dbname",
                "port" => "your dbport",            // default: 3306
                "encording" => "your encording",    // default: utf8
                "user" => "your dbuser",
                "password" => "your dbpass",

            ]
    */
    private function __construct($ds = []) {
        // Check required parameters.
        $requires = ["db_name", "user", "password"];
        foreach ($requires as $r) {
            if (!isset($ds[$r])) {
                throw new \Exception("Required parameter hasn't been set.");
            }
        }

        // Datasources.
        $dsn = "mysql"
				.":host=" .($ds["host"] ?? "localhost")
				.";dbname=" .$ds["db_name"]
				.";port=" .($ds["port"] ?? "3306")
				.";";

        // PDO Options.
		$opt = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " .($ds["encording"] ?? "utf8"),
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
		$this->db = new \PDO($dsn, $ds["user"], $ds["password"], $opt);
    }


    /*
        Get My instance.
    */
    final public static function getInstance($ds=[]) {
        if (!isset(self::$instance)) {
            self::$instance = new self($ds);
        }
        return self::$instance;
    }


    /*
        Deny to clone myself
    */
    final public function __clone() {
        throw new \Exception("Cannot clone myself.");
    }


    /*
        Deny to clone myself
    */
    public function release() {
        unset($this->db);
        unset($this->stmt);
    }


    /*
        Get current statement.
    */
    public function getStatement() {
        return $this->stmt ?? null;
    }


    /*
        Get current statement.
    */
    public function getDB() {
        return $this->db ?? null;
    }


    /*
        Get last inserted id.
    */
    public function getLastInsertId() {
        return $this->db->lastInsertId() ?? null;
    }


    /*
        Execute query.
    */
    public function execute($sql, $params=[]) {
        $this->stmt = $this->db->prepare($sql);
		(empty($params)) ? $this->stmt->execute() : $this->stmt->execute($params);
    }


    /*
        Fetch result.
    */
    public function fetch() {
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /*
        Fetch All results.
    */
    public function fetchAll() {
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}
