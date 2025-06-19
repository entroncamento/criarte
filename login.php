<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Login - Criarte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0c3fc 0%, #fde2e4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-wrapper {
            display: flex;
            width: 700px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .left-panel {
            width: 50%;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .left-panel img {
            max-width: 100%;
        }

        .right-panel {
            width: 50%;
            padding: 40px;
        }

        .right-panel h2 {
            color: #5a00a1;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-login {
            background-color: #5a00a1;
            color: white;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="left-panel">
            <img src="imgs/login.png" alt="Login Illustration">
        </div>
        <div class="right-panel">
            <h2>Login</h2>
            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                unset($_SESSION['register_success']);
            }
            ?>
            <form action="actions/login_action.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="login" class="form-control" placeholder="Nome de utilizador" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-login">Login</button>
            </form>
        </div>
    </div>
</body>

</html>