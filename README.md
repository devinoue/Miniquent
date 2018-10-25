<p align="center">
<img src="https://my-portfolio.site/images/github/miniquent.jpg" alt="miniquent" />
</p>

# Miniquentとは？
Laravelが使用しているO/Rマッパー・Eloquentモデルを真似て作られた、最小構成バージョンEloquentライクなライブラリです。

使用感はEloquentそっくりなので、Laravelに慣れていて「DB操作が必要なちょっとしたサービスを作りたい」という人にぴったりです。

# 使用法
## 初期設定
まずはデータベースの情報をconfig.phpに書いてください。
データベース名、ユーザー名、パスワードで、その他の情報は必要に応じて書き加えてください。

次にComposerでvendorディレクトリをinstallします。
```bash
composer install
```

## Miniquentクラスを継承する
同梱しているPerson.phpを開いてください。
Miniquentクラスを継承しつつも、書き加えているのは`$table`だけです。
Miniquentクラスは子クラスに一つのテーブルを割り当てて使用します。

**クラス一つ = テーブル一つ**

として管理していきます。
今回は`Person`というクラスを作っていますが、名前は好きにつけても大丈夫です。
`test.php`に使用例を記述していますが、Miniquentの子クラスを使用するために、以下の宣言を書いてください。

```PHP
require_once "vendor/autoload.php";

use Miniquent\Person;
```

## 新規登録

ユーザーデータの新規登録は、Eloquentとほぼ同じです。
例えばid、name、score、ageというカラムを持つテーブルがあるとして

```php
$user = new Miniquent;
$user->name = '山田太郎';
$user->score = 78;
$user->age = 24;
$user->save(); 
```

これで新規登録できます。

## 更新

更新もEloquentモデルと同じです。

```php
$user = Miniquent::where('id',4);
$user->score = 32;
$user->save();
```

`find`メソッドを使えば、自動的に`id`というカラムと結び付けられて使用できます。
```PHP
$user = Miniquent::find(4);
$user->score = 32;
$user->save();
```
もしもid以外のカラムを使用したいときは、
継承する子クラスに`$primaryKey`プロパティを書き加えてください。


```PHP
public $primaryKey = 'name';
```
これで`find`メソッドの引数は`name`カラムと結び付けられます。


## 表示
例えばscoreが30以上のユーザーを表示したいときは……

```php
$users = Miniquent::where('score','>','30')->get();

foreach($users as $user) {
    print "名前 : $user->name スコア $user->score<br>";
}

```

でOK。get()をチェーンするのを忘れずに。

すべてのデータを取得しないなら、
```php
$users = (new Person)->all();
```
だけでOK。

## 削除
scoreが30未満の人だけ削除したいなら……


```php
$user= Miniquent::where('score','<','30');
$user->delete();
```


## メソッドチェーン
Eloquentモデル同様に、メソッドチェーンも可能です。
たとえばscoreが30以上の人を二人、スコアで降順に抽出したいなら……

```php
$users = Miniquent::where('score','>','40')->limit(2)->orderBy('score','desc')->get();
```

**最後のget()は必要なので、忘れないでください。**
(私もLaravelでよく忘れるので)


## ページネーション
ページネーションもお手軽にできますが、多少本家Eloquentモデルと異なる使用法になります。

```PHP
$users=Miniquent::where('score','>','10')->paginate(5);

foreach($users->get() as $user){
    print "名前 : $user->name スコア : $user->score<br>";
}

$users->links();
```
![完成画像](https://my-portfolio.site/images/github/miniquent_pager.png)

このようにページネーションのリンクも`links()`メソッド一つで自動的に表示してくれます。
なお、ページネーションのテンプレートはBootstrap4の構成にしていますので`src`ディレクトリの中の`page_template.php`を適宜書き換えてください。




# ORMとしての価値
現在の所、Eloquentモデルの主要な部分を抽出しています。Eloquentモデルほど高機能ではありませんが、「とりあえずサクッとDBを使いたい」という方に大変オススメです。

