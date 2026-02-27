<?php

// Mandatory claims
$mobicard_version = "2.0";
$mobicard_mode = "LIVE"; // production
$mobicard_merchant_id = "4";
$mobicard_api_key = "YmJkOGY0OTZhMTU2ZjVjYTIyYzFhZGQyOWRiMmZjMmE2ZWU3NGIxZWM3ZTBiZSJ9";
$mobicard_secret_key = "NjIwYzEyMDRjNjNjMTdkZTZkMjZhOWNiYjIxNzI2NDQwYzVmNWNiMzRhMzBjYSJ9";

$mobicard_token_id = abs(rand(1000000,1000000000));
$mobicard_token_id = "$mobicard_token_id";

$mobicard_txn_reference = abs(rand(1000000,1000000000));
$mobicard_txn_reference = "$mobicard_txn_reference";

$mobicard_service_id = "20000";
$mobicard_service_type = "TOKENIZATION";

$mobicard_single_use_token_flag = "0"; // Change to "1" for single-use tokens

// Card details to tokenize
$mobicard_card_number = "4242424242424242"; // Test card number
$mobicard_card_expiry_month = "02"; // MM
$mobicard_card_expiry_year = "28"; // YY

// Custom data fields (optional)
$mobicard_custom_one = "mobicard_custom_one";
$mobicard_custom_two = "mobicard_custom_two";
$mobicard_custom_three = "mobicard_custom_three";
$mobicard_extra_data = "your_custom_data_here_will_be_returned_as_is";

// Create JWT Header
$mobicard_jwt_header = [
    "typ" => "JWT",
    "alg" => "HS256"
];
$mobicard_jwt_header = rtrim(strtr(base64_encode(json_encode($mobicard_jwt_header)), '+/', '-_'), '=');

// Create JWT Payload
$mobicard_jwt_payload = array(
    "mobicard_version" => "$mobicard_version",
    "mobicard_mode" => "$mobicard_mode",
    "mobicard_merchant_id" => "$mobicard_merchant_id",
    "mobicard_api_key" => "$mobicard_api_key",
    "mobicard_service_id" => "$mobicard_service_id",
    "mobicard_service_type" => "$mobicard_service_type",
    "mobicard_token_id" => "$mobicard_token_id",
    "mobicard_txn_reference" => "$mobicard_txn_reference",
    "mobicard_single_use_token_flag" => "$mobicard_single_use_token_flag",
    "mobicard_card_number" => "$mobicard_card_number",
    "mobicard_card_expiry_month" => "$mobicard_card_expiry_month",
    "mobicard_card_expiry_year" => "$mobicard_card_expiry_year",
    "mobicard_custom_one" => "$mobicard_custom_one",
    "mobicard_custom_two" => "$mobicard_custom_two",
    "mobicard_custom_three" => "$mobicard_custom_three",
    "mobicard_extra_data" => "$mobicard_extra_data"
);

$mobicard_jwt_payload = rtrim(strtr(base64_encode(json_encode($mobicard_jwt_payload)), '+/', '-_'), '=');

// Generate Signature
$header_payload = $mobicard_jwt_header . '.' . $mobicard_jwt_payload;
$mobicard_jwt_signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $header_payload, $mobicard_secret_key, true)), '+/', '-_'), '=');

// Create Final JWT
$mobicard_auth_jwt = "$mobicard_jwt_header.$mobicard_jwt_payload.$mobicard_jwt_signature";

// Make API Call
$mobicard_request_access_token_url = "https://mobicardsystems.com/api/v1/card_tokenization";

$mobicard_curl_post_data = array('mobicard_auth_jwt' => $mobicard_auth_jwt);

$curl_mobicard = curl_init();
curl_setopt($curl_mobicard, CURLOPT_URL, $mobicard_request_access_token_url);
curl_setopt($curl_mobicard, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_mobicard, CURLOPT_POST, true);
curl_setopt($curl_mobicard, CURLOPT_POSTFIELDS, json_encode($mobicard_curl_post_data));
curl_setopt($curl_mobicard, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl_mobicard, CURLOPT_SSL_VERIFYPEER, false);
$mobicard_curl_response = curl_exec($curl_mobicard);
curl_close($curl_mobicard);

// Parse Response
$response_data = json_decode($mobicard_curl_response, true);

if(isset($response_data) && is_array($response_data)) {
    if($response_data['status'] === 'SUCCESS') {
        // Store these in your database
        $card_token = $response_data['card_information']['card_token'];
        $card_number_masked = $response_data['card_information']['card_number_masked'];
        
        echo "Tokenization Successful!
";
        echo "Card Token: " . $card_token . "
";
        echo "Masked Card: " . $card_number_masked . "
";
        echo "Store these values instead of actual card data.";
    } else {
        echo "Error: " . $response_data['status_message'] . " (Code: " . $response_data['status_code'] . ")";
    }
} else {
    echo "Error: Invalid API response";
}
