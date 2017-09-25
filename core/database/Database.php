<?php

class Database {

    public $conn;

    public function __construct($host, $port, $username, $password, $dbname) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $username;
        $this->password = $password;
        $this->name = $dbname;
    }

    public function connect() {
        $connectStr = "host=$this->host port=$this->port " .
            "dbname=$this->name user=$this->user password=$this->password";
        $this->conn = pg_connect($connectStr)
            or die('Could not connect: ' . pg_last_error());
    }

    public function query($query, $params) {
        return pg_query_params($this->conn, $query, $params);
    }

    public function close() {
        pg_close($this->conn);
    }

}