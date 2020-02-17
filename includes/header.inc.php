<?php $user = unserialize($_SESSION['user']);
?>
<nav class="mb-1 navbar navbar-expand-lg navbar-dark primary-color fixed-top">
    <a class="navbar-brand" href="#"><?php echo $pname; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-333"
            aria-controls="navbarSupportedContent-333" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent-333">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?php echo($page == "index.php" ? "active" : ""); ?>">
                <a class="nav-link" href="index.php">Главная
                </a>
            </li>
            <?php if (isset($user->username)) : ?>
            <li class="nav-item dropdown <?php echo($page == "show.php" || $page == "visits.php" || $page == "delay.php" ? "active" : ""); ?>">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Мои действия
                </a>
                <div class="dropdown-menu dropdown-default" aria-labelledby="navbarDropdownMenuLink-333">
                    <a class="dropdown-item" href="show.php">Логины и пароли</a>
                    <a class="dropdown-item" href="visits.php">Посещаемость</a>
                    <?php if ($user->admin == 9 || $user->admin == 2) : ?>
                        <hr>
                        <a class="dropdown-item" href="delay.php">Отметить опоздавших</a>
                    <?php endif ?>
                </div>
            </li>
            <?php if ($user->admin == "1" || $user->admin == "2") : ?>
            <li class="nav-item dropdown <?php echo($page == "myclass.php" || $page == "p_add.php" || $page == "p_del.php" || $page == "not_visited.php" ? "active" : ""); ?>">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">Действия педагогов
                </a>
                <div class="dropdown-menu dropdown-default" aria-labelledby="navbarDropdownMenuLink-333">
                <h6 class="dropdown-header">Просмотр</h6>
                <a class="dropdown-item" href="myclass.php">Списки классов</a>
                <a class="dropdown-item" href="not_visited.php">Отсутствующие</a>
                <div class="dropdown-divider"></div>
                <h6 class="dropdown-header">Мониторинги</h6>
                <a class="dropdown-item" href="mon_list.php">Список мониторингов</a>
                <?php if ($user->admin == 2) : ?>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Особые действия</h6>
                    <a class="dropdown-item" href="p_add.php">Добавить</a>
                    <a class="dropdown-item" href="p_del.php">Удалить</a>
                    </div>
                    </li>
                <?php endif ?>
            <?php endif ?>
            <?php if ($user->admin == "2") : ?>
                <li class="nav-item dropdown <?php echo($page == "a_create.php" || $page == "a_flush.php" || $page == "a_give_solo.php" || $page == "a_give_group.php" || $page == "a_delete.php" ? "active" : ""); ?>">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">Администратор
                    </a>
                    <div class="dropdown-menu dropdown-default" aria-labelledby="navbarDropdownMenuLink-333">
                        <h6 class="dropdown-header">Обработка заявок</h6>
                        <a class="dropdown-item" href="a_create.php">Создание УЗ</a>
                        <a class="dropdown-item" href="a_flush.php">Сброс пароля</a>
                        <h6 class="dropdown-header">Работа с аккаунтами</h6>
                        <a class="dropdown-item" href="a_give_solo.php">Выдача одному</a>
                        <a class="dropdown-item" href="a_give_group.php">Выдача группе</a>
                        <a class="dropdown-item" href="a_delete.php">Удаление аккаунтов</a>
                    </div>
                </li>
            <?php endif ?>
    </div>
    </li>
    <?php endif ?>
    </ul>
    <ul class="navbar-nav ml-auto nav-flex-icons">
        <?php if (isset($user->username)) : ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-default"
                     aria-labelledby="navbarDropdownMenuLink-333">
                    <span class="dropdown-item disabled"><?php echo $user->f . " " . $user->i . " " . $user->o; ?></span>
                    <span class="dropdown-item disabled"><?php echo "ID: " . $user->id ?></span>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="settings.php">Личный кабинет</a>
                    <a class="dropdown-item" href="logout.php">Выход</a>
                </div>
            </li>
        <?php else : ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    Вход в систему
                    <i class="fas fa-user"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-default"
                     aria-labelledby="navbarDropdownMenuLink-333">
                    <a class="dropdown-item " href="login.php">Вход</a>
                    <!---<a class="dropdown-item" href="register.php">Регистрация</a>
                     <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="recovery.php">Восстановление</a> --->
                </div>
            </li>
        <?php endif ?>
    </ul>
    </div>
</nav>