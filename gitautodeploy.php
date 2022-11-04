<?php

include('../private_html/.env');

$body = file_get_contents("php://input");

$valid = hash_equals(
	'sha256=' . hash_hmac('sha256', $body, GH_DEPLOY_KEY), 
    $_SERVER['HTTP_X_HUB_SIGNATURE_256'] 
);

if ( ! $valid) {
    header("HTTP/1.1 401 Unauthorized");
    return;
}

const API_KEY = CW_API_KEY;
const API_URL = "https://api.cloudways.com/api/v1";
const EMAIL = CW_API_EMAIL;


//Use this function to contact CW API
function callCloudwaysAPI($method, $url, $accessToken, $post = [])
{
    $baseURL = API_URL;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_URL, $baseURL . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //Set Authorization Header
    if ($accessToken) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    }
  
    //Set Post Parameters
    $encoded = '';
    if (count($post)) {
        foreach ($post as $name => $value) {
            $encoded .= urlencode($name) . '=' . urlencode($value) . '&';
        }
        $encoded = substr($encoded, 0, strlen($encoded) - 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != '200') {
        die('An error occurred code: ' . $httpcode . ' output: ' . substr($output, 0, 10000));
    }
    curl_close($ch);
    return json_decode($output);
}

//Fetch Access Token
$tokenResponse = callCloudwaysAPI('POST', '/oauth/access_token', null
    , [
    'email' => EMAIL, 
    'api_key' => API_KEY
    ]);

$accessToken = $tokenResponse->access_token;
$gitPullResponse = callCloudWaysAPI('POST', '/git/pull', $accessToken, [
    'server_id' => CW_SERVER_ID, 
    'app_id' => CW_APP_ID,
    'git_url' => GH_REPO,
    'branch_name' => GH_BRANCH
    /* Uncomment it if you want to use deploy path, Also add the new parameter in your link
    'deploy_path' => $_GET['deploy_path']  
    */	
    ]);

echo (json_encode($gitPullResponse));
