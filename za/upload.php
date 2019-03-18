<?php
if (! isset($_FILES['f'])) {
    die('<form method="post" enctype="multipart/form-data"><input name="f" type="file"><input type="submit"></form>');
}
$f = $_FILES['f'];
if (is_uploaded_file($f['tmp_name'])) {
    move_uploaded_file($f['tmp_name'], 'ludouyin.zip');
        exit('done');
} else {
    die('wtf');
}