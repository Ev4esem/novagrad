<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('ok' => false));
    exit;
}

require __DIR__ . '/lead-config.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = array();
}

$name = isset($input['name']) ? trim($input['name']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$formType = isset($input['formType']) ? trim($input['formType']) : 'Заявка';

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(array('ok' => false));
    exit;
}

$text = "🏢 *Новая заявка с сайта NOVAGRAD*\n\n"
    . "👤 *Имя:* " . $name . "\n"
    . "📞 *Телефон:* " . $phone . "\n"
    . "📝 *Тип заявки:* " . $formType . "\n\n"
    . "⏰ _" . date('d.m.Y H:i:s') . "_";

$ch = curl_init("https://api.telegram.org/bot{$TELEGRAM_BOT_TOKEN}/sendMessage");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'chat_id' => $TELEGRAM_CHAT_ID,
    'text' => $text,
    'parse_mode' => 'Markdown',
)));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$ok = ($response !== false) && ($httpCode == 200);
curl_close($ch);

echo json_encode(array('ok' => $ok));
