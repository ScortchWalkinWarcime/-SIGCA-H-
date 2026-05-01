<?php
require_once 'gpConfig.php';
require_once '../database/database.php';

if (isset($_GET['error'])) {
    exit('Error de Google: ' . htmlspecialchars($_GET['error']));
}

if (!isset($_GET['state']) || !hash_equals($_SESSION['google_oauth_state'] ?? '', $_GET['state'])) {
    exit('Estado inválido (posible CSRF).');
}

if (!isset($_GET['code'])) {
    exit('No se recibió código de autorización.');
}

$tokenUrl = 'https://oauth2.googleapis.com/token';

$postFields = [
    'code' => $_GET['code'],
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    exit('Error cURL: ' . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($httpCode !== 200 || isset($data['error'])) {
    echo '<pre>';
    print_r($data);
    exit('Error obteniendo token.');
}

/*
$data tendrá normalmente:
- access_token
- expires_in
- token_type
- scope
- refresh_token (a veces, no siempre)
*/

$accessToken = $data['access_token'] ?? null;

if (!$accessToken) {
    exit('No se recibió access_token.');
}

/* Obtener datos del usuario */
$ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);

$userResponse = curl_exec($ch);

if (curl_errno($ch)) {
    exit('Error cURL userinfo: ' . curl_error($ch));
}

$userHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$userData = json_decode($userResponse, true);

if ($userHttpCode !== 200 || empty($userData['email'])) {
    echo '<pre>';
    print_r($userData);
    exit('No se pudo obtener el correo del usuario.');
}

/* Datos básicos */
$googleId = $userData['id'] ?? '';
$email = $userData['email'] ?? '';
$name = $userData['name'] ?? '';
$picture = $userData['picture'] ?? '';

// Verificar si el usuario ya existe
$sql = "SELECT cve_usuario, nombre FROM usuario WHERE correo = :correo LIMIT 1";
$user = Database::query($sql, [':correo' => $email]);

if (!$user) {
    // Usuario no existe, redirigir a registro con datos de Google
    $_SESSION['google_user_data'] = [
        'google_id' => $googleId,
        'email' => $email,
        'name' => $name,
        'picture' => $picture
    ];
    header('Location: ../backend/registro_google.php');
    exit();
} else {
    // Usuario existe, hacer login
    $userId = $user[0]['cve_usuario'];
    $userName = $user[0]['nombre'];
    $roleQuery = "SELECT rol FROM usuario WHERE cve_usuario = :userId LIMIT 1";
    $roleResult = Database::query($roleQuery, [':userId' => $userId]);
    $userRole = $roleResult ? strtolower($roleResult[0]['rol']) : 'user';
    
    $_SESSION['n_usuario'] = $userName;
    $_SESSION['user'] = [
        'google_id' => $googleId,
        'email' => $email,
        'name' => $userName,
        'picture' => $picture,
        'cve_usuario' => $userId,
        'rol' => $userRole
    ];
    
    header('Location: ../backend/login_success.php');
    exit();
}