<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Class Database
 *
 * The database object holds all information about the currently
 * connected database. It also handles the queries to execute
 * on the database.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.2
 * @since 0.1
 */
class Database {

    public $conn;

    /**
     * Database constructor.
     * @param $host string the host address
     * @param $port string the database's running port
     * @param $username string username credential
     * @param $password string password credential
     * @param $dbname string name of database to use
     */
    public function __construct($host, $port, $username, $password, $dbname) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $username;
        $this->password = $password;
        $this->name = $dbname;
    }

    public static function loadFromConfig() {
        $conn_settings = CONFIG['database'];
        return new self($conn_settings['host'], $conn_settings['port'],
            $conn_settings['user'], $conn_settings['pass'],
            $conn_settings['dbname']);
    }

    /**
     * Connects to the database based on constructor values.
     */
    public function connect() {
        $connectStr = "host=$this->host port=$this->port " .
            "dbname=$this->name user=$this->user password=$this->password";
        $this->conn = pg_connect($connectStr)
            or die('Could not connect: ' . pg_last_error());
    }

    /**
     * Prepares a query to be executed later. (@see Database.execute()).
     * This is helpful if a query is executed more than once.
     * @param $name string the name of the query (to be accessed later)
     * @param $query string the query to execute
     * @return resource the result of the prepare statement
     */
    public function prepare($name, $query) {
        return pg_prepare($this->conn, $name, $query);
    }

    /**
     * Executes a prepared query (uses the name set in Database.prepare())
     * @param $name string the name of the query (set in prepare statement)
     * @param $params string[] the query parameters for the given query
     * @return resource the result of the query
     */
    public function execute($name, $params) {
        pg_send_execute($this->conn, $name, $params);
        return pg_get_result($this->conn);
    }

    /**
     * Executes the given query once and returns the results.
     * @param $query string the query to execute
     * @param $params string[] the query parameters for the given query
     * @return resource the result of the query
     */
    public function query($query, $params) {
        pg_send_query_params($this->conn, $query, $params);
        return pg_get_result($this->conn);
    }

    public function no_param_query($query) {
        return pg_query($this->conn, $query);
    }

    /**
     * Closes the database connection
     */
    public function close() {
        pg_close($this->conn);
    }

}