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
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <img src="img/main.jpg" class="img-fluid z-depth-1">
            </div>
            <div class="col-md-8">

                <h1>Единая информационная система</h1>
                <h3><?php echo $tool->getGlobal('org') ?></h3>

                <small><?php echo $tool->getGlobal('org_full') ?></small>
                <br>
                <small>Уполномоченный специалист: <?php echo $tool->getGlobal('upoln') ?></small>
            </div>
        </div>
    </div>
    <br><br>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body"><h4 class="card-title">Зачисление в 1 класс</h4>
                        <hr>
                        <p class="card-text">Проверить статус заявления на зачисление ребёнка в 1 класс</p>
                        <hr>
                        <a href="zachisleniye.php" class="black-text d-flex justify-content-end"><h5>Перейти <i
                                        class="fas fa-angle-double-right"></i></h5></a></div>
                </div>
                <br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body"><h4 class="card-title">Вход для педагогов</h4>
                        <hr>
                        <p class="card-text">Войти в административный блок Единой информационной системы</p>
                        <hr>
                        <a href="login.php" class="black-text d-flex justify-content-end"><h5>Перейти <i
                                        class="fas fa-angle-double-right"></i></h5></a></div>
                </div>
                <br>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body"><h4 class="card-title">Новости системы</h4>
                        <hr>
                        <p class="card-text">Ознакомиться с изменениями в системе и новостями Лицея</p>
                        <hr>
                        <a href="news.php" class="black-text d-flex justify-content-end"><h5>Перейти <i
                                        class="fas fa-angle-double-right"></i></h5></a></div>
                </div>
                <br>
            </div>
        </div>
    </div>
    <!---
    <br>
    <h3>Новости системы:</h3>
    <div class="row">
        <?php
    $news = $db->select_desc_fs_news("news", "display = '1'");
    foreach ($news as $article) {
        echo '<div class="col-md-4"><div class="card"><div class="view overlay">';
        echo '</div>';
        echo '<div class="card-body"><h4 class="card-title">' . $article['header'] . '</h4><hr>';
        echo '<p class="card-text">' . $article['text'] . '</p>';
        echo '<hr>' . $article['footer'];
        echo '<a href="#!" class="black-text d-flex justify-content-end"><h5>Подробнее <i class="fas fa-angle-double-right"></i></h5></a></div></div></div>';
    }
    ?>
    </div>
    --->
</center>
</body>
</html>