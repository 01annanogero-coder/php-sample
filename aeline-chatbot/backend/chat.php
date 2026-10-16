<?php
/**
 * POST /backend/chat.php
 * Body:    {"messages": [{"role": "user"|"assistant", "content": "..."}, ...]}
 * Returns: {"reply": "..."}  or  {"error": "..."}
 *
 * The browser talks ONLY to this file. This file talks to the Claude API
 * using the secret key, so the key never reaches the frontend.
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\RateLimitException;

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body);
    exit;
}

$config = require __DIR__ . '/config.php';

// 1. Only accept POST requests with JSON.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Method not allowed']);
}

// 2. Only accept requests from our own site (basic CSRF / hotlink protection).
$origin     = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost = parse_url($origin, PHP_URL_HOST);
$ourHost    = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
if ($origin !== '' && $originHost !== $ourHost) {
    respond(403, ['error' => 'Forbidden']);
}

// 3. Simple per-visitor rate limit stored in the PHP session.
session_start();
$now = time();
$_SESSION['hits'] = array_values(array_filter(
    $_SESSION['hits'] ?? [],
    fn ($t) => $t > $now - $config['rate_window']
));
if (count($_SESSION['hits']) >= $config['rate_limit']) {
    respond(429, ['error' => 'Too many messages. Please wait a minute and try again.']);
}
$_SESSION['hits'][] = $now;
session_write_close();

if ($config['api_key'] === '') {
    respond(500, ['error' => 'Server is missing ANTHROPIC_API_KEY. See README.']);
}

// 4. Validate the conversation sent by the browser. Never trust client input.
$input    = json_decode(file_get_contents('php://input') ?: '', true);
$incoming = $input['messages'] ?? null;
if (!is_array($incoming) || $incoming === []) {
    respond(400, ['error' => 'No messages provided']);
}

$messages = [];
foreach (array_slice($incoming, -$config['max_history']) as $m) {
    $role    = $m['role'] ?? '';
    $content = is_string($m['content'] ?? null) ? trim($m['content']) : '';
    if (!in_array($role, ['user', 'assistant'], true) || $content === '') {
        respond(400, ['error' => 'Invalid message format']);
    }
    if (mb_strlen($content) > $config['max_chars']) {
        respond(400, ['error' => 'Message is too long']);
    }
    $messages[] = ['role' => $role, 'content' => $content];
}
// The conversation must start and end with a user message.
while ($messages !== [] && $messages[0]['role'] !== 'user') {
    array_shift($messages);
}
if ($messages === [] || end($messages)['role'] !== 'user') {
    respond(400, ['error' => 'Last message must be from the user']);
}

// 5. Call Claude.
$client = new Client(apiKey: $config['api_key']);

try {
    $message = $client->messages->create(
        model: $config['model'],
        maxTokens: $config['max_tokens'],
        system: $config['system_prompt'],
        outputConfig: ['effort' => 'low'], // quick, cheap answers suit a website chat
        messages: $messages,
    );
} catch (AuthenticationException $e) {
    error_log('Claude auth error: ' . $e->getMessage());
    respond(500, ['error' => 'The chatbot is not configured correctly.']);
} catch (RateLimitException $e) {
    respond(503, ['error' => 'The assistant is busy right now. Please try again shortly.']);
} catch (APIStatusException $e) {
    error_log('Claude API error: ' . $e->getMessage());
    respond(502, ['error' => 'The assistant had a problem. Please try again.']);
} catch (APIConnectionException $e) {
    error_log('Claude connection error: ' . $e->getMessage());
    respond(502, ['error' => 'Could not reach the assistant. Please try again.']);
}

if ($message->stopReason === 'refusal') {
    respond(200, ['reply' => "Sorry, I can't help with that. Is there something else I can do for you?"]);
}

// 6. Collect the text blocks and send them back to the browser.
$reply = '';
foreach ($message->content as $block) {
    if ($block->type === 'text') {
        $reply .= $block->text;
    }
}

respond(200, ['reply' => trim($reply) !== '' ? trim($reply) : 'Sorry, I have no answer for that.']);
