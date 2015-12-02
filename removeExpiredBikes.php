<?php
require_once 'config.php';

use Parse\ParseObject;
use Parse\ParseGeoPoint;
use Parse\ParseQuery;
use Parse\ParseUser;


echo date("D M d, Y G:i") . " - ";

echo getExpiredReservations();
echo getStolenBikes();


// Un-reserve expired reservations
function getExpiredReservations(){
	$query = new ParseQuery( "Bikes" );
	$query->exists( "reservedTime" );
	$query->limit( 1000 );
	$results = $query->find();

	$count = count( $results );

	$expiredBikes = 0;

	for( $i = 0; $i < $count; $i++ ) {
		$reservedDate = $results[$i]->get( "reservedTime" );
		$reservedDate = $reservedDate->format( 'Y-m-d H:i:s' );
		$reservedDate = strtotime( $reservedDate );

		$currentDate = new DateTime( 'now' );
		$currentDate = $currentDate->format( 'Y-m-d H:i:s' );
		$currentDate = strtotime( $currentDate );

		$subtractedDate = $currentDate - $reservedDate;

		$expiredTime = 15;

		if( $subtractedDate >= $expiredTime*60 ) {
			$expiredBikes++;
			deleteField( $results[$i] , "currentUser" );
			deleteField( $results[$i] , "reservedTime" );
		}
	}

	$rString = "";

	if($expiredBikes > 0){
		$rString = "Successfully removed ".$expiredBikes." expired reservations, ";
	}

	return $rString;
}


// Moves stolen bikes to LostBikes Parse table
function getStolenBikes() {
	$query = new ParseQuery( "Bikes" );
	$query->exists( "sessionStartTime" );
	$query->limit( 1000 );
	$results = $query->find();

	$count = count( $results );

	$listOfBikes = array();

	$stolenBikes = 0;

	for( $i = 0; $i < $count; $i++ ) {
		$startDate = $results[$i]->get( "sessionStartTime" );
		$startDate = $startDate->format( 'Y-m-d H:i:s' );
		$startDate = strtotime( $startDate );

		$currentDate = new DateTime( 'now' );
		$currentDate = $currentDate->format('Y-m-d H:i:s');
		$currentDate = strtotime( $currentDate );

		$subtractedDate = $currentDate - $startDate;

		$expiredTime = 200*60;

		if( $subtractedDate >= $expiredTime*60 ) {
			$stolenBikes++;
			$bike = $results[$i];
			try {
				$lostBike = new ParseObject( "LostBikes" );
				$lostBike->set( "bikeId" , $bike->getObjectId() );

				$user = $bike->get( "currentUser" );

				$user->fetch();

				$customerID = $user->get( "stripeID" );
				
				$amount = 200*100;

				$charge = \Stripe\Charge::create(array(
					'customer' => $customerID,
					'amount'   => $amount,
					'currency' => 'usd'
				));
				
				$lostBike->set( "currentUser" , $user );
				$lostBike->set( "lastLocation" , $bike->get( "lastLocation" ) );
				$lostBike->set( "condition" , $bike->get( "condition" ) );
				$lostBike->save();

				$bike->destroy();
			} catch ( \Parse\ParseException $ex ) {
				return 'Failed to create LostBikes object and destroy Bikes object ' . $ex->getMessage();
			}
			catch ( \Stripe\Error\InvalidRequest $e ) {
				return $e->getMessage();
			} catch ( \Stripe\Error\Authentication $e ) {
				return $e->getMessage();
			} catch ( \Stripe\Error\ApiConnection $e ) {
				return $e->getMessage();
			} catch ( \Stripe\Error\Base $e ) {
				return $e->getMessage();
			} catch ( Exception $e ) {
				return $e->getMessage();
			}

		}
	}

	$rString = 0;

	if($stolenBikes > 0){
		$rString = "Successfully removed " . $stolenBikes ." stolen bikes.";
	}

	return $rString;
}

function deleteField( $object , $field ) {
	$object->delete( $field );
	$object->save();
}

?>