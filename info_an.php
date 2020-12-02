<?php

require_once 'includes/global.inc.php';
$page = "info_an.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}

$display = 0;
$rt = false;

$user = unserialize($_SESSION['user']);

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

if (isset($_POST['submit'])) {
    $flds = $_POST['fld'];
    foreach ($flds as $key => $fld) {
        $check = $db->select('pdata', "eis_id = '" . $_POST['uid'] . "' AND field_id = '" . $key . "'");
        if (isset($check['data'])) {
            $data['data'] = "'" . str_replace('"', '', $fld) . "'";
            $data['last_update_by'] = "'0'";
            $data['last_update_datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            $ib = $db->update($data, 'pdata', "id = '" . $check['id'] . "'");
        } else {
            $data['eis_id'] = "'" . $_POST['uid'] . "'";
            $data['field_id'] = "'" . $key . "'";
            $data['data'] = "'" . str_replace('"', '', $fld) . "'";
            $data['last_update_by'] = "'0'";
            $data['last_update_datetime'] = "'" . date("Y-m-d H:i:s", time()) . "'";
            $ib = $db->insert($data, 'pdata');
        }
    }
    $msg = "Внесение данных произведено успешно.";
    $rt = true;
}

if ($rt == false) {
    if (!isset($_GET['id'])) {
        die('Отсутствует UID.');
    } else {
        $uid = $_GET['id'];
        $usr = $userTools->get($uid);
    }

    if (!isset($_GET['gid'])) {
        die('Отсутствует ID группы внесения.');
    } else {
        $gr = $db->select('pdata_groups', "id = '" . $_GET['gid'] . "'");
        $fields = json_decode($gr['value']);
    }
}

require_once 'includes/header.inc.php';

?>
<html>
<head>
    <title>Внесение данных | <?php echo $pname; ?></title>
</head>
<body>
<br>
<?php if (isset($msg)) echo "<h3>" . $msg . "</h3>"; ?>
<?php if ($rt == false) : ?>
    <form class="md-form border border-light p-5" action="info_an.php" method="post">
        <p class="h4 mb-4 text-center">Ввод персональных данных в систему</p>
        Субъект ПД:
        <input type="text" id="textInput" name="field_name" class="form-control mb-4" placeholder=""
               value="<?php echo "(" . $usr->id . ") " . $usr->f . " " . $usr->i . " " . $usr->o ?>" disabled>
        <?php
        foreach ($fields as $field) {
            $fd = $db->select('pdata_fields', "id = '" . $field . "'");
            echo $fd['name'];
            $check = $db->select('pdata', "eis_id = '" . $uid . "' AND field_id = '" . $fd['id'] . "'");
            if (isset($check['data'])) echo '<input type="text" id="textInput" value="' . $check['data'] . '" name="fld[' . $fd['id'] . ']" class="form-control mb-4" placeholder="">';
            else echo '<input type="text" id="textInput" name="fld[' . $fd['id'] . ']" class="form-control mb-4" placeholder="">';
        }
        ?>
        <input type="hidden" name="uid" value="<?php echo $_GET['id']; ?>">


        <button class="btn btn-info btn-block" type="submit" name="submit">Внести данные</button>
    </form>
<?php endif ?>
</body>
</html>