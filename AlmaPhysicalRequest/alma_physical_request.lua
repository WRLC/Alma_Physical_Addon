-- About alma_physical_request.lua
--
-- Author: K Kilduff, kilduff@wrlc.org
-- alma_physical_request.lua is used for submitting physical item requests to Alma using the Users and Fulfillment API
--
-- set autoSearch to true for this script to automatically run the search when the request is opened.

local settings = {};
settings.autoSearch = GetSetting("AutoSearch");
settings.SubmissionURL = GetSetting("SubmissionURL");
settings.InstitutionCode = GetSetting("InstitutionCode");
settings.UserId = GetSetting("UserId");
settings.regionalURL = GetSetting("RegionalURL");
settings.IlliadClientSecret = GetSetting("IlliadClientSecret");

local interfaceMngr = nil;
local PhysicalRequestForm = {};
PhysicalRequestForm.Form = nil;
PhysicalRequestForm.Browser = nil;
PhysicalRequestForm.RibbonPage = nil;

function Init()
    
	if GetFieldValue("Transaction", "RequestType") == "Loan" then
		
		interfaceMngr = GetInterfaceManager();
		
		PhysicalRequestForm.Form = interfaceMngr:CreateForm("WRLC Physical Request", "Script");
		
		PhysicalRequestForm.Browser = PhysicalRequestForm.Form:CreateBrowser("WRLC Physical Request", "WRLC Physical Request", "WRLC Physical Request");
		
		PhysicalRequestForm.Browser.TextVisible = false;
		
		PhysicalRequestForm.Browser.WebBrowser.ScriptErrorsSuppressed = true;
		
		PhysicalRequestForm.RibbonPage = PhysicalRequestForm.Form:GetRibbonPage("WRLC Physical Request");
		
		PhysicalRequestForm.RibbonPage:CreateButton("Load Request Page", GetClientImage("Search32"), "LoadRequestPage", "WRLC Physical Request");
		
		PhysicalRequestForm.Form:Show();
		
		if settings.autoSearch then
			LoadRequestPage();
		end
	end
end

function NewURL()
	PhysicalRequestForm.Browser:Navigate(settings.FriendsURL);
	
end

function LoadRequestPage()
	instCode =  "instCode=" .. settings.InstitutionCode;
	usrId = "&usrId=" .. settings.UserId;
	illiadCS = "&illiadCS=" .. settings.IlliadClientSecret;
	regionalURL = "&regionalURL=" .. settings.regionalURL;
	
	tn = ("&tn=" .. GetFieldValue("Transaction", "TransactionNumber")) or "&tn="
	itemId = ("&itemId=" .. GetFieldValue("Transaction", "ReferenceNumber")) or "&itemId="
	mmsId = ("&mmsId=" .. GetFieldValue("Transaction", "CallNumber")) or "&mmsId="
	illNu = ("&illNu=" .. GetFieldValue("Transaction", "ILLNumber")) or "&illNu="
	borIn = ("&borIn=" .. GetFieldValue("Transaction", "LendingLibrary")) or "&borIn="

    if(GetFieldValue("Lender", "LibraryName"))
    then
	    md = ("&md=" .. string.gsub((GetFieldValue("Lender", "LibraryName")), "%&", " and")) or "&md="
    else
		md = "&md="
	end
	
    if(GetFieldValue("Lender", "Address1"))
    then
	    address1 = ("&address1=" .. string.gsub((GetFieldValue("Lender", "Address1")), "%&", " and")) or "&address1="
    else
    	address1 = "&address1="
	end

    if(GetFieldValue("Lender", "Address2"))
    then
	    address2 = ("&address2=" .. string.gsub((GetFieldValue("Lender", "Address2")), "%&", " and")) or "&address2="
    else
    	address2 = "&address2="
	end

    if(GetFieldValue("Lender", "Address3"))
    then
	    address3 = ("&address3=" .. string.gsub((GetFieldValue("Lender", "Address3")), "%&", " and")) or "&address3="
    else
    	address3 = "&address3="
	end

    if(GetFieldValue("Lender", "Address4"))
    then
	    address4 = ("&address4=" .. string.gsub((GetFieldValue("Lender", "Address4")), "%&", " and")) or "&address4="
    else
    	address4 = "&address4="
	end


	url = "" .. settings.SubmissionURL .. instCode .. usrId .. illiadCS .. tn .. itemId .. mmsId .. illNu .. borIn .. md .. address1 .. address2 .. address3 .. address4 .. regionalURL;
	
	PhysicalRequestForm.Browser:Navigate(url);
	
end
