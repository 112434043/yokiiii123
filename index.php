<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'campus_bites');

$msg = "";
$msg_type = "error";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Please enter a valid email address!";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $pass)) {
            $msg = "Password does not meet the security requirements!";
        } else {
            $hashed = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            if ($stmt->execute()) {
                $msg = "Registration successful! You can now log in.";
                $msg_type = "success";
            } else {
                $msg = "This email address is already registered!";
            }
        }
    } elseif ($_POST['action'] === 'login') {
        $email = trim($_POST['email']);
        $pass = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($user = $res->fetch_assoc()) {
            if (password_verify($pass, $user['password'])) {
                $_SESSION['user_name'] = $user['name'];
                header("Location: home.html");
                exit;
            } else {
                $msg = "Incorrect Password!";
            }
        } else {
            $msg = "User account not found!";
        }
    } elseif ($_POST['action'] === 'reset_password') {
        $email = trim($_POST['email']);
        $new_pass = $_POST['new_password'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->fetch_assoc()) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_pass)) {
                $msg = "New password does not meet the security requirements!";
            } else {
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $hashed, $email);
                if ($update_stmt->execute()) {
                    $msg = "Password updated successfully in database! Please log in.";
                    $msg_type = "success";
                } else {
                    $msg = "Failed to update password. Try again!";
                }
            }
        } else {
            $msg = "User with this email was not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Bites - Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.75)), 
                        url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1600&auto=format&fit=crop') no-repeat center center/cover;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            border-top: 5px solid #e65100;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .brand-header h1 {
            color: #d84315;
            font-size: 28px;
            font-weight: 700;
        }

        .brand-header p {
            color: #666;
            font-size: 13px;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #e65100;
            outline: none;
            box-shadow: 0 0 0 3px rgba(230, 81, 0, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #e65100, #ff8f00);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 4px 12px rgba(230, 81, 0, 0.3);
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(230, 81, 0, 0.4);
        }

        .links-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .link-text {
            color: #e65100;
            text-decoration: none;
            cursor: pointer;
            font-weight: 500;
        }

        .link-text:hover {
            text-decoration: underline;
        }

        .strength-meter {
            height: 5px;
            width: 100%;
            background: #e0e0e0;
            border-radius: 4px;
            margin-top: 6px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .strength-label {
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
            display: block;
            text-align: right;
        }

        .alert-box {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: center;
            font-weight: 500;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="brand-header">
        <h1>Campus Bites</h1>
        <p>Pre-Order Your Favourite Food & Skip the Queue</p>
    </div>

    <?php if ($msg): ?>
        <div class="alert-box <?php echo ($msg_type === 'success') ? 'alert-success' : 'alert-error'; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="loginForm" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="student@college.edu" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-submit">Sign In</button>
        <div class="links-row">
            <span class="link-text" onclick="showForm('forgotForm')">Forgot Password?</span>
            <span class="link-text" onclick="showForm('registerForm')">Create Account</span>
        </div>
    </form>

    <!-- Register Form -->
    <form id="registerForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="John Doe" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="student@college.edu" required>
        </div>
        <div class="form-group">
            <label>Strong Password</label>
            <input type="password" name="password" id="regPassword" placeholder="Min 8 chars, uppercase, number, symbol" required oninput="evaluatePassword(this.value, 'strengthBar', 'strengthLabel')">
            <div class="strength-meter">
                <div id="strengthBar" class="strength-bar"></div>
            </div>
            <span id="strengthLabel" class="strength-label"></span>
        </div>
        <button type="submit" class="btn-submit">Sign Up</button>
        <div class="links-row" style="justify-content: center;">
            <span class="link-text" onclick="showForm('loginForm')">Already have an account? Sign In</span>
        </div>
    </form>

    <!-- Forgot Password Form (Direct Database Password Reset) -->
    <form id="forgotForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="reset_password">
        <div class="form-group">
            <label>Registered Email</label>
            <input type="email" name="email" placeholder="student@college.edu" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" id="resetPassword" placeholder="Min 8 chars, uppercase, number, symbol" required oninput="evaluatePassword(this.value, 'resetStrengthBar', 'resetStrengthLabel')">
            <div class="strength-meter">
                <div id="resetStrengthBar" class="strength-bar"></div>
            </div>
            <span id="resetStrengthLabel" class="strength-label"></span>
        </div>
        <button type="submit" class="btn-submit">Reset & Update Password</button>
        <div class="links-row" style="justify-content: center;">
            <span class="link-text" onclick="showForm('loginForm')">Back to Sign In</span>
        </div>
    </form>
</div>

<script>
function showForm(formId) {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'none';
    document.getElementById('forgotForm').style.display = 'none';
    document.getElementById(formId).style.display = 'block';
}

function evaluatePassword(pass, barId, labelId) {
    const bar = document.getElementById(barId);
    const label = document.getElementById(labelId);
    let score = 0;

    if (pass.length >= 8) score++;
    if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) score++;
    if (/\d/.test(pass)) score++;
    if (/[@$!%*?&]/.test(pass)) score++;

    if (pass.length === 0) {
        bar.style.width = '0%';
        label.innerText = '';
    } else if (score <= 2) {
        bar.style.width = '33%';
        bar.style.background = '#e53935';
        label.style.color = '#e53935';
        label.innerText = 'Weak Password';
    } else if (score === 3) {
        bar.style.width = '66%';
        bar.style.background = '#fb8c00';
        label.style.color = '#fb8c00';
        label.innerText = 'Medium Password';
    } else {
        bar.style.width = '100%';
        bar.style.background = '#43a047';
        label.style.color = '#43a047';
        label.innerText = 'Strong Password';
    }
}
</script>
</body>
</html>