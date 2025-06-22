<?php
// Test PHP Upload Limits
echo "<h2>Current PHP Upload Settings:</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>max_input_time: " . ini_get('max_input_time') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'On' : 'Off') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "</ul>";

// Try to set limits
echo "<h2>Attempting to set limits...</h2>";
$results = [];
$results[] = ini_set('upload_max_filesize', '500M');
$results[] = ini_set('post_max_size', '500M');
$results[] = ini_set('memory_limit', '512M');
$results[] = ini_set('max_execution_time', '300');
$results[] = ini_set('max_input_time', '300');

echo "<h2>After setting limits:</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>max_input_time: " . ini_get('max_input_time') . "</li>";
echo "</ul>";

echo "<h2>Setting results:</h2>";
echo "<ul>";
foreach ($results as $i => $result) {
    echo "<li>Setting " . ($i + 1) . ": " . ($result !== false ? "Success" : "Failed") . "</li>";
}
echo "</ul>";
?>
