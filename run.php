<?php
// Set custom error handling to prevent server default error pages
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Ensure the request is valid
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['code'])) {
    http_response_code(400);
    exit('Bad Request');
}

// Determine the language for user feedback
$lang = isset($_POST['lang']) && $_POST['lang'] === 'zh' ? 'zh' : 'en';
$userCode = $_POST['code'];

/**
 * Custom Shutdown Handler for Fatal Errors.
 * This function is registered to run at the end of the script,
 * allowing it to catch fatal errors like timeouts or memory exhaustion
 * that normal try-catch blocks cannot handle.
 */
register_shutdown_function('fatal_error_handler');

function fatal_error_handler() {
    global $lang; // Access the global language variable

    $error = error_get_last();

    // Check if a fatal error occurred
    if ($error !== null && $error['type'] === E_ERROR) {
        $errorTitle = '';
        $errorMessage = '';

        // Case 1: Execution timeout
        if (strpos($error['message'], 'Maximum execution time') !== false) {
            $errorTitle = ($lang === 'zh') ? '运行超时' : 'Execution Timeout';
            $errorMessage = ($lang === 'zh')
                ? '您的代码执行超过了 <b>15秒</b> 的时间限制。请检查代码中是否存在死循环或耗时过长的操作。公益服务器资源有限，请多多谅解！'
                : 'Your code exceeded the maximum execution time of <b>15 seconds</b>. Please check for infinite loops or long-running operations. Resources are limited on this public server.';
            send_friendly_error_page($errorTitle, $errorMessage, 'warning');

        // Case 2: Memory limit exceeded
        } elseif (strpos($error['message'], 'Allowed memory size') !== false) {
            $errorTitle = ($lang === 'zh') ? '内存超限' : 'Memory Limit Exceeded';
            $errorMessage = ($lang === 'zh')
                ? '您的代码消耗的内存超过了 <b>32MB</b> 的限制。请检查代码是否在循环中创建了大量变量或处理了过大的数据。公益服务器资源有限，请多多谅解！'
                : 'Your code exhausted the allowed memory size of <b>32MB</b>. Please check for memory-intensive operations, such as creating large arrays in a loop.';
            send_friendly_error_page($errorTitle, $errorMessage, 'warning');
        }
    }
}

/**
 * Renders a user-friendly, styled error page and terminates the script.
 * @param string $title The title of the error.
 * @param string $message The descriptive error message.
 * @param string $type The type of error ('security' or 'warning') to determine styling.
 */
function send_friendly_error_page($title, $message, $type = 'security') {
    // Determine colors based on error type
    $borderColor = ($type === 'warning') ? '#FFC107' : '#F44336'; // Orange for warning, Red for security
    $iconColor = $borderColor;
    $titleColor = ($type === 'warning') ? '#FFE082' : '#FFCDD2';
    
    die(<<<HTML
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #1F202E; margin: 0; padding: 20px; display: grid; place-items: center; height: 100vh; color: #E0E0E0; }
    .error-card { background-color: #2a2c41; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 550px; width: 100%; border-top: 4px solid {$borderColor}; padding: 25px 30px; display: flex; align-items: flex-start; gap: 20px; }
    .error-card svg { flex-shrink: 0; width: 40px; height: 40px; color: {$iconColor}; }
    .error-card-content h4 { color: {$titleColor}; margin: 0 0 10px 0; font-size: 1.2em; }
    .error-card-content p { margin: 0; line-height: 1.6; color: #BDBDBD; }
    .error-card-content code { background-color: #1F202E; padding: 2px 6px; border-radius: 4px; color: {$titleColor}; }
</style>
<div class="error-card">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13 14h-2V9h2m0 9h-2v-2h2M1 21h22L12 2L1 21"/></svg>
    <div class="error-card-content">
        <h4>{$title}</h4>
        <p>{$message}</p>
    </div>
</div>
HTML);
}

// --- Core Security Filter ---

// The comprehensive, merged list of disabled functions for maximum security.
$disabledFunctions = [
    'assert', 'chgrp', 'chmod', 'chown', 'copy', 'create_function', 'curl_exec', 
    'curl_multi_exec', 'delete', 'dl', 'eval', 'exec', 'fclose', 'file', 
    'file_get_contents', 'file_put_contents', 'fopen', 'fread', 'fwrite', 'fsockopen', 
    'get_defined_vars', 'glob', 'highlight_file', 'include', 'include_once', 
    'ini_alter', 'ini_restore', 'ini_set', 'link', 'mkdir', 'move_uploaded_file', 
    'openlog', 'parse_ini_file', 'passthru', 'pcntl_exec', 'pfsockopen', 'phpinfo', 
    'popen', 'posix_kill', 'posix_mkfifo', 'posix_setpgid', 'posix_setsid', 'posix_setuid', 
    'proc_open', 'readfile', 'readlink', 'readdir', 'rename', 'require', 'require_once', 
    'rmdir', 'scandir', 'shell_exec', 'show_source', 'socket_connect', 'socket_create', 
    'symlink', 'syslog', 'system', 'tempnam', 'touch', 'unlink'
];

// Use PHP's built-in tokenizer for accurate function detection.
$tokens = token_get_all($userCode);
foreach ($tokens as $index => $token) {
    // Check if the token is a function name from our blacklist...
    if (is_array($token) && $token[0] === T_STRING && in_array(strtolower($token[1]), $disabledFunctions, true)) {
        // ...and is immediately followed by an opening parenthesis, indicating a function call.
        $nextToken = $tokens[$index + 1] ?? null;
        if (is_string($nextToken) && $nextToken === '(') {
            $functionName = htmlspecialchars($token[1]);
            
            $errorTitle = ($lang === 'zh') ? '执行错误' : 'Execution Error';
            $errorMessage = ($lang === 'zh') 
                ? "出于安全原因，函数 <code>{$functionName}</code> 已被禁用。请尝试其他方法。" 
                : "For security reasons, the function <code>{$functionName}</code> has been disabled. Please try another method.";

            // Render the security error page.
            send_friendly_error_page($errorTitle, $errorMessage, 'security');
        }
    }
}

// --- File Execution ---

$tempDir = __DIR__ . '/temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}
if (!is_writable($tempDir)) {
    http_response_code(500);
    die('Error: Temp directory is not writable.');
}

// Preamble to set resource limits for the user's code.
$executionPreamble = "<?php\nset_time_limit(15);\nini_set('memory_limit', '32M');\n?>";

$tempFile = $tempDir . '/' . uniqid('php_run_', true) . '.php';

if (file_put_contents($tempFile, $executionPreamble . $userCode) === false) {
    http_response_code(500);
    die('Error: Could not write to temp file.');
}

// Register a separate shutdown function specifically for cleaning up the temp file.
register_shutdown_function(function () use ($tempFile) {
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});

// Execute the user's code.
include $tempFile;

?>
