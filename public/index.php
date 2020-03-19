<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/DbConnect.php';
require __DIR__ . '/../includes/DbOperations.php';

// $app = AppFactory::create();
$app = new \Slim\App;

/*
get request
{name} = first parameter

function (Request $request, Response $response, array $args)
This is second parameter
Request = Request object
Response = Response object
args = arguments passed as "name"
it gets the name and returns the same
*/

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name, How are you?");

    $db = new DbConnect;

    if ($db->connect()!=null){
        echo 'Connection Successfull';
    }
    return $response;
});    

/*
First API call
    endpoint: createuser
    parameters: username, password, role
    method: POST to create a new record in database
    firstparameter = url like /createuser
    secondparameter is a function which takes Request and Response as arguments
*/    
$app->post('/createuser', function(Request $request, Response $response){
    
    // if parameters are not empty create record
    if(!haveEmptyParameters(array('username', 'password', 'role'), $request, $response)){
        // get the parameters from the request object
        $request_data = $request->getParsedBody();

        // get the values
        $username = $request_data['username'];
        $password = $request_data['password'];
        $role = $request_data['role'];

        // encrypt the password
        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        // create a DbOperations object
        $db = new DbOperations;

        // call the CreateUser method
        $result = $db->CreateUser($username, $hash_password, $role);

        // check the result
        if ($result == USER_CREATED) {
            # code...
            $message = array();
            $message['error'] = false;
            $message['message'] = 'User created successfully';
            $response->write(json_encode($message));

            // 201 is http status coode for resource created
            // google for http status codes
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

            
        } else if($result == USER_FAILURE){
            # code...
            $message = array();
            $message['error'] = true;
            $message['message'] = 'Some error occurred';
            $response->write(json_encode($message));

            // 422 (Unprocessable Entity)
            // google for http status codes
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);            
        } else if($result == USER_EXISTS){
            $message = array();
            $message['error'] = true;
            $message['message'] = 'User already exists';
            $response->write(json_encode($message));

            // 422 (Unprocessable Entity)
            // google for http status codes
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);            
        }
    }
    // default return error
    return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(422);            
});

// function to validate the params
function haveEmptyParameters($required_params, $request, $response){
    //initialization false
    $error = false; 
    //to get the empty params
    $error_params = '';
    //get all the request params with the current request
    // $request_params = $_REQUEST;
    $request_params = $request->getParsedBody();

    // loop through params
    foreach($required_params as $param){
        // check the parameter is empty or parameter length is zero
        // !isset checks whether the parameter is empty
        if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0){
            # code...
            $error = true;
            // concatenate the parameter in error_params
            $error_params .= $param . ', ';
        }
    }

    if ($error) {
        # code...
        $error_detail = array();
        $error_detail['error'] = true;
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        // use the $response object to return the response
        // encode the error_detail in json format
        $response->write(json_encode($error_detail));
    }
    return $error;
}



// App run method
$app->run();
/*
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

// Run app
$app->run();
*/