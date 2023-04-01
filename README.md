
# Alma Physical Item Request Addon
This project provides an ILLiad Client Addon to submit a physical item request to an institution's Alma using the Users API.

## Addon Architecture
ILLiad client addons use the lua scripting language to embed forms and other functionality in the ILLiad client user interface. This addon creates a form that is prepopulated with the ILLiad Loan request data (e.g. Library, Address). Item identifier fields must be entered on the ILLiad form by the person doing the processing by looking the item up in Alma.  Using the Physical Items search is the easiest way, as both the MMS ID and Item ID show up on the record display.

Submitting the form in ILLiad will post the form fields to a PHP application running on a web server. The PHP application will create and post a call to the users/requests API to create the request in the institution's Alma IZ.

## Installing the ILLiad Client Addon
General instructions for Installing Addons are here:
https://atlas-sys.atlassian.net/wiki/spaces/ILLiadAddons/pages/3149384/Installing+Addons

### Download
Copy the [AlmaPhysicalRequest folder](https://github.com/WRLC/Alma_Physical_Addon/tree/master/AlmaPhysicalRequest) from this repo to the ILLiad addons folder: C:\program files\illiad\addons

### Configuration
Addon settings are configured in the ILLiad Client Manage Addons form.

| Setting | Default | Type | Description |
| ---- | ---- | ---- | ---- |
| AutoSearch | false | boolean | Determines whether or not the search should be done automatically when the request is opened. |
| InstitutionCode | 01WRLC_IZC | string | Institution Code for Alma. |
| regionalURL | https://api-na.hosted.exlibrisgroup.com/ | string | Alma API Regional URL. Sourced from table: https://developers.exlibrisgroup.com/alma/apis/#calling |
| SubmissionURL | https://illiad-apa.wrlc.org/index.php? | string | URL for website to handle request submissions (see web setup instructions) |
| UserId | IllUserPrimaryId | string | Alma Primary ID for ILL lending patron in Alma (the patron which will receive all of the requests). |
| IlliadClientSecret | sharedsecretwithwebserver | string | Credential for posting form to web server; keep this secret. |

## Web Setup Instructions

Clone this repository to your web server.
```
   git clone git@github.com:WRLC/Alma_Physical_Addon.git
```
Navigate to the `www/utils` directory and copy the example configuration file to `config.php`. Edit the `$izSettings` array for the IZs supported for your web server. Also get a random password from some place like passwordsgenerator.net and set `ILLIAD_CLIENT_SECRET` in `config.php` to that string; share the credential with your ILLiad clients for the IlliadClientSecret setting.
```
   cd www/utils
   cp config.php.example config.php
   vi config.php
```
Configure a named virtual host in your web server to serve up PHP files from the `Alma_Physical_Addon/www` directory. The resulting URL for that directory is the SubmissionURL setting for the ILLiad client addon.
