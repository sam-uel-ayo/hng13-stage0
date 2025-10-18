<?php

http_response_code(200);

$responseBody = Profile::getProfileData();

echo json_encode( $responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

