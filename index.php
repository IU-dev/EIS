<?php
//index.php 
require_once 'includes/global.inc.php';
$page = "index.php";
require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Главная | <?php echo $pname; ?></title>
    <?php require_once 'includes/footer.inc.php'; ?>
</head>
<body>
<center>
    <br>
    <h1>Единая информационная система</h1>
    <h3><?php echo $tool->getGlobal('org') ?></h3>
    <small>Полное наименование организации: <?php echo $tool->getGlobal('org_full') ?></small><br>
    <small>Уполномоченный специалист: <?php echo $tool->getGlobal('upoln') ?></small><br><br>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="alert alert-info" role="alert">
                    <?php if(isset($_SESSION['logged_in'])) : ?>
                    <h3>Добро пожаловать, <?php echo $user->i.' '.$user->o; ?>!</h3>
                    <?php else : ?>
                        <h3>Добро пожаловать!</h3>
                    <?php endif ?>
                    Текущая версия системы: R1.6 U2<br>
                    <?php
                    if (isset($_SESSION['user'])) {
                        $user = unserialize($_SESSION['user']);
                        $nots = $db->select_desc_fs('logs', "userid = '" . $user->id . "' AND displayed = '0'");
                        $k = count($nots);
                        if ($k > 0) {
                            echo '<script>';
                            echo "toastr.info('У вас есть " . $k . " новых сообщений. Проверьте их в личном кабинете.', 'Уведомления', {timeOut: 5000});";
                            echo '</script>';
                            echo "<br>У вас есть " . $k . " новых сообщений. Проверьте их в личном кабинете.";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <?php if(isset($_SESSION['logged_in'])) : ?>
                    <div class="alert alert-primary" role="alert">
                        <h4>Быстрые действия:</h4>
                        <a href="myclass.php">Посмотреть списки классов</a>
                    </div>
                <?php else : ?>
                <div class="alert alert-primary" role="alert">
                    <h4>Вы не авторизованы!</h4>
                    Чтобы начать работу в системе, пройдите авторизацию.<br>
                    <a href="login.php" class="btn btn-primary">Войти в систему</a>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <br>
    <h3>Важная информация:</h3>
    <?php
    $news = $db->select_desc_fs_news("news", "display = '1'");
    foreach ($news as $article) {
        echo '<div class="alert alert-' . $article['colortype'] . '" role="alert">';
        echo '<h4 class="alert-heading">' . $article['header'] . '</h4>';
        echo '<p>' . $article['text'] . '</p>';
        echo '<hr><p class="mb-0">' . $article['footer'] . '</p></div>';
    }
    ?>
</center>
</body>
</html>