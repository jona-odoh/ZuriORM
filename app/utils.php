<?php

/*
This file is typically used to store utility functions that don’t necessarily fit into specific categories 
like database operations or user management but are still commonly used throughout the application. 
These utility functions are often general-purpose and aid in various tasks across your project.
*/

if (!function_exists('format_date')) {
    /**
     * Format a date into a readable format.
     *
     * @param string $date The date to format.
     * @param string $format The format to use (default is 'Y-m-d').
     * @return string
     */
    function format_date($date, $format = 'Y-m-d')
    {
        $dateObject = new DateTime($date);
        return $dateObject->format($format);
    }
}

if (!function_exists('generate_random_string')) {
    /**
     * Generate a random string of a given length.
     *
     * @param int $length Length of the random string.
     * @return string
     */
    function generate_random_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input to prevent XSS attacks.
     *
     * @param string $input The input to sanitize.
     * @return string
     */
    function sanitize_input($input)
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('log_message')) {
    /**
     * Log a message to a log file.
     *
     * @param string $message The message to log.
     * @param string $file The log file path.
     */
    function log_message($message, $file = 'app.log')
    {
        $logFile = __DIR__ . '/../logs/' . $file;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
}

// Generates a universally unique identifier (UUID).
if (!function_exists('generate_uuid')) {
    /**
     * Generate a UUID (version 4).
     *
     * @return string
     */
    function generate_uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

// Uploads Utility Functions
if (!function_exists('upload_file')) {
    function upload_file($fileInputName, $destinationDir, $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'])
    {
        if (isset($_FILES[$fileInputName])) {
            $file = $_FILES[$fileInputName];
            $fileName = basename($file['name']);
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if ($fileError === 0) {
                if (in_array($fileExtension, $allowedExtensions)) {
                    $fileNewName = uniqid('', true) . '.' . $fileExtension;
                    $fileDestination = $destinationDir . '/' . $fileNewName;
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        return $fileNewName;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
}
// Examples: $uploadedFilePath = upload_file($_FILES['myfile'], 'uploads', ['jpg', 'png', 'pdf'], 5242880);



// Validates an uploaded file based on extension and size.
if (!function_exists('validate_upload')) {
    /**
     * Validate an uploaded file.
     *
     * @param array $file The file array from $_FILES.
     * @param array $allowedExtensions List of allowed file extensions.
     * @param int $maxFileSize Maximum allowed file size in bytes.
     * @return bool True if valid, false otherwise.
     */
    function validate_upload(array $file, array $allowedExtensions = [], int $maxFileSize = 10485760): bool
    {
        if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileSize = $file['size'];

        // Check file extension
        if (!empty($allowedExtensions) && !in_array($fileExtension, $allowedExtensions)) {
            return false;
        }

        // Check file size
        if ($fileSize > $maxFileSize) {
            return false;
        }

        return true;
    }
}
// Examples: $isValid = validate_upload($_FILES['myfile'], ['jpg', 'png'], 5242880);


// gets the file extension from a file name.
if (!function_exists('get_file_extension')) {
    /**
     * Get the file extension from a file name.
     *
     * @param string $fileName The file name.
     * @return string The file extension.
     */
    function get_file_extension(string $fileName): string
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }
}
// Examples: $extension = get_file_extension('document.pdf'); // Outputs: 'pdf'


// Retrieves the MIME type of a file.
if (!function_exists('get_mime_type')) {
    /**
     * Get the MIME type of a file.
     *
     * @param string $filePath The path to the file.
     * @return string|null The MIME type or null if it can't be determined.
     */
    function get_mime_type(string $filePath): ?string
    {
        return mime_content_type($filePath) ?: null;
    }
}
// Examples: $mimeType = get_mime_type('uploads/image.jpg'); // Outputs: 'image/jpeg'

//Deletes a file from the server.
if (!function_exists('delete_file')) {
    /**
     * Delete a file from the server.
     *
     * @param string $filePath The path to the file.
     * @return bool True if deleted, false otherwise.
     */
    function delete_file(string $filePath): bool
    {
        return file_exists($filePath) ? unlink($filePath) : false;
    }
}
// Examples: $deleted = delete_file('uploads/oldfile.txt'); // Outputs: true or false

// Gets the size of a file.
if (!function_exists('get_file_size')) {
    /**
     * Get the size of a file in a human-readable format.
     *
     * @param string $filePath The path to the file.
     * @return string The file size in a human-readable format.
     */
    function get_file_size(string $filePath): string
    {
        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
// Examples: $fileSize = get_file_size('uploads/file.zip'); // Outputs: e.g., '4.5 MB'


// Converts an array to a CSV format string.
if (!function_exists('array_to_csv')) {
    /**
     * Convert an array to CSV format.
     *
     * @param array $array The array to convert.
     * @param string $delimiter The delimiter used in the CSV (default is ',').
     * @param string $enclosure The enclosure used in the CSV (default is '"').
     * @return string
     */
    function array_to_csv(array $array, string $delimiter = ',', string $enclosure = '"'): string
    {
        $f = fopen('php://memory', 'r+');
        foreach ($array as $row) {
            fputcsv($f, $row, $delimiter, $enclosure);
        }
        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return $csv;
    }
}
// Examples: 
// $csvString = array_to_csv([
//     ['Name', 'Email'],
//     ['Jonathan Odoh', 'Jonathan@example.com'],
//     ['Jane Odoh', 'jane@example.com']
// ]);
// Outputs: 'Name,Email\nJonathan Odoh,Jonathan@example.com\nJane Odoh,jane@example.com\n'


// Formats a number as currency.
if (!function_exists('money_format')) {
    /**
     * Format a number as currency.
     *
     * @param float $amount The amount to format.
     * @param string $currencySymbol The currency symbol.
     * @param int $decimals Number of decimal points.
     * @return string
     */
    function money_format(float $amount, string $currencySymbol = '$', int $decimals = 2): string
    {
        return $currencySymbol . number_format($amount, $decimals);
    }
}
// Examples: echo money_format(1234.56); 
// Outputs: '$1,234.56'


// Generates the full URL for assets (CSS, JS, images).
if (!function_exists('asset')) {
    /**
     * Generate an asset URL.
     *
     * @param string $path The asset path.
     * @return string
     */
    function asset(string $path): string
    {
        return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
    }
}
 // Examples: 
 /**
<link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
 */

// Dumps a variable's contents and stops execution.
if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed ...$vars Variables to dump.
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}
// Examples: dd($user, $posts);


// Generates a time difference string.
if (!function_exists('time_elapsed_string')) {
    /**
     * Get human-readable time difference.
     *
     * @param string $datetime The datetime string.
     * @param bool $full Whether to show full time difference.
     * @return string
     */
    function time_elapsed_string(string $datetime, bool $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
// Examples: echo time_elapsed_string('2023-01-01 12:00:00'); Outputs: '8 months ago'


// Encryption and decryption using OpenSSL.
if (!function_exists('encrypt')) {
    /**
     * Encrypt a string.
     *
     * @param string $plaintext The plaintext to encrypt.
     * @param string $key The encryption key.
     * @return string
     */
    function encrypt(string $plaintext, string $key): string
    {
        $iv = random_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }
}

if (!function_exists('decrypt')) {
    /**
     * Decrypt a string.
     *
     * @param string $ciphertext The ciphertext to decrypt.
     * @param string $key The decryption key.
     * @return string
     */
    function decrypt(string $ciphertext, string $key): string
    {
        $data = base64_decode($ciphertext);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);
        return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
    }
}
// Examples: 
// $key = 'your-secret-key';
// $encrypted = encrypt('Sensitive Data', $key);
// $decrypted = decrypt($encrypted, $key);


// Generates a CSRF token and stores it in the session.
if (!function_exists('generate_csrf_token')) {
    /**
     * Generate and store a CSRF token in the session.
     *
     * @return string
     */
    function generate_csrf_token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
}
// Examples: 
// $csrfToken = generate_csrf_token();
// echo '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';

// Verifies the provided CSRF token against the one stored in the session.
if (!function_exists('verify_csrf_token')) {
    /**
     * Verify the CSRF token.
     *
     * @param string $token The token to verify.
     * @return bool
     */
    function verify_csrf_token(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
//Example: 
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (!verify_csrf_token($_POST['csrf_token'])) {
//         die('Invalid CSRF token');
//     }
//     // Process the form
// }


// Retrieves previously submitted form input to repopulate forms.
if (!function_exists('old_input')) {
    /**
     * Retrieve old input value.
     *
     * @param string $key The input name.
     * @param mixed $default Default value if key doesn't exist.
     * @return mixed
     */
    function old_input(string $key, $default = null)
    {
        return $_SESSION['old_input'][$key] ?? $default;
    }
}
/* Example: 
<input type="text" name="username" value="<?php echo htmlspecialchars(old_input('username')); ?>">
*/

//Retrieves the current full URL.
if (!function_exists('current_url')) {
    /**
     * Get the current full URL.
     *
     * @return string
     */
    function current_url(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
//  Example: $url = current_url();  Outputs: e.g., 'https://www.example.com/current/page'


// Sends a JSON response with appropriate headers.
if (!function_exists('response_json')) {
    /**
     * Send a JSON response.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int $statusCode HTTP status code (default is 200).
     */
    function response_json($data, int $statusCode = 200)
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
        exit();
    }
}

//  Example: response_json(['success' => true, 'message' => 'Operation completed successfully.']);


// Redirects to a specified URL.
if (!function_exists('redirect')) {
    /**
     * Redirect to a specific URL.
     *
     * @param string $url The URL to redirect to.
     * @param int $statusCode HTTP status code (default is 302).
     */
    function redirect(string $url, int $statusCode = 302)
    {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }
}
//  Example: redirect('https://www.example.com');


// Returns a random value from an array.
if (!function_exists('array_random')) {
    /**
     * Get a random value from an array.
     *
     * @param array $array The array to pick from.
     * @return mixed
     */
    function array_random(array $array)
    {
        if (empty($array)) {
            return null;
        }
        return $array[array_rand($array)];
    }
}
//  Example: $random = array_random([1, 2, 3, 4, 5]); Outputs: Random number between 1 and 5


// Retrieves a value from a nested array using dot notation.
if (!function_exists('array_get')) {
    /**
     * Get an item from an array using dot notation.
     *
     * @param array $array The array to search.
     * @param string $key The key using dot notation.
     * @param mixed $default Default value if key doesn't exist.
     * @return mixed
     */
    function array_get(array $array, string $key, $default = null)
    {
        if (!$key) {
            return $array;
        }
        $keys = explode('.', $key);
        foreach ($keys as $segment) {
            if (array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        return $array;
    }
}
//  Example:
// $data = ['user' => ['name' => 'John', 'email' => 'john@example.com']];
// $name = array_get($data, 'user.name'); // Outputs: 'John'


// Extracts a single column from an array of arrays.
if (!function_exists('array_pluck')) {
    /**
     * Pluck a certain value from an array.
     *
     * @param array $array The array to pluck from.
     * @param string $key The key to pluck.
     * @return array
     */
    function array_pluck(array $array, string $key): array
    {
        return array_map(function ($item) use ($key) {
            return is_array($item) && isset($item[$key]) ? $item[$key] : null;
        }, $array);
    }
}
//  Example:
// $users = [
//     ['name' => 'Jonathan', 'email' => 'Jonathan@example.com'],
//     ['name' => 'Jane', 'email' => 'jane@example.com'],
// ];
// $emails = array_pluck($users, 'email');
// Outputs: ['Jonathan@example.com', 'jane@example.com']


// Checks if a nested array has a given key using dot notation.
if (!function_exists('array_has')) {
    /**
     * Check if an array has a given key using dot notation.
     *
     * @param array $array The array to check.
     * @param string $key The key using dot notation.
     * @return bool
     */
    function array_has(array $array, string $key): bool
    {
        if (!$key) {
            return false;
        }
        $keys = explode('.', $key);
        foreach ($keys as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }
        return true;
    }
}
//  Example: $exists = array_has($data, 'user.email');  Outputs: true


// Flattens a multi-dimensional array into a single-level array.
if (!function_exists('array_flatten')) {
    /**
     * Flatten a multi-dimensional array into a single-level array.
     *
     * @param array $array The multi-dimensional array.
     * @return array
     */
    function array_flatten(array $array): array
    {
        $flattened = [];
        array_walk_recursive($array, function ($value) use (&$flattened) {
            $flattened[] = $value;
        });
        return $flattened;
    }
}
//  Example: $flat = array_flatten([1, [2, 3], [[4]], 5]);
// Outputs: [1, 2, 3, 4, 5]


// Truncates a string to a specified length with optional suffix.
if (!function_exists('truncate')) {
    /**
     * Truncate a string to a specified length.
     *
     * @param string $string The input string.
     * @param int $length The desired length.
     * @param string $suffix The suffix to append (default is '...').
     * @return string
     */
    function truncate(string $string, int $length, string $suffix = '...'): string
    {
        if (strlen($string) <= $length) {
            return $string;
        }
        return substr($string, 0, $length) . $suffix;
    }
}
//  Example: $short = truncate('This is a long sentence that needs to be shorter.', 20);
// Outputs: 'This is a long sent...'


// Converts a string to snake_case.
if (!function_exists('snake_case')) {
    /**
     * Convert a string to snake_case.
     *
     * @param string $string The input string.
     * @return string
     */
    function snake_case(string $string): string
    {
        $string = preg_replace('/\s+/u', '_', trim($string));
        $string = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $string));
        return $string;
    }
}
//  Example: $snake = snake_case('HelloWorld');  Outputs: 'hello_world'


// Converts a string to camelCase.
if (!function_exists('camel_case')) {
    /**
     * Convert a string to camelCase.
     *
     * @param string $string The input string.
     * @return string
     */
    function camel_case(string $string): string
    {
        $result = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
        $result[0] = strtolower($result[0]);
        return $result;
    }
}
//  Example: $camel = camel_case('hello_world');  Outputs: 'helloWorld'


// Converts a string into a URL-friendly slug.
if (!function_exists('slugify')) {
    /**
     * Convert a string into a URL-friendly slug.
     *
     * @param string $text The text to convert.
     * @param string $separator The separator to use (default is '-').
     * @return string
     */
    function slugify(string $text, string $separator = '-'): string
    {
        // Replace non-letter or digits with separator
        $text = preg_replace('~[^\pL\d]+~u', $separator, $text);

        // Transliterate to ASCII
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim and lowercase
        $text = trim($text, $separator);
        $text = strtolower($text);

        // Remove duplicate separators
        $text = preg_replace('~-+~', $separator, $text);

        return !empty($text) ? $text : 'n-a';
    }
}
//  Example: $slug = slugify('Hello World!');  Outputs: 'hello-world'

/**
 * Converts a string to lowercase.
 *
 * @param string $string The input string.
 * @return string The lowercase string.
 */
function to_lowercase($string) {
    return strtolower($string);
}

// $input = 'HELLO WORLD';
// $lowercase = to_lowercase($input);
// echo "Lowercase: " . $lowercase;


// Get Client IP Address
if (!function_exists('get_client_ip')) {
    /**
     * Get the client's IP address.
     *
     * @return string
     */
    function get_client_ip(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? 
               $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
// Example: 
// $clientIp = get_client_ip();
// echo "Client IP: " . $clientIp;
// Output: Client IP: 192.123.1.1



// Check if a String is a Valid Email
if (!function_exists('is_valid_email')) {
    /**
     * Check if the given string is a valid email address.
     *
     * @param string $email
     * @return bool
     */
    function is_valid_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
/*Example: 
    $email = 'example@example.com';
    if (is_valid_email($email)) {
        echo "Valid email address.";
    } else {
        echo "Invalid email address.";
    }
*/


// Send an Email
if (!function_exists('send_email')) {
    /**
     * Send an email.
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers
     * @return bool
     */
    function send_email(string $to, string $subject, string $message, string $headers = ''): bool
    {
        return mail($to, $subject, $message, $headers);
    }
}
/*Example:
    $to = 'recipient@example.com';
    $subject = 'Test Email';
    $message = 'This is a test email message.';
    $headers = 'From: sender@example.com';

    if (send_email($to, $subject, $message, $headers)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
*/


// Generate a Random Password
if (!function_exists('generate_random_password')) {
    /**
     * Generate a random password.
     *
     * @param int $length
     * @return string
     */
    function generate_random_password(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        return substr(str_shuffle($chars), 0, $length);
    }
}
/*Example:
$password = generate_random_password(16);
echo "Generated password: " . $password;
*/


// Convert a String to Title Case
if (!function_exists('title_case')) {
    /**
     * Convert a string to title case.
     *
     * @param string $string
     * @return string
     */
    function title_case(string $string): string
    {
        return ucwords(strtolower($string));
    }
}
/*Example:
$string = 'hello world';
$titleCaseString = title_case($string);
echo "Title Case: " . $titleCaseString;
*/


// Check if a String is a Valid URL
if (!function_exists('is_valid_url')) {
    /**
     * Check if the given string is a valid URL.
     *
     * @param string $url
     * @return bool
     */
    function is_valid_url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
/*Example:
$url = 'https://www.example.com';
if (is_valid_url($url)) {
    echo "Valid URL.";
} else {
    echo "Invalid URL.";
}
*/


// Convert an Array to an XML String
if (!function_exists('array_to_xml')) {
    /**
     * Convert an array to XML format.
     *
     * @param array $array
     * @param SimpleXMLElement|null $xml
     * @return string
     */
    function array_to_xml(array $array, ?SimpleXMLElement $xml = null): string
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement('<root/>');
        }
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                array_to_xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, htmlspecialchars((string)$value));
            }
        }
        
        return $xml->asXML();
    }
}
/*Example:
$array = [
    'name' => 'Jonathan Odoh',
    'email' => 'jonathan.odoh@example.com',
    'address' => [
        'street' => '123 Main St',
        'city' => 'Nigeria'
    ]
];

$xmlString = array_to_xml($array);
echo "XML Output: " . $xmlString;
*/


// Parse a CSV String into an Array
if (!function_exists('csv_to_array')) {
    /**
     * Convert a CSV string into an array.
     *
     * @param string $csvString
     * @param string $delimiter
     * @return array
     */
    function csv_to_array(string $csvString, string $delimiter = ','): array
    {
        $rows = explode(PHP_EOL, trim($csvString));
        $header = str_getcsv(array_shift($rows), $delimiter);
        $array = [];
        
        foreach ($rows as $row) {
            $data = str_getcsv($row, $delimiter);
            $array[] = array_combine($header, $data);
        }
        
        return $array;
    }
}
/*Example:
$csvString = "name,email,age\nJohn Doe,john.doe@example.com,30\nJane Smith,jane.smith@example.com,25";
$array = csv_to_array($csvString);

echo "<pre>";
print_r($array);
echo "</pre>";

*/


// Convert a JSON String into an Array
if (!function_exists('json_to_array')) {
    /**
     * Convert a JSON string into an array.
     *
     * @param string $json
     * @return array|null
     */
    function json_to_array(string $json): ?array
    {
        return json_decode($json, true);
    }
}
/*Example:
$jsonString = '{"name": "John Doe", "email": "john.doe@example.com"}';
$array = json_to_array($jsonString);

echo "<pre>";
print_r($array);
echo "</pre>";

*/

// Generate a Random Integer within a Range
if (!function_exists('random_int_in_range')) {
    /**
     * Generate a random integer within a given range.
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    function random_int_in_range(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
/*Example:
$randomInt = random_int_in_range(1, 100);
echo "Random Integer: " . $randomInt;
*/

/**
 * Calculates the age from a birthdate.
 *
 * @param string $birthdate The birthdate in 'Y-m-d' format.
 * @return int The age.
 */
function calculate_age($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $today->diff($birthDate)->y;
    return $age;
}

/*Exaples: 
$birthdate = '1990-05-15';
$age = calculate_age($birthdate);
echo "Age: " . $age;

*/