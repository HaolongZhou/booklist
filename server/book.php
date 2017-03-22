<?php

    function object2array(&$object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }

	require_once 'db.config.php';
	require_once 'medoo.class.php';
    require_once 'Booklist.class.php';
	//Decode received JSON data
	$data = file_get_contents("php://input");
	
	
	$receivedData = json_decode($data);
 //   $receivedData = object2array($receivedData);
	//print_r($receivedData->{"bookname"});

	$book = new Booklist($receivedData);
	
    if(isset($_GET['a'])){
        switch ($_GET['a']){
            case 'booklist':
                $booklist = $book->getBooklist();				
                echo json_encode($book->getBookAllList($booklist));
                break;
            case 'addborrow':
               if(isset($receivedData->{'bookid'})){
                   if(!isset($receivedData->{"borrow"}->{"borrowname"}) || !$receivedData->{"borrow"}->{"borrowtime"}){
                       break;
                   }
                   if($book->updateBookstatusByid($receivedData->{'bookid'},$receivedData->{'bookstatus'})){
                     //  print_r($receivedData);
                     //  $receivedData["borrow"]["borrowtime"] = time() * 1000;
					   $book->setBorrowtime(time() * 1000);
                       $res = $book->insertBorrower();
                       echo json_encode(array('borrowid'=>$res));
                   }else{
                       echo json_encode(array('borrowid'=>0));
                   }
               }
                break;
            case 'removeborrow':
                if($book->getBookid()) {
                    if ($book->updateBookstatusByid($book->getBookid(), $book->getBookstatus())) {
						$book->setReturntime(time() * 1000);
                        $res = $book->returnBorrower();
                        echo json_encode(array('res' => $res));
                    }else{
                        echo json_encode(array('res' => 0));
                    }
                }

                break;
            case 'addbook':
                if(isset($receivedData->{"booknum"})) {
                    $res = $book->addBook();
                    echo json_encode(array('bookid'=>$res));
                }else{
                    echo json_encode(array('bookid'=>0));
                }
                break;

            case 'delbook':
                if(isset($receivedData->{"bookid"})) {
                    $res = $book->delBookByid($receivedData->{"bookid"});
                    echo json_encode(array('res'=>$res));
                }else{
                    echo json_encode(array('res'=>false));
                }
                break;
            default:
                break;
        }
    }



/*
	die();





	$user = new User_Components();
	$session = new Session();

	if(isset($receivedData->{"type"})){
		$response = '';
		switch ($receivedData->{"type"}) {

		    case 'registerUser':
		    	if(isset($receivedData->{"userName"}) && 
		    		isset($receivedData->{"firstName"}) &&
		    		isset($receivedData->{"lastName"}) &&
		    		isset($receivedData->{"email"}) &&
		    		isset($receivedData->{"dob"}) &&
		    		isset($receivedData->{"password"}) &&
		    		isset($receivedData->{"verifyPassword"})){
		    		
		    		$userName 	= $receivedData->{"userName"};
		    		$firstName 	= $receivedData->{"firstName"};
		    		$lastName 	= $receivedData->{"lastName"};
		    		$email 		= $receivedData->{"email"};
		    		$dob 		= $receivedData->{"dob"};
		    		$password   = $receivedData->{"password"};

		    		if(strcmp($receivedData->{"password"}, $receivedData->{"verifyPassword"}) == 0){
			        	
			        	$res = $user->registerUserDetails($userName, $firstName, $lastName, $password, $email, $dob);

			        	if($res["status"] == 0)
			        	    $response = array("status" => 0,
			        	                      "message"=> $res["message"]);
			        	else
			        	    $response = array("status" => 1,
			        	                      "message"=> $res["message"]);
			        }
			        else{
			        	$response = array("status" => 1,
	                      "message"=> "Password mismatch");	
			        }
		        }
		        else{
		        	$response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
		        }
		        echo json_encode($response);
		    break;

		    case 'verifyUserLogin':
		    	if(isset($receivedData->{"userName"}) && 
		    		isset($receivedData->{"password"})){

		    		$userName 	= $receivedData->{"userName"};
		    		$password   = $receivedData->{"password"};

		    		$res = $user->verifyUserLogin($userName, $password);

		    		if($res["status"] == 0)
		    		    $response = array("status" => 0,
		    		                      "message"=> $res["message"]);
		    		else
		    		    $response = array("status" => 1,
		    		                      "message"=> $res["message"]);

		    	}
		        else{
		        	$response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
		        }
		        echo json_encode($response);

		    break;

		    case 'checkValidSession':
		    	if(isset($receivedData->{"sessionId"})){
		    		$res = $session->isValidSession($receivedData->{"sessionId"});

		    		if($res["status"] == 0){
		    			$response = array("status" => 0,
	                      				   "message"=> "Valid Session");	
		    		}
		    		else{
		    			$response = array("status" => 1,
	                      				   "message"=> "Invalid Session");	
		    		}
		    	}	
		    	else{
		        	$response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
		        }
		        echo json_encode($response);
		    break;

		    case 'logOffUser':

		    	session_unset();     // unset $_SESSION variable for the run-time 
		    	session_destroy();   // destroy session data in storage
		    	
		    	$response = array("status" => 0,
		    		              "message"=> "logged out");
		    	
		    	echo json_encode($response);
		    break;

		    case 'forgotpassword':
		    	if(isset($receivedData->{"email"})){
		    		$res = $user->forgotpassword($receivedData->{"email"});

		    		if($res["status"] == 0){
		    			$response = array("status" => 0,
	                      				   "message"=> "Reset Email sent");	
		    		}
		    		else{
		    			$response = array("status" => 1,
	                      				   "message"=> $res["message"]);	
		    		}
		    	}
		    	else{
		        	$response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
		        }
		        echo json_encode($response);
		    break;

		    case 'updatepassword':
		    	if(isset($receivedData->{"email"}) && 
		    		isset($receivedData->{"token"}) &&
		    		isset($receivedData->{"password"}) &&
		    		isset($receivedData->{"verifypassword"})) {
		    		
		    		$email 		= $receivedData->{"email"};
		    		$token 		= $receivedData->{"token"};
		    		$password   = $receivedData->{"password"};
		    		$verifypassword   = $receivedData->{"verifypassword"};

		    		if(strcmp($receivedData->{"password"}, $receivedData->{"verifypassword"}) == 0){
			        	
			        	$res = $user->updatePasswordResetDetails($email, $token, $password);

			        	if($res["status"] == 0)
			        	    $response = array("status" => 0,
			        	                      "message"=> $res["message"]);
			        	else
			        	    $response = array("status" => 1,
			        	                      "message"=> $res["message"]);
			        }
			        else{
			        	$response = array("status" => 1,
	                      "message"=> "Password mismatch");	
			        }
		        }
		        else{
		        	$response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
		        }
		        echo json_encode($response);
		    break;

		}
	}
	else {

	    $response = array("status" => 1,
	                      "message"=> "All fields needs to be set");
	    echo json_encode($response);
	}
*/
