<?php
/*
 * support routines for consuming JSON APIs
 */

#
# initialize a cURL session and return the handle
#
function getCurlSession( $url=NULL ) {
    if ($url) {
        $ch = curl_init( $url );
    } else {
        $ch = curl_init( );
    }

    $mycurl = curl_version( );
    $userAgent = 'WRLC APIclient curl/'
                .$mycurl['version']
                .' (almaapiuser@wrlc.org)';
    curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    curl_setopt( $ch, CURLOPT_HEADER, TRUE );

    return $ch;
}

#
# send the request and return the http response code and json response body
#
function sendCurlRequest( $ch, $postfields=NULL ) {
    if ($postfields) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }

    $response = curl_exec( $ch );
    list ($hdr, $body) = explode( "\r\n\r\n", $response, 2 );

    # set the HTTP response code based on cURL error or API response code
    $errno = curl_errno( $ch );
    if ($errno) {
        $code = 500;
        $message = curl_error( $ch ) . " ($errno)";
        error_log( "cURL Error:  $message" );
        $body = '{"status":500,"error":"'. $message . '"}';
    } else {
        $code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    }

    return array ($code, $body);
}

#
# return a JSON error
#
# default parms for errors decoding BC3 response; override for encoding errors
function jsonError( $errcode, $status=502, $error='JSON decode error: ' ) {
    switch($errcode){
        case JSON_ERROR_DEPTH:
            $error .= 'Maximum depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            $error .= 'Invalid or malformed JSON';
        break;
        case JSON_ERROR_CTRL_CHAR:
            $error .= 'Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            $error .= 'Syntax error';
        break;
        case JSON_ERROR_UTF8:
             $error .= 'Malformed UTF-8 characters found';
        break;
        # the following error codes require PHP 5.5+
        case JSON_ERROR_RECURSION:
            $error .= 'One or more recursive references in the value to be encoded';
        break;
        case JSON_ERROR_INF_OR_NAN:
            $error .= 'One or more NAN or INF values in the value to be encoded';
        break;
        case JSON_ERROR_UNSUPPORTED_TYPE:
            $error .= 'A value of a type that cannot be encoded was given';
        break;
        default:
            $error .= 'Unknown error';
        break;
    }
    error_log( $error );
    return array ($status, "{\"status\":$status,\"error\":\"$error\"}\n");
}

?>
