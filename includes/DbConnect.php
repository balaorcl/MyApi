<?php
    
    class DbConnect{
        // to store the connection name
        private $con;

        // to connect the database
        function connect(){
            // get the Constants.php
            include_once dirname(__FILE__)  . '/Constants.php';

            // for connection create a MySqli object
            // The MySQLi functions allows you to access MySQL database servers
            $this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3308);

            // in case of connection error
            if(mysqli_connect_errno()){
                echo "Failed to connect " . mqsqli_connect_error();
                return null;
            }

            return $this->con;

    }
   }