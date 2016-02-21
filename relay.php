<?php
header('Content-Type: application/json');

// Get the data from NationBuilder

$raw = file_get_contents('php://input');
$data = json_decode($raw,true);

// Parse the data

$person = $data['payload']['person'];
$tags = $person['tags'];

// Only do any of this if the person is tagged properly,
// otherwise every single signup on NationBuilder will get the PDF.
// Update your selected tag accordingly

if (in_array("YOUR TAG HERE",$tags)) {

	// Prepare the relay for Webmerge. Property names are used
	// in the Field Map, e.g. FirstName = {$FirstName}

	$relay = array(
		"FirstName" => $person['first_name'],
		"LastName" 	=> $person['last_name'],
		"City" 		=> $person['primary_address']['city'],
		"State" 	=> $person['primary_address']['state'],
		"Address" 	=> $person['primary_address']['address1']." ".$person['primary_address']['address2'],
		"Zip" 		=> $person['primary_address']['zip'],
		"Email" 	=> $person['email1']
		);

	// Include the county in the relay if the signup has one;
	// they probably won't because NB sucks at counties

	if ( $person['primary_address']['county'] ) {
		$relay['County'] = $person['primary_address']['county'];
	}

	// Rearrange birthdate month/day/year to match the PDF's requirements.
	// NationBuilder sends it in YYYY-MM-DD whereas most government documents
	// want MM/DD/YYYY

	if ($person['birthdate']) {
		$rawDOB = explode("-", $person['birthdate']);
		$formattedDOB = $rawDOB[1]."/".$rawDOB[2]."/".$rawDOB[0];
		$relay['DOB'] = $formattedDOB;
	}

	// JSON-encode the relay

	$json = json_encode($relay);

	// Writing the relay to a file can be handy for testing

	$fp = fopen('data.json', 'w');
	fwrite($fp, $json);
	fclose($fp);

	// Replace these with your Document/Key

	$document_id = 'DOCUMENT ID';
	$api_key = 'API KEY';

	// Only turn off test mode when you are absolutely sure
	// you're good to go; those merges aren't cheap

	$test_mode = true;

	if ( $test_mode ) {
		$webmerge = 'https://www.webmerge.me/merge/'.$document_id.'/'.$api_key.'?test=1';
	} else {
		$webmerge = 'https://www.webmerge.me/merge/'.$document_id.'/'.$api_key;
	}

	// Send relay to Webmerge with cURL

	$ch = curl_init($webmerge);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch, CURLOPT_TIMEOUT, '10'); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array("Content-Type: application/json","Accept: application/json"));

	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	$response = curl_exec($ch);
	curl_close($ch);

}

// * NAILED IT

?>