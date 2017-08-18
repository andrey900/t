<?php
define('APP_DIR', realpath('..').DIRECTORY_SEPARATOR);

require APP_DIR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Create and configure Slim app
$config = [
	'settings' => [
	    'addContentLengthHeader' => false,
	    'displayErrorDetails' => true,
	    'db' => [
			'host' => "localhost",
			'user' => "user",
			'pass' => "password",
			'dbname' => "exampleapp"
		]
	]
];

$app = new \Slim\App($config);

$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

$app->get('/', function (Request $request, Response $response, $args) {
    $response = $this->view->render($response, "tickets.phtml", ["tickets" => $tickets]);
    return $response;
})->setName("home");

$app->group('/api/v_1', function () {
    $this->get('/', function ($request, $response, $args) {
    	$data = [
    		'get' => '/api/v1.0/get-match/[0-9]+',
    		'post' => '/api/v1.0/reservation/[0-9]+',
    		'post' => '/api/v1.0/reservation-order/[0-9]+'
    	];
    	return $response->withJson($data);
    })->setName('api-allow-method');

    $this->get('/get-match/{id:[0-9]+}', function ($request, $response, $args) {
    	$reservation = [];
    	$reserved = [];

    	$data = file_get_contents(APP_DIR.'reservation.json');
    	if( $data )
    		$reservation = json_decode($data);
    	
    	$data = file_get_contents(APP_DIR.'reserved.json');
    	if( $data )
    		$reserved = json_decode($data);

    	$result = [
			'reserved' => $reserved,
			'reservation' => $reservation
		];

		return $response->withJson($result);
    })->setName('api-get-match');

    $this->post('/reservation/{id:[0-9]+}', function ($request, $response, $args) {
    	$parsedBody = $request->getParsedBody();

    	if( $parsedBody['places'] ){
	    	$reservation = file_get_contents(APP_DIR.'reservation.json');

	    	if( !$reservation ){
	    		$reservation = [];
	    	} else {
	    		$reservation = json_decode($reservation);
	    	}

    		foreach ($parsedBody['places'] as $place) {
    			$reservation[] = $place;
    		}
    		
    		file_put_contents(APP_DIR.'reservation.json', json_encode((array)$reservation));
    	}

    	return $response->withJson(['status' => 'success']);
    })->setName('api-add-reservation');

    $this->delete('/reservation/{id:[0-9]+}', function ($request, $response, $args) {
    	$reservation = file_get_contents(APP_DIR.'reservation.json');
    	if( !$reservation ){
    		$reservation = [];
    	} else {
    		$reservation = json_decode($reservation);
    	}

    	$parsedBody = $request->getParsedBody();
    	if( $parsedBody['places'] ){
    		foreach ($parsedBody['places'] as $place) {
    			if( ($k = array_search($place, $reservation)) !== false )
    				unset($reservation[$k]);
    		}
    		$reservation = array_values($reservation);
    	}

    	file_put_contents(APP_DIR.'reservation.json', json_encode((array)$reservation));

    	return $response->withJson(['status' => 'success', 'app' => $parsedBody]);
    })->setName('api-delete-reservation');

    $this->post('/reservation-order/{id:[0-9]+}', function ($request, $response, $args) {
    	$reserved = file_get_contents(APP_DIR.'reserved.json');
    	if( !$reserved ){
    		$reserved = [];
    	} else {
    		$reserved = json_decode($reserved);
    	}

    	$parsedBody = $request->getParsedBody();
    	if( $parsedBody['places'] ){
    		foreach ($parsedBody['places'] as $place) {
    			$reserved[] = $place;
    		}
    	}
    	file_put_contents(APP_DIR.'reserved.json', json_encode((array)$reserved));

    	$reservation = file_get_contents(APP_DIR.'reservation.json');
    	if( !$reservation ){
    		$reservation = [];
    	} else {
    		$reservation = json_decode($reservation);
    	}

    	if( $parsedBody['places'] ){
    		foreach ($parsedBody['places'] as $place) {
    			if( ($k = array_search($place, $reservation)) !== false )
    				unset($reservation[$k]);
    		}
    		$reservation = array_values($reservation);
    	}

    	file_put_contents(APP_DIR.'reservation.json', json_encode((array)$reservation));

    	return $response->withJson(['status' => 'success']);
    })->setName('api-reservation-order');
});

// Run app
$app->run();