<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

$TELEGRAM_BOT_TOKEN = '8289760567:AAEzXSQxW331Jykl9LIWXNxAVc-MCvJ73gE';
$TELEGRAM_CHAT_ID = '312780961';

$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$formType = trim($input['formType'] ?? 'Заявка');

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$text = "🏢 *Новая заявка с сайта NOVAGRAD*\n\n"
    . "👤 *Имя:* " . $name . "\n"
    . "📞 *Телефон:* " . $phone . "\n"
    . "📝 *Тип заявки:* " . $formType . "\n\n"
    . "⏰ _" . date('d.m.Y H:i:s') . "_";

$ch = curl_init("https://api.telegram.org/bot{$TELEGRAM_BOT_TOKEN}/sendMessage");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode([
        'chat_id' => $TELEGRAM_CHAT_ID,
        'text' => $text,
        'parse_mode' => 'Markdown',
    ]),
]);
$response = curl_exec($ch);
$ok = $response !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
curl_close($ch);

echo json_encode(['ok' => $ok]);
