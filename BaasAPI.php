 <?php
	require_once 'config.php';

	use Parse\ParseObject;
	use Parse\ParseGeoPoint;
	use Parse\ParseQuery;
	use Parse\ParseUser;

	class BaasAPI {
		function cancelReservation ( $email , $password , $bikeID ){
			$query = new ParseQuery( "Bikes" );
			try {
				$bike = $query->get( $bikeID );

				$user = $this->setCurrentUser( $email , $password , $bike );

				$userBikes = $this->getBikesWithUser( $user );
				$numBikes = count( $userBikes );

				if( $numBikes == 1 ){
					if( $userBikes[0] == $bikeID ) {
						$point = $bike->get( "lastLocation" );

						$date = new DateTime( 'now' );

						$this->logInteractions( $bike , $point, "CancelReservation" );

						$this->deleteField( $bike , "currentUser" );
						$this->deleteField( $bike , "reservedTime" );

						$bike->save();
					}
					else {
						return 'Cannot cancel the reservation. The bike is not reserved by the user.';
					}
				}
				else {
					return 'Cannot cancel the reservation.';
				}
			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
			return "success";
		}

		// Deletes field from object
		function deleteField( $object , $field ) {
			$object->delete( $field );
			$object->save();
		}

		// Returns a list of bike ids of bikes with a condition of less than $condition
		function getBikesWithConditionLessThan( $condition ) {
			$query = new ParseQuery( "Bikes" );
			$query->lessThan( "condition" , $condition );
			$query->limit( 1000 );
			$results = $query->find();
			$count = count( $results );

			$listOfBikes = array();
			for( $i = 0; $i < $count; $i++ ) {
				array_push( $listOfBikes , $results[$i]->getObjectId() );
			}

			return $listOfBikes;
		}

		// Returns all the bikes belonging to a user
		function getBikesWithUser( $user ) {
			$query = new ParseQuery( "Bikes" );
			$query->equalTo( "currentUser" , $user );
			$results = $query->find();

			$listOfBikes = array();
			for( $i = 0; $i < count( $results ); $i++ ) {
				array_push( $listOfBikes , $results[$i]->getObjectId() );
			}

			return $listOfBikes;
		}

		// Returns a list of usernames of bikes with current users
		function getCurrentUsers() {
			$query = new ParseQuery( "Bikes" );
			$query->includeKey( "currentUser" );
			$query->ascending( "currentUser" );
			$query->exists( "currentUser" );
			$query->limit( 1000 );
			$results = $query->find();
			$count = count( $results );

			$listOfCurrentUsers = array();
			for( $i = 0; $i < $count; $i++ ) {
				$name = $results[$i]->get( "currentUser" )->getUserName();
				if( !in_array( $name , $listOfCurrentUsers ) ) {
					array_push( $listOfCurrentUsers , $results[$i]->get( "currentUser" )->getUserName() );
				}
			}

			return $listOfCurrentUsers;
		}

		// Returns a list of bike ids of expired reserved bikes
		function getExpiredReservedBikes() {
			$query = new ParseQuery( "Bikes" );
			$query->exists( "reservedTime" );
			$query->limit( 1000 );
			$results = $query->find();

			$count = count( $results );

			$listOfBikes = array();
			for( $i = 0; $i < $count; $i++ ) {
				$reservedDate = $results[$i]->get( "reservedTime" );
				$reservedDate = $reservedDate->format('Y-m-d H:i:s');
				$reservedDate = strtotime( $reservedDate );

				$currentDate = new DateTime( 'now' );
				$currentDate = $currentDate->format('Y-m-d H:i:s');
				$currentDate = strtotime( $currentDate );

				$subtractedDate = $currentDate - $reservedDate;

				echo $subtractedDate;
				echo "s ";

				$expiredTime = 10;

				if( $subtractedDate >= $expiredTime*60 ) {
					array_push( $listOfBikes , $results[$i]->getObjectId() );
				}
			}

			return $listOfBikes;
		}

		// Returns a list of bike ids of bikes without a current user within a certain distance from a given point
		function getOpenBikesWithinDistance( $latitude , $longitude , $radius ) {
			$point = new ParseGeoPoint( $latitude , $longitude );

			$query = new ParseQuery( "Bikes" );
			$query->includeKey( "currentUser" );
			$query->doesNotExist( "currentUser" );
			$query->withinMiles( "lastLocation" , $point , $radius );
			$query->limit( 1000 );
			$results = $query->find();

			$count = count( $results );

			$listOfBikes = array();
			for( $i = 0; $i < $count; $i++ ) {
				$bike = $results[$i];
				$ID = $bike->getObjectId();
				$point = $bike->get( "lastLocation" );
				$lat = $point->getLatitude();
				$long = $point->getLongitude();
				
				$object = new stdClass();
				$object->bikeID = $ID;
				$object->latitude = $lat;
				$object->longitude = $long;
				
				array_push( $listOfBikes , $object );
			}

			return $listOfBikes;
		}

		// Puts interactions into the log
		function logInteractions( $bike , $location , $typeOfInteraction ) {
			$logObject = new ParseObject( "Log" );

			$logObject->set( "bike" , $bike );
			$logObject->set( "location" , $location );
			$logObject->set( "typeOfInteraction" , $typeOfInteraction );
			$logObject->set( "user" , $bike->get( "currentUser" ) );
			
			try {
				$logObject->save();
			} catch ( \Parse\ParseException $ex ) {
				return 'Failed to create  new object, with error message: ' . $ex->getMessage();
			}
		}

		// Sets the current user and sets the reserve time to the current time
		function reserve( $email , $password , $bikeID ) {
			// Check if user does not have a bike already
			$query = new ParseQuery( "Bikes" );
			try {
				$bike = $query->get( $bikeID );
				$user = $this->setCurrentUser( $email , $password , $bike );
				
				$userBikes = $this->getBikesWithUser( $user );
				$numBikes = count( $userBikes );

				if( $numBikes == 0 ) {

					$reserved = $bike->get( "reservedTime" );
					if( isset( $reserved ) == FALSE ) {
						$date = new DateTime( 'now' );
						$bike->set( "reservedTime", $date );
						$bike->save();

						$this->logInteractions( $bike , $bike->get( "lastLocation" ) , "ReserveBike" );
					}
					else {
						return 'Bike cannot be reserved. The bike is already reserved.';
					}
				}
				else {
					return 'Bike cannot be reserved. The user already is using or has reserved another bike.';
				}

			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
			return "success";
		}

		// Sets the condition of the bike
		function setCondition( $bikeID , $condition ) {
			$query = new ParseQuery( "Bikes" );
			try {
				$bike = $query->get( $bikeID );
				$bike->set( "condition" , $condition );
				$bike->save();
			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
		}

		// Sets the current user and returns a pointer to the user
		function setCurrentUser( $email , $password , $bike ) {
			try {
				$user = ParseUser::logIn( $email , $password );
			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
			$bike->set( "currentUser" , ParseUser::getCurrentUser() );

			return $user;
		}

		// Logs in the user
		function signInUser( $email , $password ) {
			try {
				$user = ParseUser::logIn( $email , $password );
			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
			return "success";
		}

		// Signs up the user
		function signUpUser( $email , $password , $token, $phone = "301-828-8684" ) {
			$user = new ParseUser();
			$user->set( "username" , $email );
			$user->set( "password" , $password );
			$user->set( "email" , $email );
			$user->set( "phone" , $phone );


			// Parse try-catch
			try {
				$user->signUp();

				// Stripe try-catch
				try {
					$customer = \Stripe\Customer::create( array(
						'email' => $email,
						'card'  => $token
					));

					$user->set( "stripeID" , $customer->id );
					$user->save();					
				} catch ( \Stripe\Error\InvalidRequest $e ) {
					$user->destroy();
					return $e->getMessage();
				} catch ( \Stripe\Error\Authentication $e ) {
					$user->destroy();
					return $e->getMessage();
				} catch ( \Stripe\Error\ApiConnection $e ) {
					$user->destroy();
					return $e->getMessage();
				} catch ( \Stripe\Error\Base $e ) {
					$user->destroy();
					return $e->getMessage();
				} catch ( Exception $e ) {
					$user->destroy();
					return $e->getMessage();
				}


			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}		
			return "success";
		}

		// Starts the session
		function start( $email , $password , $bikeID , $latitude , $longitude ) {
			$query = new ParseQuery( "Bikes" );
			try {
				$bike = $query->get( $bikeID );

				$user = $this->setCurrentUser( $email , $password , $bike );

				$userBikes = $this->getBikesWithUser( $user );
				$numBikes = count( $userBikes );

				if( $numBikes == 0 ) {
					$point = new ParseGeoPoint( $latitude , $longitude );
					$bike->set( "lastLocation" , $point );

					$date = new DateTime( 'now' );
					$bike->set( "sessionStartTime" , $date );

					$this->deleteField( $bike , "reservedTime" );

					$bike->save();

					$this->logInteractions( $bike , $point, "StartSession" );
				}
				else if( $numBikes == 1 ){
					if( $userBikes[0] == $bikeID ) {
						$point = new ParseGeoPoint( $latitude , $longitude );
						$bike->set( "lastLocation" , $point );

						$date = new DateTime( 'now' );
						$bike->set( "sessionStartTime" , $date );

						$this->deleteField( $bike , "reservedTime" );

						$bike->save();

						$this->logInteractions( $bike , $point, "StartSession" );
					}
					else {
						return 'Cannot start the session. Another session is in progress.';
					}
				}
				else {
					return 'Cannot start the session. Another session is in progress.';
				}
			} catch( \Parse\ParseException $ex ) {
				return $ex->getMessage();
			}
			return "success";
		}

		// Stops the session
		function stop( $email , $password , $bikeID , $latitude , $longitude ) {
			$query = new ParseQuery( "Bikes" );
			$bike = $query->get( $bikeID );
			$user = $this->setCurrentUser( $email , $password , $bike );

			$userBikes = $this->getBikesWithUser( $user );

			if( in_array( $bikeID , $userBikes ) == TRUE ) {
				// Houry rate in cents
				$hourlyRate = 100;

				// Setting log information
				$query = new ParseQuery( "Bikes" );
				$bike = $query->get( $bikeID );

				$point = new ParseGeoPoint( $latitude , $longitude );
				$bike->set( "lastLocation" , $point );

				// Calculating payment
				$reservedDate = $bike->get( "sessionStartTime" );
				$reservedDate = $reservedDate->format( 'Y-m-d H:i:s' );
				$reservedDate = strtotime( $reservedDate );

				$currentDate = new DateTime( 'now' );
				$currentDate = $currentDate->format( 'Y-m-d H:i:s' );
				$currentDate = strtotime( $currentDate );

				$subtractedDate = $currentDate - $reservedDate;
				$hours = $subtractedDate/3600;

				$additionalCost = $hours * $hourlyRate;

				$amount = 100 + intval( $additionalCost );

				$customerID = $user->get( "stripeID" );

				try {
					$charge = \Stripe\Charge::create( array(
						'customer' => $customerID,
						'amount'   => $amount,
						'currency' => 'usd'
					));
				} catch ( \Stripe\Error\InvalidRequest $e ) {
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


				// Log the end of session
				try {
					$this->logInteractions( $bike , $point, "EndSession" );

					$this->deleteField( $bike , "currentUser" );
					$this->deleteField( $bike , "reservedTime" );
					$this->deleteField( $bike , "sessionStartTime" );
				} catch ( \Parse\ParseException $ex ) {
					return 'Failed to log or delete fields';
				}
				return array( $subtractedDate , $amount );
			}
			else {
				return "Session cannot be ended. The user does not have access to the bike.";
			}
		}

		// Unlocks the bluetooth lock
		function unlock( $bikeID , $latitude , $longitude ) {
			$query = new ParseQuery( "Bikes" );
			
			$bikeObject = $query->get( $bikeID );

			$this->logInteractions( $bikeObject , new ParseGeoPoint( $latitude , $longitude ) , "UnlockBike" );
			return "success";
		}

	}

	// $api = new BaasAPI();

	// $myName = "Erik3";
	// $myPass = "Shicheng";
	// $testBikeID = "o5I3Ywhkxm";
	// $myLatitude = 40.0;
	// $myLongitude = -30.0;

	// $myEmail = "happystreet12345@gmail.com";
	// $myPhone = "123-456-7890";
	// $api->signUpUser( $myName , $myPass , $myEmail , $myPhone );

	// $api->start( $myName , $myPass , $testBikeID , $myLatitude , $myLongitude );
	// $api->reserve( $myName , $myPass , $testBikeID );
	// $api->testGrabbers();
	// $api->setCondition( $testBikeID , 2 );
	// $api->stop( $testBikeID , $myLatitude , $myLongitude );
?>
