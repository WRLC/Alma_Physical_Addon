<?php
// debug/error logging goes to web server error.log
//define('DEBUG_LOG', false);
define('DEBUG_LOG', true);

/**
 * Fetch a required environment variable safely.
 * Uses getenv() for reliability across Azure environments.
 * Throws immediately if missing or empty.
 */
function require_env(string $key): string {
    $val = getenv($key);
    if ($val === false || $val === '') {
        throw new RuntimeException("Missing required environment variable: {$key}");
    }
    return $val;
}

// settings for the production IZs:
$izSettings = array(
    // indexed by Alma Institution Code
    '01WRLC_AMU' => array(
        'name'   => 'American University',
        'apikey' => require_env('AU_API_KEY'),
        'loclib' => 'UNIV_LIB',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_CAA' => array(
        'name'   => 'Catholic University',
        'apikey' => require_env('CU_API_KEY'),
        'loclib' => 'CUMULLEN',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_DOC' => array(
        'name'   => 'University of the District of Columbia',
        'apikey' => require_env('DC_API_KEY'),
        'loclib' => 'DCVN',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_GAL' => array(
        'name'   => 'Gallaudet University',
        'apikey' => require_env('GA_API_KEY'),
        'loclib' => 'GALLAUDET',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_GML' => array(
        'name'   => 'George Mason University',
        'apikey' => require_env('GM_API_KEY'),
        'loclib' => 'FENWICK',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_GWA' => array(
        'name'   => 'George Washington University',
        'apikey' => require_env('GW_API_KEY'),
        'loclib' => 'gelman',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_GWAHLTH' => array(
        'name'   => 'GW Health Himmelfarb Library',
        'apikey' => require_env('GWHH_API_KEY'),
        'loclib' => 'GWAHLTH',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_GUNIV' => array(
        'name'   => 'Georgetown University',
        'apikey' => require_env('GT_API_KEY'),
        'loclib' => 'lau',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_HOW' => array(
        'name'   => 'Howard University',
        'apikey' => require_env('HU_API_KEY'),
        'loclib' => 'HUF',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
    '01WRLC_MAR' => array(
        'name'   => 'Marymount University',
        'apikey' => require_env('MU_API_KEY'),
        'loclib' => 'main',
        'locirc' => 'DEFAULT_CIRC_DESK',
    ),
);

$apiSettings = array(
    'defaultURL' => 'https://api-na.hosted.exlibrisgroup.com/',
);

// ILLiad Client Secret - should only be shared with authorized ILLiad clients
// from https://passwordsgenerator.net/ - 16 lowercase & number characters
define('ILLIAD_CLIENT_SECRET', require_env('ILLIAD_CLIENT_SECRET'));
?>
