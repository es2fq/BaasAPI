<!-- <li><a href="#user-content-post-getopenbikeswithindistance">POST /getOpenBikesWithinDistance</a></li>
<li><a href="#user-content-post-reserve">POST /reserve</a></li>
<li><a href="#user-content-post-signinuser">POST /signInUser</a></li>
<li><a href="#user-content-post-signupuser">POST /signUpUser</a></li>
<li><a href="#user-content-post-start">POST /start</a></li>
<li><a href="#user-content-post-stop">POST /stop</a></li>
<li><a href="#user-content-post-unlock">POST /unlock</a></li> -->

# **Baas API Documentation**

---

# Overview of API Calls
1. POST /cancelReservation
2. POST /getOpenBikesWithinDistance
3. POST /reserve
4. POST /signInUser
5. POST /signUpUser
6. POST /start
7. POST /stop
8. POST /unlock

---

# Details

## POST /cancelReservation

Cancels the reservation.

##### URL

https://api.baasbikes.com/dev/cancelReservation

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*						| string	 	| Email of the user reserving the bike																						|
| password\*					| string	 	| Password of the user reserving the bike																					|
| bikeID\*						| string	 	| ID for the specific bike																									|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---

## POST /getOpenBikesWithinDistance

Returns a list of all the bikes within a certain radius of the user. Each bike returned must be available to be reserved or rented. 

##### URL

https://api.baasbikes.com/dev/getOpenBikesWithinDistance

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| latitude\*					| string	 	| Latitude of the current user																								|
| longitude\*					| string	 	| Longitude of the current user																								|
| radius\*						| string	 	| Radius in miles																											|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| listOfBikes					| list		 	| List of bikes within the given radius. Each entity is a bikeID (string) and its latutude (double) and longitude (double)	|

```javascript
[ 
	{ 
		"bikeID": "c6bTeNrW58", 
		"latitude": 10, 
		"longitude": -50 
	}, 
	{ 
		"bikeID": "gHsXizqhXK", 
		"latitude": 10, 
		"longitude": -50 
	} 
]
```


---


## POST /reserve

Reserves a specific bike to the user. The bike must not have a current user attacked to it. The reservation time expires after 15 minutes.

##### URL

https://api.baasbikes.com/dev/reserve

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*						| string	 	| Email of the user reserving the bike																						|
| password\*					| string	 	| Password of the user reserving the bike																					|
| bikeID\*						| string	 	| ID for the specific bike																									|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---


## POST /signInUser

Signs in the user. The user must be in the system.

##### URL

https://api.baasbikes.com/dev/signInUser

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*						| string	 	| Email for the user 																										|
| password\*					| string	 	| Password for the user																										|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---


## POST /signUpUser

Signs up a user. The user must not already be in the system.

##### URL

https://api.baasbikes.com/dev/signUpUser

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*						| string	 	| Email for the user 																										|
| password\*					| string	 	| Password for the user																										|
| token\*						| string		| Stripe token																												|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---


## POST /start

Starts a session. In order for the session to start, the user currently must not have any bikes reserved or in use. 

##### URL

https://api.baasbikes.com/dev/start

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*	 					| string		| Email of the user beginning the session 																					|
| password\* 					| string		| Password of the user beginning the session																				|
| bikeID\*						| string	 	| ID for the specific bike																									|
| lattude\*						| string	 	| Latitude of the current user																								|
| longitude\*					| string	 	| Longitude of the current user																								|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---


## POST /stop

Stops the current session. The session is linked to the bike ID and must have been started by the same user.

##### URL

https://api.baasbikes.com/dev/stop

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| email\*						| string	 	| Email of the user ending the session																						|
| password\*					| string	 	| Password of the user ending the session																					|
| bikeID\*						| string	 	| ID of the bike to stop																									|
| latitude\*					| string	 	| Latitude of the current user																								|
| longitude\*					| string	 	| Longitude in miles																										|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| time							| Internal	 	| Return the session time in seconds	 																					|
| amount 						| Integer 		| Returns the cost of the session 																							|


##### Successful response
```javascript
[ 
	3600,
	200 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---


## POST /unlock

Sends the signal to unlock the bike.

##### URL

https://api.baasbikes.com/dev/unlock

#### Parameters
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| bikeID\*						| string	 	| ID of the bike to stop																									|
| latitude\*					| string	 	| Latitude of the current user																								|
| longitude\*					| string	 	| Longitude in miles																										|

#### Response
| Name							| Type			| Description																												|
| -----------------------------	| :-----------:	| ------------------------------------------------------------------------------------------------------------------------:	|
| status						| string	 	| Return success or failure				 																					|


##### Successful response
```javascript
[ 
	"success" 
]
```

##### Error codes
| Code 		| Name 					| Description 								|
| --------- | :-------------------: | ----------------------------------------: |
| 200		| OK 					| Request has succeeded 					|
| 400 		| Bad Request 			| The request, header, or URL is incorrect	|
| 403 		| Unauthorized 			| Authentication error 						|
| 404 		| Not Found 			| Invalid URL								|
| 405 		| Method Not Allowed	| Method is an invalid or unexpected type 	|
| 500		| Internal Server Error | Unexpected server error 					|

---
