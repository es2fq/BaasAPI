 <?php
	require_once 'config.php';
	require_once 'BaasAPI.php';

	$app = new \Slim\Slim();

	header("Content-Type: application/json");

	$baasAPI = new BaasAPI();

	$app->add(new \Slim\Middleware\HttpBasicAuthentication([
		"users" => [
			"dcxpUser" => "BaasBikesHave2Wheels"
		]
	]));


	$app->post('/cancelReservation', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email	= $data->email;
		$password	= $data->password;
		$bikeID		= $data->bikeID;
		$response = $baasAPI->cancelReservation( $email , $password , $bikeID );
		echo json_encode( array( $response ) );
	});

	$app->post('/jsonTest', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		echo json_encode( $data );
	});
	
	$app->post('/getOpenBikesWithinDistance', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$latitude 	= floatval($data->latitude);
		$longitude 	= floatval($data->longitude);
		$radius 	= floatval($data->radius);
		$response = $baasAPI->getOpenBikesWithinDistance( $latitude , $longitude , $radius );
		echo json_encode( $response );
	});

	$app->post('/reserve', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email    = $data->email;
		$password = $data->password;
		$bikeID   = $data->bikeID;
		$response = $baasAPI->reserve( $email , $password , $bikeID );
		echo json_encode( array($response) );
	});
	
	$app->post('/signInUser', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email    = $data->email;
		$password = $data->password;
		$response = $baasAPI->signInUser( $email , $password );
		echo json_encode( array($response) );
	});

	$app->post('/signUpUser', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email 		= $data->email;
		$password 	= $data->password;
		// $phone 		= $data->phonenumber;
		$token		= $data->token;
		$response = $baasAPI->signUpUser( $email , $password , $token );
		echo json_encode( array($response) );
	});
	
	$app->post('/start', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email	= $data->email;
		$password	= $data->password;
		$bikeID		= $data->bikeID;
		$latitude 	= floatval($data->latitude);
		$longitude 	= floatval($data->longitude);
		$response = $baasAPI->start( $email , $password , $bikeID , $latitude , $longitude );
		echo json_encode( array($response) );
	});
	
	$app->post('/stop', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$email	= $data->email;
		$password	= $data->password;
		$bikeID		= $data->bikeID;
		$latitude 	= floatval($data->latitude);
		$longitude 	= floatval($data->longitude);
		$response = $baasAPI->stop( $email , $password , $bikeID , $latitude , $longitude );
		echo json_encode( $response );
	});
	
	$app->post('/unlock', function () use ( $app , $baasAPI ) {
		$data = json_decode( $app->request->getBody() );
		$bikeID		= $data->bikeID;
		$latitude 	= floatval($data->latitude);
		$longitude 	= floatval($data->longitude);
		$response = $baasAPI->unlock( $bikeID , $latitude , $longitude );
		echo json_encode( array($response) );
	});
	

	$app->run();
?>