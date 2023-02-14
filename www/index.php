<?php
	require_once 'utils/config.php';

    if (isset($_GET["illiadCS"])) {
        if (ILLIAD_CLIENT_SECRET != $_GET["illiadCS"]) {
            http_response_code(403);
            exit;
        }
    } else {
        http_response_code(400);
        exit;
    }

    if (isset($_GET["instCode"])) {
        $instCode = $_GET["instCode"];
        $instName = $izSettings[$instCode]['name'];
    } else {
        http_response_code(400);
        exit;
    }

    if (isset($_GET["usrId"])) {
        $usrId = $_GET["usrId"];
    } else {
        http_response_code(400);
        exit;
    }
    if (isset($_GET["tn"])) {
        $tn = $_GET["tn"];
    } else {
        $tn = '';
    }

    if (isset($_GET["illNu"])) {
        $illNu = $_GET["illNu"];
    } else {
        $illNu  = '';
    }

    if (isset($_GET["borIn"])) {
        $borIn = $_GET["borIn"];
    } else {
        $borIn = '';
    }
    if (isset($_GET["md"])) {
        $md = $_GET["md"];
    } else {
        $md = '';
    }
    if (isset($_GET["address1"])) {
        $address1 = $_GET["address1"];
    } else {
        $address1 = '';
    }

    if (isset($_GET["address2"])) {
        $address2 = $_GET["address2"];
    } else {
        $address2 = '';
    }

        if (isset($_GET["address3"])) {
        $address3 = $_GET["address3"];
    } else {
        $address3 = '';
    }

        if (isset($_GET["address4"])) {
        $address4 = $_GET["address4"];
    } else {
        $address4 = '';
    }


    if (isset($_GET["itemId"])) {
        // Strips accidental non-numeric characters from itemId
        $itemId = preg_replace("/[^0-9]/", "", $_GET["itemId"]);
    } else {
        $itemId = '';
    }

    if (isset($_GET["mmsId"])) {
        // Strips accidental non-numeric characters from mmsId
        $mmsId = preg_replace("/[^0-9]/", "", $_GET["mmsId"]);
    } else {
        $mmsId = '';
    }

    if (isset($_GET["regionalURL"])) {
        $regionalURL = $_GET["regionalURL"];
    } else {
        $regionalURL = $apiSettings["defaultURL"];
    }
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Physical Item Request Submitting</title>
		<script src="utils/utils.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="default.css">
		<script>
			$( function() {
			buildUI();
			addEventListeners();
			});
		</script>
	</head>

	<body>

		<div class="widget" id="form" width="100%">
			<form method="POST" name="htmlForm" action="submitPhysicalRequest.php" onsubmit="return validateForm()" id="requestForm">

					<legend id="requestFormHeader" class="ui-widget-header">Physical Item Request Form</legend>

					<label for="instCode">Institution Code:</label>
					<input type="text" id="instCode" name="instCode" value="<?php echo $instCode; ?>" class="text ui-widget-content ui-corner-all" readonly>
					<span class="error" id="instCodeValidation"></span>
					<br><br>

					<label for="instName">Institution Name:</label>
					<input type="text" id="instName" name="instName" value="<?php echo $instName; ?>" class="text ui-widget-content ui-corner-all" readonly>
					<span class="error" id="instNameValidation"></span>
					<br><br>

					<label for="usrId">User ID:</label>
					<input type="text" id="usrId" name="usrId" class="text ui-widget-content ui-corner-all" value="<?php echo $usrId; ?>" readonly>
					<span class="error" id="usrIdValidation"></span>
					<br><br>

					<label for="tn">ILLiad TN:</label>
					<input type="text" id="tn" name="tn" value="<?php echo $tn; ?>" class="text ui-widget-content ui-corner-all">
					<span class="error" id="usrIdValidation"></span>
					<br><br>


                                        <label for="illNu">ILL Number:</label>
                                        <input type="text" id="illNu" name="illNu" value="<?php echo $illNu; ?>" class="text ui-widget-content ui-corner-all">
                                        <span class="error" id="usrIdValidation"></span>
                                        <br><br>



					<label for="borIn">Library Code:</label>
					<input type="text" id="borIn" name="borIn" value="<?php echo $borIn; ?>" class="text ui-widget-content ui-corner-all">
					<span class="error" id="instNameValidation"></span>
					<br><br>

					<label for="md">Library Name:</label>
					<input type="text" id="md" name="md" value="<?php echo $md; ?>" class="text ui-widget-content ui-corner-all">
					<span class="error" id="usrIdValidation"></span>
					<br><br>

            <label for="address1">Address L1:</label>
                <input type="text" id="address1" name="address1" value="<?php echo $address1; ?>" class="text ui-widget-content ui-corner-all">
                <span class="error" id="usrIdValidation"></span>
            <br><br>

            <label for="address2">Address L2:</label>
                <input type="text" id="address2" name="address2" value="<?php echo $address2; ?>" class="text ui-widget-content ui-corner-all">
                <span class="error" id="usrIdValidation"></span>
            <br><br>

            <label for="address3">Address L3:</label>
                <input type="text" id="address3" name="address3" value="<?php echo $address3; ?>" class="text ui-widget-content ui-corner-all">
                <span class="error" id="usrIdValidation"></span>
            <br><br>

            <label for="address4">Address L4:</label>
                <input type="text" id="address4" name="address4" value="<?php echo $address4; ?>" class="text ui-widget-content ui-corner-all">
                <span class="error" id="usrIdValidation"></span>
            <br><br>

					<label for="itemId">Item ID:</label>
					<input type="text" id="itemId" name="itemId" value="<?php echo $itemId; ?>" class="text ui-widget-content ui-corner-all">
					<span class="error" id="itemIdValidation"></span>
					<br><br>

					<label for="mmsId">MMS ID:</label>
					<input type="text" id="mmsId" name="mmsId" value="<?php echo $mmsId; ?>" class="text ui-widget-content ui-corner-all">
					<span class="error" id="mmsIdValidation"></span>
					<br><br>

					<input id="regionalURL" name="regionalURL" type="hidden" value="<?php echo $regionalURL ?>">
					<input id="illiadCS" name="illiadCS" type="hidden" value="<?php echo ILLIAD_CLIENT_SECRET ?>">
				<button id="send">Submit Request</button>

			</form>
		</div>

		<div id="loadingDialog" title="Submitting Request" hidden>
			<p>Request submitting to <?php echo $instName; ?>'s Alma</p>
			<p><div id="progressbar"></div></p>
		</div>

		<div id="contentHolder"></div>
	</body>
</html>	
