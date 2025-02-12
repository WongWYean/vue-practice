<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Debugging: Log the request method
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

$jsonFile = __DIR__ . "/jobs.json"; // Path to JSON file

// Check if the JSON file exists
if (!file_exists($jsonFile)) {
    error_log("Error: JSON file not found at " . $jsonFile);
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error - JSON file not found"]);
    exit;
}

// Debugging: Log the JSON file path
error_log("JSON file path: " . $jsonFile);

$data = json_decode(file_get_contents($jsonFile), true);

// Check if the JSON data is valid
if ($data === null) {
    error_log("Error: Failed to decode JSON from " . $jsonFile);
    error_log("JSON Error: " . json_last_error_msg());
    http_response_code(500);
    echo json_encode(["error" => "Internal Server Error - Failed to decode JSON"]);
    exit;
}

// Debugging: Log the decoded JSON data
error_log("Decoded JSON data: " . print_r($data, true));

$method = $_SERVER['REQUEST_METHOD'];

// 🟢 GET: Retrieve all jobs
if ($method === 'GET') {
    error_log("Handling GET request");
    echo json_encode($data["jobs"]);
    exit;
}

// 🔵 POST: Add a new job
if ($method === 'POST') {
    error_log("Handling POST request");
    $newJob = json_decode(file_get_contents("php://input"), true);
    if ($newJob === null) {
        error_log("Error: Failed to decode JSON input for POST");
        error_log("JSON Error: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(["error" => "Bad Request - Invalid JSON"]);
        exit;
    }
    $newJob["id"] = uniqid(); // Generate unique ID
    $data["jobs"][] = $newJob;

    // Debugging: Log the new job data
    error_log("New Job Data: " . print_r($newJob, true));

    if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        error_log("Error: Failed to write to JSON file at " . $jsonFile);
        http_response_code(500);
        echo json_encode(["error" => "Internal Server Error - Failed to write to JSON file"]);
        exit;
    }
    echo json_encode(["message" => "Job added successfully", "job" => $newJob]);
    exit;
}

// 🟡 PUT: Update a job
if ($method === 'PUT') {
    error_log("Handling PUT request");

    // Decode JSON input correctly
    $updatedJob = json_decode(file_get_contents("php://input"), true);
    if ($updatedJob === null) {
        error_log("Error: Failed to decode JSON input for PUT");
        error_log("JSON Error: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(["error" => "Bad Request - Invalid JSON"]);
        exit;
    }

    // Extract job ID from URL query parameter or JSON body
    $jobId = $_GET['jobId'] ?? $updatedJob['id'] ?? null;

    if (!$jobId) {
        error_log("Error: Job ID is missing in PUT request");
        http_response_code(400);
        echo json_encode(["error" => "Bad Request - Job ID is required"]);
        exit;
    }

    error_log("Updating Job ID: " . $jobId);

    // Search for the job and update it
    $found = false;
    foreach ($data["jobs"] as &$job) {
        if ($job["id"] === $jobId) {
            $job = array_merge($job, $updatedJob);
            $found = true;
            break;
        }
    }

    if (!$found) {
        http_response_code(404);
        echo json_encode(["error" => "Job not found"]);
        exit;
    }

    // Save the updated JSON file
    if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        error_log("Error: Failed to write to JSON file at " . $jsonFile);
        http_response_code(500);
        echo json_encode(["error" => "Internal Server Error - Failed to write to JSON file"]);
        exit;
    }

    echo json_encode(["message" => "Job updated successfully", "job" => $updatedJob]);
    exit;
}

// 🔴 DELETE: Remove a job
if ($method === 'DELETE') {
    error_log("Handling DELETE request");

    // Decode JSON input correctly
    $deletedJob = json_decode(file_get_contents("php://input"), true);
    
    // Extract job ID from URL query parameter or JSON body
    $jobId = $_GET['jobId'] ?? $deletedJob['id'] ?? null;

    if (!$jobId) {
        error_log("Error: Job ID is missing in DELETE request");
        http_response_code(400);
        echo json_encode(["error" => "Bad Request - Job ID is required"]);
        exit;
    }

    error_log("Deleting Job ID: " . $jobId);

    // Check if the job exists before deleting
    $initialCount = count($data["jobs"]);
    $data["jobs"] = array_values(array_filter($data["jobs"], function ($job) use ($jobId) {
        return $job["id"] !== $jobId;
    }));

    // Check if any job was actually deleted
    if (count($data["jobs"]) === $initialCount) {
        http_response_code(404);
        echo json_encode(["error" => "Job not found"]);
        exit;
    }

    // Save the updated JSON file
    if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        error_log("Error: Failed to write to JSON file at " . $jsonFile);
        http_response_code(500);
        echo json_encode(["error" => "Internal Server Error - Failed to write to JSON file"]);
        exit;
    }

    echo json_encode(["message" => "Job deleted successfully"]);
    exit;
}


// 🟡 If no matching method, return 405
http_response_code(405);
header("Content-Type: application/json");
echo json_encode(["error" => "Method Not Allowed"]);
?>