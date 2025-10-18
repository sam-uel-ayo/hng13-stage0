<?php

/**
 * service to build the complete profile response.
 */
class Profile {

    /** @var bool To ensure the .env file is loaded only once. */
    private static bool $envLoaded = false;

    // The complete structured profile returns the final response array ready to be encoded as JSON.
    public static function getProfileData(): array {
        self::loadEnv();
        return [
            "status" => "success",
            "user"   => self::getUserInfo(),
            "timestamp" => self::generateTimestamp(),
            "fact"   => self::fetchCatFact()
        ];
    }

    // Get user profile information from environment variables.
    private static function getUserInfo(): array {
        return [
            "email" => getenv('USER_EMAIL') ?: 'email_not_set_in_env',
            "name"  => getenv('USER_NAME') ?: 'name_not_set_in_env',
            "stack" => getenv('USER_STACK') ?: 'stack_not_set_in_env'
        ];
    }

    // Generates the current UTC timestamp in ISO 8601 format.
    private static function generateTimestamp(): string {
        return (new DateTime('now', new DateTimeZone('UTC')))
            ->format('Y-m-d\TH:i:s.v\Z');
    }


    //Fetches a random cat fact from the Cat Facts API.Return string A cat fact or a fallback error message.
    private static function fetchCatFact(): string {
        $apiUrl = 'https://catfact.ninja/fact';
        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'My-PHP-App/1.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("CURL error fetching cat fact: " . $error);
            return "Could not fetch cat fact due to a network error.";
        }

        if ($httpCode !== 200) {
            error_log("Cat Fact API returned non-200 status: " . $httpCode);
            return "Could not fetch cat fact (API status: " . $httpCode . ").";
        }

        $data = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($data['fact'])) {
            return $data['fact'];
        } else {
            error_log("Failed to decode JSON response from Cat Fact API.");
            return "Could not parse the cat fact response.";
        }
    }


    // Loads environment variables from a .env file in the project root.
    private static function loadEnv(): void {
        if (self::$envLoaded) {
            return;
        }

        // Path to the .env file, assuming it's in the project root.
        $dotenvPath = __DIR__ . '/../.env';

        if (!is_readable($dotenvPath)) {
            error_log('.env file not found or not readable.');
            self::$envLoaded = true;
            return;
        }

        $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove surrounding quotes from the value
            if (strlen($value) > 1 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }
            
            // Set the environment variable for the current request
            putenv(sprintf('%s=%s', $name, $value));
        }

        self::$envLoaded = true;
    }
}

