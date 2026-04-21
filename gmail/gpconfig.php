<?php
session_start();

define('GOOGLE_CLIENT_ID', '15971065731-qoj802f5ae0o41apitf29qsor8brofgj.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost:80/SIGCA/gmail/callback.php');

/*
Scopes comunes.
openid email profile = autenticación básica del usuario.
*/
define('GOOGLE_SCOPE', 'openid email profile');