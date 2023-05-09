<?php
    require_once 'utils/config.php';
    require_once 'utils/jsonapi.php';

    // check shared secret credential
    if (isset($_POST["illiadCS"])) {
        if (ILLIAD_CLIENT_SECRET != $_POST["illiadCS"]) {
            http_response_code(403);
            exit;
        }
    } else {
        http_response_code(400);
        exit;
    }

    // Loads variables from form
    $instCode = $_POST["instCode"];
    $apiKey = $izSettings[$instCode]['apikey'];
    $loclib = $izSettings[$instCode]['loclib'];
    $locirc = $izSettings[$instCode]['locirc'];
    $usrId = $_POST["usrId"];
    $itemId = preg_replace("/[^0-9]/", "", $_POST["itemId"]); // Strips accidental non-numeric characters from itemId
    $mmsId = preg_replace("/[^0-9]/", "", $_POST["mmsId"]); // Strips accidental non-numeric characters from mmsId
    $tn = $_POST["tn"];
    if ($tn == '') {
        $tn = 'unknown';
    }
    $comment = "ILLiad TN: $tn; ";

    $borIn = $_POST["borIn"];
    if ($borIn == '') {
        $borIn = 'unknown';
    }
    $md = $_POST["md"];
    if ($md == '') {
        $md = 'unknown';
    }
    $address1 = $_POST["address1"];
    if ($address1 == '') {
        $address1 = 'unknown';
    }
    $address2 = $_POST["address2"];
    if ($address2 == '') {
        $address2 = 'unknown';
    }
    $address3 = $_POST["address3"];
    if ($address3 == '') {
        $address3 = 'unknown';
    }
    $address4 = $_POST["address4"];
    if ($address4 == '') {
        $address4 = 'unknown';
    }
    $illNu = $_POST["illNu"];
    if ($illNu == '') {
        $illNu = 'unknown';
    }

    $comment .= "BorCode: $borIn; ";
    $comment .= "Library: $md; ";
    $comment .= "A1: $address1; ";
    $comment .= "A2: $address2; ";
    $comment .= "A3: $address3; ";
    $comment .= "A4: $address4; ";
    $comment .= "ILL#: $illNu; ";

    $WRLCmails = $_POST["WRLCmails"];
    if ($WRLCmails == '') {
        $WRLCmails == 'unknown';
    }
    $comment .= "WRLCmails: $WRLCmails; ";

    $regionalURL = $_POST["regionalURL"];

    $emptyResponse = array (
        "request_id" => '',
        "title" => '',
    );

    $requestArray = array (
        'user_primary_id' => $usrId,
        'request_id' => '',
        'request_type' => 'HOLD',
        'request_sub_type' => array (
            'desc' => 'Patron physical item request',
	    'value' => 'PATRON_PHYSICAL',
        ),
        'item_id' => $itemId,
        'target_destination' => array (
            'value' => '',
        ),
        'partial_digitization' => false,
        'chapter_or_article_title' => '',
        'chapter_or_article_author' => '',
        'required_pages_range' => array (
            0 => array (
                'from_page' => '',
                'to_page' => '',
            ),
        ),
	'comment' => $comment,
	'copyrights_declaration_signed_by_patron' => false,
	'pickup_location_type' => 'LIBRARY',
	'pickup_location_library' => $loclib,
	'pickup_location_circulation_desk' => $locirc,
	'pickup_location_institution' => $instCode, // need to get code
    );
    $requestJSON = json_encode($requestArray);

    $fetch_url = $regionalURL . "almaws/v1/users/$usrId/requests?mms_id=$mmsId&item_pid=$itemId&apiKey=";
    DEBUG_LOG? error_log( "ADDON DEBUG: POST $fetch_url\n$requestJSON" ) : true;
    $fetch_url .= $apiKey;

    // Initialize cURL session
    $curl = getCurlSession( $fetch_url );
    // Set cURL options for request API
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
		'accept: application/json',
        'Content-Length: ' . strlen($requestJSON))
    );
    // execute cURL command to create request
