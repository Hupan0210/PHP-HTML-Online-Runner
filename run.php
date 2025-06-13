<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['code'])) {
    http_response_code(400); exit('Bad Request');
}

// 读取POST过来的语言，默认为英语
$lang = isset($_POST['lang']) && $_POST['lang'] === 'zh' ? 'zh' : 'en';
$userCode = $_POST['code'];

// v--- 新增：致命错误处理函数 ---v
// 无论脚本是正常结束还是因致命错误中止，此函数都将在最后执行
register_shutdown_function('fatal_error_handler');

function fatal_error_handler() {
    global $lang; // 获取全局语言设置

    $error = error_get_last();

    // 检查是否发生了致命错误
    if ($error !== null && $error['type'] === E_ERROR) {
        $errorTitle = '';
        $errorMessage = '';

        // 检查错误消息，判断是超时还是内存超限
        if (strpos($error['message'], 'Maximum execution time') !== false) {
            if ($lang === 'zh') {
                $errorTitle = '运行超时';
                $errorMessage = '您的代码执行超过了 <b>15秒</b> 的时间限制。请检查代码中是否存在死循环或耗时过长的操作。公益服务器资源有限，请多多谅解！';
            } else {
                $errorTitle = 'Execution Timeout';
                $errorMessage = 'Your code exceeded the maximum execution time of <b>15 seconds</b>. Please check for infinite loops or long-running operations. Resources are limited on this public server.';
            }
            send_friendly_error_page($errorTitle, $errorMessage);

        } elseif (strpos($error['message'], 'Allowed memory size') !== false) {
            if ($lang === 'zh') {
                $errorTitle = '内存超限';
                $errorMessage = '您的代码消耗的内存超过了 <b>32MB</b> 的限制。请检查代码是否在循环中创建了大量变量或处理了过大的数据。公益服务器资源有限，请多多谅解！';
            } else {
                $errorTitle = 'Memory Limit Exceeded';
                $errorMessage = 'Your code exhausted the allowed memory size of <b>32MB</b>. Please check for memory-intensive operations, such as creating large arrays in a loop.';
            }
            send_friendly_error_page($errorTitle, $errorMessage);
        }
    }
}

// 新增：用于发送统一格式友好错误页面的函数
function send_friendly_error_page($title, $message) {
    // 使用heredoc语法输出HTML
    die(<<<HTML
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #1F202E; margin: 0; padding: 20px; display: grid; place-items: center; height: 100vh; color: #E0E0E0; }
    /* 使用橙色边框表示资源警告，区别于红色的安全错误 */
    .error-card { background-color: #2a2c41; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 550px; width: 100%; border-top: 4px solid #FFC107; padding: 25px 30px; display: flex; align-items: flex-start; gap: 20px; }
    .error-card svg { flex-shrink: 0; width: 40px; height: 40px; color: #FFC107; }
    .error-card-content h4 { color: #FFE082; margin: 0 0 10px 0; font-size: 1.2em; }
    .error-card-content p { margin: 0; line-height: 1.6; color: #BDBDBD; }
    .error-card-content code { background-color: #1F202E; padding: 2px 6px; border-radius: 4px; color: #FFE082; }
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
// ^--- 新增功能结束 ---^


// --- 安全检查逻辑 (保持不变) ---
$disabledFunctions = [
    'exec', 'passthru', 'shell_exec', 'system', 'proc_open', 'popen', 'pcntl_exec', 'show_source',
    'fopen', 'chmod', 'chown', 'copy', 'delete', 'file_put_contents', 'file_get_contents', 'file',
    'link', 'mkdir', 'move_uploaded_file', 'rename', 'rmdir', 'symlink', 'tempnam', 'touch', 'unlink',
    'scandir', 'glob', 'readdir', 'curl_exec', 'curl_multi_exec', 'fsockopen', 'pfsockopen',
    'socket_create', 'socket_connect', 'eval', 'assert', 'create_function',
    'include', 'include_once', 'require', 'require_once',
    'parse_ini_file', 'phpinfo', 'posix_kill', 'posix_mkfifo', 'posix_setpgid', 'posix_setsid', 'posix_setuid'
];

$tokens = token_get_all($userCode);
foreach ($tokens as $index => $token) {
    if (is_array($token) && $token[0] === T_STRING && in_array(strtolower($token[1]), $disabledFunctions, true)) {
        $nextToken = $tokens[$index + 1] ?? null;
        if (is_string($nextToken) && $nextToken === '(') {
            $functionName = htmlspecialchars($token[1]);
            
            $errorTitle = ($lang === 'zh') ? '执行错误' : 'Execution Error';
            $errorMessage = ($lang === 'zh') ? "出于安全原因，函数 <code>{$functionName}</code> 已被禁用。请尝试其他方法。" : "For security reasons, the function <code>{$functionName}</code> has been disabled. Please try another method.";

            // 对于安全错误，使用红色主题的错误页面
            die(<<<HTML
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #1F202E; margin: 0; padding: 20px; display: grid; place-items: center; height: 100vh; color: #E0E0E0; }
    .error-card { background-color: #2a2c41; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 550px; width: 100%; border-top: 4px solid #F44336; padding: 25px 30px; display: flex; align-items: flex-start; gap: 20px; }
    .error-card svg { flex-shrink: 0; width: 40px; height: 40px; color: #F44336; }
    .error-card-content h4 { color: #FFCDD2; margin: 0 0 10px 0; font-size: 1.2em; }
    .error-card-content p { margin: 0; line-height: 1.6; color: #BDBDBD; }
    .error-card-content code { background-color: #1F202E; padding: 2px 6px; border-radius: 4px; color: #FFCDD2; }
</style>
<div class="error-card">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M13 14h-2V9h2m0 9h-2v-2h2M1 21h22L12 2L1 21"/></svg>
    <div class="error-card-content">
        <h4>{$errorTitle}</h4>
        <p>{$errorMessage}</p>
    </div>
</div>
HTML);
        }
    }
}
// --- 安全检查结束 ---


$tempDir = __DIR__ . '/temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}
if (!is_writable($tempDir)) {
    http_response_code(500);
    die('Error: Temp directory is not writable.');
}

// v--- 代码修改处 ---v
// 为执行的代码增加15秒时间和32MB内存的限制
$executionPreamble = "<?php\nset_time_limit(15);\nini_set('memory_limit', '32M');\n?>";
// ^--- 代码修改处 ---^

$tempFile = $tempDir . '/' . uniqid('php_run_', true) . '.php';

if (file_put_contents($tempFile, $executionPreamble . $userCode) === false) {
    http_response_code(500);
    die('Error: Could not write to temp file.');
}

// 这里的 shutdown function 还是需要的，用于删除临时文件
register_shutdown_function(function () use ($tempFile) {
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});

// 执行用户的代码。如果发生致命错误，上面的 fatal_error_handler 会捕获它。
include $tempFile;
?>