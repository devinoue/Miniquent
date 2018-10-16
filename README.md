# Miniquentとは？
Laravelが使用しているO/Rマッパー・Eloquentモデルを真似て作られた、最小構成バージョンEloquentライクなライブラリです。

使用感はEloquentそっくりなので、Laravelに慣れていて「DB操作が必要なちょっとしたサービスを作りたい」という人にぴったりです。

# 使用法
## 初期設定
まずはデータベースの情報をconfig.phpに書いてください。必要なのはデータベース名、ユーザー名、パスワード、テーブル名です。

## 新規登録。

ユーザーデータの新規登録は、Eloquentとほぼ同じです。
例えばid、name、score、ageというカラムを持つテーブルがあるとして

```php
$user = new Miniquent;
$user->name = '山田太郎';
$user->score = 78;
$user->age = 24;
$user->save(); 
```

で新規登録できます。

## 更新

更新もEloquentモデルと同じです。
(ただfindメソッドなどはまだ未実装)

```php
$user = Miniquent::where('id',4);
$user->score = 32;
$user->save();
```


## 表示
例えばscoreが30以上のユーザーを表示したいときは……

```php
$users = Miniquent::where('score','>','30');
```

でOK。


## 削除
scoreが30未満の人だけ削除したいなら……


```php
Miniquent::where('score','<','30');
```


## メソッドチェーン
Eloquentモデル同様に、メソッドチェーンも可能です。
たとえばscoreが30以上の人を二人、スコアで降順に抽出したいなら……

```php
$users = DB::where('score','>','40')->limit(2)->orderBy('score','desc')->get();
```

**最後のget()は必要なので、忘れないでください。**
(私もLaravelでよく忘れるので)



# ORMとしての価値
現在の所色々未実装な部分もあるので、Eloquentモデルほど高機能ではありませんが、「とりあえずサクッとDBを使いたい」という方にオススメです。

