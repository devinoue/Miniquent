<?php

require_once "vendor/autoload.php";

use Miniquent\Person;


$db = new Person();



$st = $db->pagination(3);//全てのデータを取得
$get_all = $st->get();
var_dump($get_all);
$st->links();

// 削除
$del = Person::where('name','太郎');
//$del->delete();

// スコアが40以上での二人を降順で表示する
// $users = Person::where('score','>','40')->limit(2)->orderBy('score','desc')->get();
$users = Person::where('score','>','40')->orWhere('name','like','%は%')->orderBy('score','desc')->get();
var_dump($users);

