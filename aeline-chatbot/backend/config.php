<?php
/**
 * Backend configuration.
 *
 * The API key is read from the server environment or from backend/.env.
 * It is NEVER sent to the browser — only chat.php uses it, on the server.
 */

// Block direct access to this file from the browser.
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit;
}

// Load backend/.env (simple KEY=VALUE lines) if it exists.
$envFile = __DIR__ . '/.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

return [
    'api_key'       => getenv('ANTHROPIC_API_KEY') ?: '',
    'model'         => 'claude-opus-5-5',
    'max_tokens'    => 1024,
    'max_history'   => 20,    // how many previous messages we forward to the model
    'max_chars'     => 2000,  // max length of a single user message
    'rate_limit'    => 20,    // max requests per visitor...
    'rate_window'   => 60,    // ...per this many seconds
    'system_prompt' => <<<TXT
You are the friendly website assistant for Aeline, a consulting firm that helps
organizations grow with AI and data-driven strategy. Services: AI strategy,
data training, intelligent automation, and analytics consulting.
Answer briefly (2-4 sentences), in plain language. If someone wants a quote or
a meeting, ask them to use the "Get started" button or email hello@aeline.example.
If you don't know something about the company, say so instead of inventing it.
TXT,
];
