<?php

/* Before we Assign a value for the variable name,
 we chek how many index in the $argv:
 */   

   

    try{
    if (!isset($argv[1])) {
        throw new Exception("You didn't enter the username.\n");
        } else {
            // Syntax to be tested
            $name = $argv[1];
            echo "Hello, $name!\n";
            // Github API URL 
            $github_url = "https://api.github.com/users/$name/events";

            // Defining HTTP Headers:
            $options = [
            
            "http" => [

                'ignore_errors' => true,
                "header" => "User-Agent: PHP\r\n"

                ], 
            ];
            // Creating Stream & Sending The HTTP Request: 
            $context = stream_context_create($options);
            $sending_request= file_get_contents( $github_url,  false , $context);
            }
            
 
}catch (Exception $e){
    die("Caught Exception: " . $e ->   getMessage(). "\n");
}

/* 
--- 
--  Handling the HTTP Response:
---
*/

// This Line Means $statusCode = $http_response_header[0] otherwise empty string
$statusLine = $http_response_header[0] ?? ''; 
preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
$statusCode = $match[1] ?? null;

// var_dump( $statusCode);

try {

if ($statusCode === "404"){
 throw new Exception("this username not Found! -_- " . PHP_EOL);
// echo "mew";

}elseif($statusCode === "500"){
    throw new Exception("Somthing happen even me I don't know ;)");

} else {
    // echo "mewoooo";
    
//  Display Block:
$data = json_decode($sending_request, true);
foreach($data as $event){

    echo $event['type'] . "\t at \t" . $event["repo"]["name"]. "\t in \t" . $event["created_at"] . PHP_EOL;
    // echo $event['type'] . " at " . $event['repo']['name'] . PHP_EOL;

    
    }
}

}catch(Exception $e){
    die("Find an Exception: ". $e -> getMessage() . PHP_EOL);
}





// print_r($http_response_header[0]);












?>
