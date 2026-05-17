<?php
// Facebook auto-posting utility
// Fill in your Facebook Page Access Token and Page ID
function postToFacebook($message, $imageUrl = null) {
    $accessToken = 'YOUR_PAGE_ACCESS_TOKEN';
    $pageId = 'YOUR_PAGE_ID';
    $url = "https://graph.facebook.com/$pageId/photos";
    $data = [
        'caption' => $message,
        'access_token' => $accessToken
    ];
    if ($imageUrl) {
        $data['url'] = $imageUrl;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>
