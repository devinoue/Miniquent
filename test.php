<?php

require_once('Person.php');


$db = new Person();

$db->links();

// $db->column[]="name";
// $get_all = $db->get();
$get_all = $db->pagination(5)->get();//全てのデータを取得
var_dump($get_all);

// 削除
$del = Person::where('name','太郎');
//$del->delete();

// スコアが40以上での二人を降順で表示する
// $users = Person::where('score','>','40')->limit(2)->orderBy('score','desc')->get();
$users = Person::where('score','>','40')->orWhere('name','like','%は%')->orderBy('score','desc')->get();
var_dump($users);

