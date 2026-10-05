<?php
session_start();

// Session-ஐ அழிக்க
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging Out...</title>
    <script>
        // Cart மற்றும் பயனர் விவரங்களை அழிக்க
        localStorage.clear();
        // Login பக்கமான index.php-க்கு நேரடியாகத் திருப்ப
        window.location.href = "index.php";
    </script>
</head>
<body>
    <p>Logging out, please wait...</p>
</body>
</html>