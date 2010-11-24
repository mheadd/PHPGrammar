<?php

// Include the Tropo PHP WebAPI library.
require('path/to/TropoClasses.php');

// Include the Limonade Framework.
require('path/to/limonade/lib/limonade.php');

// Set up variables holding path to script and external grammar file.
$hostURL = 'http://'.$_SERVER['SERVER_NAME'];
$scriptURL = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

// The starting point for our caller.
dispatch_post('/start', 'menu_start');
function menu_start() {
	
	GLOBAL $hostURL, $scriptURL;
	
	// Create a new instance of the Tropo object.
	$tropo = new Tropo();
	
	// A greeting prompt.
	$tropo->say("Welcome to the Tropo speech enabled phone menu example.");
	
	// Set up options form menu, including SRGS grammar to use.
	$options = array("attempts" => 3, 
					 "bargein" => true, 
					 "choices" => "$hostURL/SampleGrammar.php;type=application/grammar-xml", 
					 "name" => "phone", 
					 "timeout" => 5);
	
	// Ask the caller to say the name of the person they want to be transferred to.
	$tropo->ask("Say the name of the person you want to be transferred to.", $options);
	
	// Set up a handler for the continue event, which is delivered when the caller selection successfully matches the grammar.
	$tropo->on(array("event" => 
					 "continue", 
					 "next" => "$scriptURL?uri=transfer", 
					 "say" => "Transferring. Please hold..."));
	
	// Write out the JSON for Tropo to consume.
	$tropo->RenderJson();
	
}

// After the caller makes a selection, transfer the call.
dispatch_post('/transfer', 'transfer_call');
function transfer_call() {
	
	// Create a new instance of the Result object and get the value of the selection the caller made.
	$result = new Result();
	$phone = $result->getValue();
	
	// Create a new instance of the Tropo object and transfer the call.
	$tropo = new Tropo();
	$tropo->transfer('+1'.$phone);
	
	// Write out the JSON for Tropo to consume.
	$tropo->RenderJson();	
	
}

run();

?>