<?php

require_once 'includes/global.inc.php';
$page = "myclass.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;

if (isset($_POST['submit'])) {
    $display = 1;
    $cont = $db->select_fs('visits', "date = '" . $_POST['date'] . "'");
}

$user = unserialize($_SESSION['user']);

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Список отсутствующих | <?php echo $pname; ?></title>
</head>
<body>
<center><br>
    <?php if ($display == 0) : ?>
        <form class="md-form border border-light p-5" action="not_visited.php" method="post">
            <p class="h4 mb-4 text-center">Введите дату:</p>
            <input type="date" id="inputMDEx" class="form-control" name="date">
            <button class="btn btn-info btn-block" type="submit" name="submit">Выбрать</button>
        </form>
    <?php else : ?>
        <br>
    <h3>Список отсутствующих на <?php echo date("d.m.Y", strtotime($_POST['date'] . " GMT")) ?></h3></center>
        <br><br>
        <?php
            $grs = $db->select_fs('groups', "id != '0'");
            foreach($grs as $group){
                echo '<h4><u>'.$group['name'].'</u></h4>';
                echo '<ul>';
                foreach($cont as $usv){
                    $usvr = $db->select('users', "username = '".$usv['eis_id']."'");
                    if($usvr['group_id'] == $group['id']){
                        echo '<li>'.$usvr['f'].' '.$usvr['i'].' '.$usvr['o'].' (пропущено '.$usv['hrs'].' часов по ';
                        if($usv['reason'] == '0') echo 'неустановленной причине)</li>';
                        else if($usv['reason'] == '1') echo 'болезни)</li>';
                        else if($usv['reason'] == '2') echo 'заявлению от родителей)</li>';
                        else if($usv['reason'] == '3') echo 'участию в мероприятии)</li>';
                    }
                }
                echo '</ul><br>';
            }
        ?>
    <?php endif ?>
    <?php require_once 'includes/footer.inc.php'; ?>
</body>
</html>