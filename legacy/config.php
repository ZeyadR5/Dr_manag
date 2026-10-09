<?php
// Orthanc Configuration
$orthancUrl  = "http://localhost:8042";
$orthancUser = getenv('ORTHANC_USER') ?: '';
$orthancPass = getenv('ORTHANC_PASS') ?: '';

/**
 * Creates a stream context for file_get_contents with Basic Auth and JSON support
 * @param string $method HTTP method (GET, POST, etc.)
 * @param array|null $content Data to send in the body for POST requests
 * @return resource
 */
function createOrthancContext($method = 'GET', $content = null) {
    global $orthancUser, $orthancPass;
    
    $auth = base64_encode("$orthancUser:$orthancPass");
    $headers = [
        "Authorization: Basic $auth",
        "Accept: application/json"
    ];
    
    $options = [
        'http' => [
            'method' => $method,
            'ignore_errors' => true // Allow us to see 401/404 messages instead of just failing
        ]
    ];
    
    if ($content !== null) {
        $headers[] = "Content-Type: application/json";
        $options['http']['content'] = json_encode($content);
    }
    
    $options['http']['header'] = implode("\r\n", $headers);
    
    return stream_context_create($options);
}
?>
