<?php

require_once 'includes/global.inc.php';
$page = "print.php";

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
}
$user = unserialize($_SESSION['user']);

if ($user->admin < 1) {
    header("Location: access_denied.php");
}

$link = "0";
$docname = "";

if(isset($_GET['sysdoc'])){
    if($_GET['sysdoc'] == "1"){
        $docname = "Соглашение об обработке ПД (стандартная форма)";
        $doc = $db->select('pdata_docs', "id = '".$_GET['id']."'");
        $usr = $db->select('users', "id = '".$doc['user_id']."'");
        $gr = $db->select('groups', "id = '".$usr['group_id']."'");
        $document = new \PhpOffice\PhpWord\TemplateProcessor("/print/sys/1/template.docx");
        $document->setValue('docid', $_GET['id']);
        $document->setValue('id', $usr['id']);
        $document->setValue('fio', $usr['f'].' '.$usr['i'].' '.$usr['o']);
        $document->setValue('group', $gr['name']);
        $document->saveAs($_SERVER['DOCUMENT_ROOT'] . "/print/sys/1/" . $_GET['id'] . ".docx");
        $link = 'http://' . $_SERVER['SERVER_NAME'] . "/print/sys/1/" . $_GET['id'] . ".docx";
    }
}

require_once 'includes/header.inc.php';
?>
<html>
<head>
    <title>Печать документа | <?php echo $pname; ?></title>
    <?php require_once 'includes/footer.inc.php'; ?>
</head>
<body>
    <br>
    <div class="jumbotron">
        <h2 class="display-4">Модуль печати документов</h2>
        <hr class="my-4">
        <?php if ($link == "0") : ?>
        <p class="lead">Запрос на печать не был отправлен.</p>
        <?php else : ?>
        <p class="lead">Документ "<?php echo $docname; ?>" успешно изготовлен.</p>
        <a href="<?php echo $link; ?>" target="_blank">Скачать документ</a>
        <?php endif ?>
    </div>
</body>
</html>
