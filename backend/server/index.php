<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🟢 Set headers to allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🟢 API Routing
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Debugging: Log the request path
error_log("Requested Path: " . $path);

// Check if the requested path matches '/server/jobs'
if ($path === '/server/jobs') {
    error_log("Routing to jobs.php");
    require __DIR__ . '/jobs.php';
    exit;
}

// 🟡 If no matching route, return 404
http_response_code(404);
header("Content-Type: application/json");
echo json_encode(["error" => "API Not Found"]);
?>