<?php

require_once 'includes/global.inc.php';
$page = "myclass.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

if (isset($_POST['submit'])) {
    $display = 1;
    $cont = $db->select('groups', "id = '" . $_POST['section'] . "'");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Список участников | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <?php if ($display == 0) : ?>
        <form class="md-form border border-light p-5" action="myclass.php" method="post">
            <p class="h4 mb-4 text-center">Выберите группу обучающихся</p>
            <select class="browser-default custom-select mb-4" id="select" name="section">
                <?php
                if($user->admin == 2) $sections = $db->select_fs('groups', "id != '0'");
                else $sections = $db->select_fs('groups', "curator_id = '".$user->username."'");
                foreach ($sections as $section) {
                    $cur = $db->select('users', "username = '".$section['curator_id']."'");
                    echo '<option value="' . $section['id'] . '">' . "(" . $section['id'] . ") " . $section['name'] . ' (куратор ' . $cur['f'] . ' ' . $cur['i'] . ' ' . $cur['o'] . ' (ЕИС-'. $cur['username'] . '))</option>';
                }
                ?>
            </select>
            <button class="btn btn-info btn-block" type="submit" name="submit">Выбрать</button>
        </form>
    <?php else : ?>
    <h3>Список группы</h3>
    <br><br>
</center>
<div class="card card-cascade narrower">
    <div
            class="view view-cascade gradient-card-header blue-gradient narrower py-2 mx-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
        </div>
        <a href="" class="white-text mx-3"><?php echo "(" . $cont['id'] . ") " . $cont['name']; ?></a>

        <div>
        </div>
    </div>
    <div class="px-4">
        <div class="table-wrapper">
            <?php
            echo '<table id="participants" class="table table-sm table-hover">' .
                '<thead>' .
                '<tr>' .
                '<th>№</th>' .
                '<th>ЕИС</th>' .
                '<th>ФИО участника</th>' .
                '<th>Действие</th>' .
                '</tr>' .
                '</thead>';
            $parts = $db->select_fs('users', "group_id = '" . $cont['id'] . "'");
            $i = 1;
            foreach ($parts as $part) {
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $part['username'] . '</td>';
                echo '<td>' . $part['f'] . ' ' . $part['i'] . ' ' . $part['o'] . '</td>';
                echo '<td>Нет доступных действий.</td>';
                $i = $i + 1;
            }
            echo '</table>';
            ?>
        </div>
    </div>
    <?php require_once 'includes/footer.inc.php'; ?>
    <script>
        $(document).ready(function () {
            $('#participants').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });

    </script>
    <?php endif ?>
</body>
</html>