#    $apiResponse = apiPost($apiKey,"$fetch_url&apiKey=$apiKey",$requestJSON);
    list ($code, $apiResponse) = sendCurlRequest( $curl, $requestJSON );
    DEBUG_LOG? error_log( "ADDON DEBUG: RESPONSE - $code\n$apiResponse" ) : true;

    // Close cURL session
    curl_close($curl);

    if ($code == 200) {
        // Success!
        $responseArray = json_decode($apiResponse, true);
        $error_rpt = '';
    } else {
        // Error response, log and report it
        $error_rpt = "<pre>\n**************** HTTP Error Response: $code ****************\n";
        if (substr( $apiResponse, 0,5 ) == '<?xml') {
            // XML is returned regardless for some errors, like invalid apiKey
            $errxml = simplexml_load_string($apiResponse);
            #var_dump( $errxml );
            if (isset($errxml->errorsExist) && $errxml->errorsExist == 'true') {
                foreach ($errxml->errorList->error as $error) {
                    $errmsg = $error->errorCode." - ".$error->errorMessage;
                    error_log( "ADDON ERROR: $errmsg" );
                    $error_rpt .= $errmsg."\n";
                }
            } else {
                error_log( "ADDON ERROR: API response $code\n$apiResponse" );
                $error_rpt .= "check web error log for messages\n";
            }
        } else if ($errjson = json_decode($apiResponse, true)) {
            // ToDo: handle data errors (like 401129) better
            //      401129 - No items can fulfill the submitted request.
            // https://developers.exlibrisgroup.com/alma/apis/docs/users/UE9TVCAvYWxtYXdzL3YxL3VzZXJzL3t1c2VyX2lkfS9yZXF1ZXN0cw==/#errorCodes
            if (isset($errjson["errorsExist"]) && $errjson["errorsExist"] == 'true') {
                foreach ($errjson["errorList"]["error"] as $error) {
                    $errmsg = $error["errorCode"]." - ".$error["errorMessage"];
                    error_log( "ADDON ERROR: $errmsg" );
                    $error_rpt .= $errmsg."\n";
                }
            } else {
                error_log( "ADDON ERROR: API response $code\n$apiResponse" );
                $error_rpt .= "check web error log for messages\n";
            }
        } else {
            list ($err, $msg) = jsonError( json_last_error() );
            error_log( "ADDON ERROR: JSON error $err $msg" );
            error_log( "ADDON ERROR: API response $code\n$apiResponse" );
            $error_rpt .= "check web error log for messages\n";
        }
        $error_rpt .= "**********************************************************";
        $error_rpt .= "        </pre>\n";
        $responseArray = $emptyResponse;
    }
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Physical Item Request Submitting</title>
		<script src="utils/utils.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="default.css">	
		<script>
			$( "#requestForm" ).controlgroup({
			"direction": "vertical",
			});
		</script>
	</head>
	<body>
        <?php echo $error_rpt; ?>
		<div class="widget" id="form" width="100%">
			<form method="POST" name="htmlForm" action="submitRequest.php" id="requestForm">
					
					<legend id="requestFormHeader" class="ui-widget-header">The following request has been submitted:</legend>
					<br>
					
					<label for="reqId">Request ID:</label>
					<input type="text" id="reqId" name="reqId" value="<?php echo $responseArray["request_id"]; ?>" class="text ui-widget-content ui-corner-all" readonly>
					<br><br>
					
					<label for="bTitle">Book Title:</label>
					<input type="text" id="bTitle" name="bTitle" value="<?php echo $responseArray["title"]; ?>" class="text ui-widget-content ui-corner-all" readonly>
					<br><br>
					
					<label for="comment">Comment:</label>
					<input type="text" id="comment" name="comment" value="<?php echo $responseArray["comment"]; ?>" class="text ui-widget-content ui-corner-all" readonly>
					<br><br>
					
			</form>
		</div>
	</body>
</html>
