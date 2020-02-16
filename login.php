<?php
//login.php
require_once 'includes/global.inc.php';
$page = "login.php";
require_once 'includes/header.inc.php';
$error = "";
$username = "";
$password = "";

//проверить отправлена ли форма логина
if (isset($_POST['submit-login'])) {

    $username = $_POST['login'];
    $password = $_POST['password'];

    $userTools = new UserTools();
    if ($userTools->login($username, $password) == 1) {
//удачный вход, редирект на страницу
        header("Location: index.php");
    } else if ($userTools->login($username, $password) == 2) {
        $error = 'Аккаунт не активирован.';
    } else {
        $error = 'Неверный логин или пароль.';
    }
}
?>
<html>
<head>
    <title>Вход | <?php echo $pname; ?></title>
</head>
<body>
<main role="main">
    <?php $user = unserialize($_SESSION['user']); ?>

    <?php if (isset($_SESSION['logged_in'])) : ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка безопасности #001</strong><br>
            <p>Вы уже вошли в систему.</p>
        </div>
    <?php else : ?>
        <center>
            <form class="md-form border border-light p-5 fixed-25em" action="login.php" method="post">

                <p class="h4 mb-4 text-center">Вход</p>

                <input type="text" id="login" name="login" class="form-control mb-4 fixed-15em"
                       placeholder="ID участника системы ЕИС">

                <input type="password" id="password" name="password" class="form-control mb-4 fixed-15em"
                       placeholder="Пин-код">

                <button class="btn btn-info btn-block my-4 fixed-15em" type="submit" name="submit-login">Войти</button>

                <!--- <div class="text-center">
                    <p>Еще не зарегистрированы?
                        <a href="register.php">Зарегистрироваться</a>
                    </p>

                </div> --->
            </form>
            <?php if ($error != "") : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Ошибка</strong><br>
                <?php echo $error; ?>
                <?php endif; ?>
        </center>
    <?php endif; ?>
</main>
</body>
<?php require_once 'includes/footer.inc.php'; ?>
</html>