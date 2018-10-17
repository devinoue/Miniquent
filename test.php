<?php


require_once('miniquent.php');



$db = new Miniquent();
$db->column[]="name";
$get_all = $db->get();
// $get_all = $db->pagination(5)->get();//全てのデータを取得
var_dump($get_all);

// 削除
$del = Miniquent::where('name','=','太郎');
//$del->delete();

// スコアが40以上での二人を降順で表示する
//$users = Miniquent::where('score','>','40')->limit(2)->orderBy('score','desc')->get();
//var_dump($users);

print<<<EOF
<html>
<head>
	<title>タイトル</title>
</head>
<body>
	<h1></h1>

</body>
</html>

EOF;

