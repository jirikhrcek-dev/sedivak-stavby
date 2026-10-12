<?php
// Odeslání poptávky z kontaktního formuláře přes Resend.
// Používá se jen, když je v src/components/Contact.astro FORM_SERVICE = 'resend'.
// API klíč je v odeslat-klic.php (není v gitu), soubor vypadá takhle:
//   <?php const RESEND_API_KEY = 're_...';

header('Content-Type: application/json; charset=utf-8');

const MAIL_TO = 'info@sedivakstavby.cz';
// Doména musí být v Resendu ověřená (DNS záznamy u registrátora)
const MAIL_FROM = 'Web Šedivák Stavby <web@sedivakstavby.cz>';
const ALLOWED_HOSTS = ['www.sedivakstavby.cz', 'sedivakstavby.cz'];

function fail(int $code, string $msg): void {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $msg]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail(405, 'Jen POST');

// Formulář smí posílat jen vlastní web
$origin = parse_url($_SERVER['HTTP_ORIGIN'] ?? '', PHP_URL_HOST);
if (!in_array($origin, ALLOWED_HOSTS, true)) fail(403, 'Cizí původ');

$keyFile = __DIR__ . '/odeslat-klic.php';
if (!is_file($keyFile)) fail(500, 'Chybí klíč');
require $keyFile;

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) fail(400, 'Špatná data');

// Past na roboty – skryté pole, člověk ho nevyplní
if (!empty($data['web'])) { echo json_encode(['ok' => true]); exit; }

$name    = trim(mb_substr((string)($data['from_name'] ?? ''), 0, 120));
$email   = trim(mb_substr((string)($data['from_email'] ?? ''), 0, 200));
$phone   = trim(mb_substr((string)($data['phone'] ?? ''), 0, 40));
$message = trim(mb_substr((string)($data['message'] ?? ''), 0, 5000));

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  fail(422, 'Chybí jméno, e-mail nebo zpráva');
}

// Jednoduchý limit: z jedné IP max. 5 poptávek za hodinu
$ipFile = sys_get_temp_dir() . '/sedivak-form-' . md5($_SERVER['REMOTE_ADDR'] ?? '');
$hits = array_filter(
  is_file($ipFile) ? (array)json_decode(file_get_contents($ipFile), true) : [],
  fn($t) => $t > time() - 3600
);
if (count($hits) >= 5) fail(429, 'Příliš mnoho zpráv');
$hits[] = time();
file_put_contents($ipFile, json_encode(array_values($hits)));

$e = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$html = '<h2>Nová poptávka z webu</h2>'
  . '<p><b>Jméno:</b> ' . $e($name) . '<br>'
  . '<b>E-mail:</b> ' . $e($email) . '<br>'
  . '<b>Telefon:</b> ' . ($phone !== '' ? $e($phone) : '–') . '</p>'
  . '<p><b>Zpráva:</b><br>' . nl2br($e($message)) . '</p>';

$payload = json_encode([
  'from'     => MAIL_FROM,
  'to'       => [MAIL_TO],
  'reply_to' => $email,
  'subject'  => 'Poptávka z webu – ' . $name,
  'html'     => $html,
]);

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, [
  CURLOPT_POST           => true,
  CURLOPT_POSTFIELDS     => $payload,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT        => 15,
  CURLOPT_HTTPHEADER     => [
    'Authorization: Bearer ' . RESEND_API_KEY,
    'Content-Type: application/json',
  ],
]);
curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status < 200 || $status >= 300) fail(502, 'Resend odmítl e-mail');

echo json_encode(['ok' => true]);
