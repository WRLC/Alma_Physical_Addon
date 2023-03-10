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
        md = ("&md=" .. GetFieldValue("Lender", "LibraryName")) or "&md="
        address1 = ("&address1=" .. GetFieldValue("Lender", "Address1")) or "&address1="
        address2 = ("&address2=" .. GetFieldValue("Lender", "Address2")) or "&address2="
        address3 = ("&address3=" .. GetFieldValue("Lender", "Address3")) or "&address3="
        address4 = ("&address4=" .. GetFieldValue("Lender", "Address4")) or "&address4="

	url = "" .. settings.SubmissionURL .. instCode .. usrId .. illiadCS .. tn .. itemId .. mmsId .. illNu .. borIn .. md .. address1 .. address2 .. address3 .. address4 .. regionalURL;
	
	PhysicalRequestForm.Browser:Navigate(url);
	
end