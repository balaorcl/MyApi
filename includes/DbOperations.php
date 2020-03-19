<?php
    // class to handle all db operations
    class DbOperations{

        // for connection name
        private $con;

        // constructor
        function __construct(){
            // call the DbConnect
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect;
            $this->con = $db->connect();

        }

	 // create user
	 public function createUser($username, $password, $role){
		// if the user not exists
		if (!$this->isUserExist($username)){
			// pass a prepared statement
			$stmt = $this->con->prepare("insert into users (username, password, role) VALUES (?, ?, ?)");
			// all values are sttring so pass "s"
			$stmt->bind_param("sss", $username, $password, $role);
			if($stmt->execute()){
				return USER_CREATED;
			}else{
				return USER_FAILURE;
			}
		}

		return USER_EXIST;

	}

	// to check user already exist
	private function isUserExist($username){
		$stmt = $this->con->prepare("select id from users where username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		// to get the result
		$stmt->store_result();
		// returns TRUE if number of rows greater than zero, else FALSE
		return $stmt->num_rows > 0;
		 
	}

    }