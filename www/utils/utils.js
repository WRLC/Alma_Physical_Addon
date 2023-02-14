// COMPLETE
// Updates the page range fields after splitting the string
function updatePageRange(pageRange) {
	if (pageRange.length > 0) {
		var rangeArray = pageRange.split("-");
		$("#start").val(rangeArray[0]);
		$("#end").val(rangeArray[1]);
	}
}

// COMPLETE
// Validates Page Numbers (should only be numbers, is required)
function pageValidate(domId) {
	var regex = /^\d+$/;
	if (regex.test($(domId).val())) {
		$(domId+"Validation").text("");
		return true;
		} else if($(domId).val() == "") {
		$(domId+"Validation").text("*This field is required");
		return false;
		} else {
		$(domId+"Validation").text("*This field must be numerical");
		return false;
	}
}

// COMPLETE
// Checks to see if a form field is empty
function isEmpty(dom) {
	var domId = "#" + dom.attr('id');
	if (dom.val() == "") {
		$(domId+"Validation").text("*This field is required");
		return true;
		} else {
		$(domId+"Validation").text("");
		return false;
	}
}

// COMPLETE
// Provides data validation on form submit
function validateForm() {
	var alertText = "The following fields are invalid:\n";
	var issueCount = 0;

	$("input").each(function() {
		if ($(this).attr("id") == 'start') {
			if (pageValidate("#start") == false) {
				alertText += "\n\u2022 Start Page";
				issueCount++;
			}
		} else if ($(this).attr("id") == 'end') {
			if (pageValidate("#end") == false) {
				alertText += "\n\u2022 End Page";
				issueCount++;
			}
		} else if (isEmpty($(this))) {
			alertText += "\n\u2022 ";
			alertText += $(this).attr("name");
			issueCount++;
		}
	});
	
	alertText += "\n\n Please correct these errors and resubmit";
	console.log(issueCount);
	if (issueCount == 0) {
		$("#loadingDialog").dialog("open");
		return true;
		} else {
		alert(alertText);
		return false;
	}
}

// COMPLETE
// Adds Event Listeners to do automatic validation
function addEventListeners() {
	$("input").each(function() {
		if ($(this).attr("id") == 'start') {
			$(this).on("change", function() {pageValidate("#start")});
		} else if ($(this).attr("id") == 'end') {
			$(this).on("change", function() {pageValidate("#end")});
		} else if (isEmpty($(this))) {
			$(this).on("change", function() {isEmpty($(this))});
		}
	});
}

// TODO: Add constructors as needed
// Calls JQueryUI constructors
function buildUI() {
	$("#loadingDialog").dialog({
		dialogClass: "no-close",
		autoOpen: false
	});
	$("#progressbar").progressbar({
		value: false
	});
	$("#send").button({
	});
	$( "#requestForm" ).controlgroup({
		"direction": "vertical"
	});	
}		