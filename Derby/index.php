<?php
session_start();

$countPost = 0;
$path = '';
$word = '';
$btnLeft = '';
$btnCenter = '';
$btnRight = '';
$heroName = '';
$selectNames = [];
$finish_flg = false;
$crush_flg = false;

//種牡馬格納配列
$sires = array();
//繁殖牝馬格納配列
$mares = array();

//馬クラス（抽象クラス）
abstract class horse{

  //名前
  protected $name;
  //パラメータ
  protected $temper;
  protected $guts;
  protected $health;
  protected $speed;
  protected $stamina;
  //画像
  protected $img;
  //各パラメータ出力メソッド
  abstract public function getParam($str);
}

//父母馬クラス（馬クラスを継承）
class parentHorse extends horse{
  //種牡馬選択メッセージ
  const selectSir = '父馬を選んでください。<br>
                    ディープインパクトは抜群のスピードで早い時期から活躍するでしょう。<br>
                    キングカメハメハは安定した活躍ができるでしょう。<br>
                    ステイゴールドは気性次第で大活躍があるかもしれません。';
  const selectMare = '母馬を選んでください。<br>
                      エアグルーヴは抜群の根性とスタミナが自慢です。<br>
                      ビワハイジは健康で安定した成績を残せるでしょう。<br>
                      シーザリオは順調にいけばかなりの期待ができます。';
  //初期値（名前、パラメータを設定）
  public function __construct($name,$temper,$guts,$health,$speed,$stamina,$img){
    $this->name = $name;
    $this->temper = $temper;
    $this->guts = $guts;
    $this->health = $health;
    $this->speed = $speed;
    $this->stamina = $stamina;
    $this->img = $img;
  }

  //引数のパラメータを出力する
  public function getParam($str){
    return $this->$str;
  }
}
//仔馬クラス（馬クラスを継承）
class foal extends horse{
  const birthFoal = '仔馬が産まれました！<br>
                      名前を選んでください。';
  const foalname_a = 'リアルスティール';
  const foalname_b = 'ドゥラメンテ';
  const foalname_c = 'オルフェーヴル';
  protected $names = array(
    'リアルスティール','エピファネイア','サートゥルナーリア','リオンディーズ','ドゥラメンテ','ルーラーシップ','オルフェーヴル','ゴールドシップ','フェノーメノ','ブエナビスタ'
  );
  protected $sireName;
  protected $sireTemper;
  protected $sireGuts;
  protected $sireHealth;
  protected $sireSpeed;
  protected $sireStamina;
  protected $mareName;
  protected $mareTemper;
  protected $mareGuts;
  protected $mareHealth;
  protected $mareSpeed;
  protected $mareStamina;

  //初期値設定
  public function __construct($img){
    $this->img = $img;
  }
  //引数のパラメータを出力する
  public function getParam($str){
    return $this->$str;
  }
  //引数のパラメータを変更する
  public function setParam($targetParam,$num){
    $this->$targetParam = $num;
  }
  //名前を選ぶ
  public function selectName(){
    $names = $this->names;
    foreach( array_rand( $names, 3 ) as $key ) {
      global $selectNames;
		$selectNames[] = $names[$key];
	 }
   return $selectNames;
  }
  //名前を変更する
  public function changeName($postName){
    $this->name = $postName;
  }
  //選択された種牡馬を取得
  public function searchSire($select){
    global $sires;
    $hitSire = $sires[$select];
    return $hitSire;
  }
  //選択された牝馬を取得
  public function searchMare($select){
    global $mares;
    $hitMare = $mares[$select];
    return $hitMare;
  }
  //種牡馬のパラメータをプロパティに格納
  public function getSireParam(){
    $this->setParam('sireName',$_SESSION['sire']->getParam('name'));
    $this->setParam('sireTemper',$_SESSION['sire']->getParam('temper'));
    $this->setParam('sireGuts',$_SESSION['sire']->getParam('guts'));
    $this->setParam('sireHealth',$_SESSION['sire']->getParam('health'));
    $this->setParam('sireSpeed',$_SESSION['sire']->getParam('speed'));
    $this->setParam('sireStamina',$_SESSION['sire']->getParam('stamina'));
  }

  //牝馬のパラメータをプロパティに格納
  public function getMareParam(){
    $this->setParam('mareName',$_SESSION['mare']->getParam('name'));
    $this->setParam('mareTemper',$_SESSION['mare']->getParam('temper'));
    $this->setParam('mareGuts',$_SESSION['mare']->getParam('guts'));
    $this->setParam('mareHealth',$_SESSION['mare']->getParam('health'));
    $this->setParam('mareSpeed',$_SESSION['mare']->getParam('speed'));
    $this->setParam('mareStamina',$_SESSION['mare']->getParam('stamina'));
  }
  //パラメータを決定する
  public function decideParam($sireParam,$mareParam){
    $sireParam = $this->getParam($sireParam);
    $mareParam = $this->getParam($mareParam);
    $parentParam = $sireParam + $mareParam;
    $result = $parentParam/2;
    if (!preg_match('/^[0-9]+$/', $result)){
      if(mt_rand(0,1)){
        $result = floor($result);
      }else {
        $result = ceil($result);
      }
    }
    return $result;
  }

}
//主人公馬クラス
class heroHorse extends horse {
  protected $condition;
  protected $img_finish;

  public function __construct($name,$temper,$guts,$health,$speed,$stamina,$condition,$img,$img_finish){
    $this->name = $name;
    $this->temper = $temper;
    $this->guts = $guts;
    $this->health = $health;
    $this->speed = $speed;
    $this->stamina = $stamina;
    $this->condition = $condition;
    $this->img = $img;
    $this->img_finish = $img_finish;
  }
  public function getParam($str){
    return $this->$str;
  }
  public function setParam($targetParam,$num){
    $this->$targetParam = $num;
  }
  public function reportDirector($heroName,$str){
    Director::setWord($heroName,$str);
  }
  //気性によって能力を変動させる
  public function changeOnParam(){
    $temper = $this->temper;
    $params = [];
    $params[] = $this->guts;
    $params[] = $this->health;
    $params[] = $this->speed;
    $params[] = $this->stamina;
    switch ($temper) {
      case 5:
        if(!mt_rand(0,19)){
          for($i = 0; $i < 4; $i++){
            $params[$i] += 1;
          }
        }
        break;
      case 4:
        if(!mt_rand(0,29)){
          for($i = 0; $i < 4; $i++){
            $params[$i] += 1;
          }
        }
        break;
      case 2:
        if(!mt_rand(0,29)){
          for($i = 0; $i < 4; $i++){
            $params[$i] -= 1;
          }
        }
        break;
      case 1:
        if(!mt_rand(0,4)){
          for($i = 0; $i < 4; $i++){
            $params[$i] -= 1;
          }
        }else if(!mt_rand(0,19)){
          for($i = 0; $i < 4; $i++){
            $params[$i] += 2;
          }
        }
    };
    $this->guts = $params[0];
    $this->health = $params[1];
    $this->speed = $params[2];
    $this->stamina = $params[3];
  }
  public function finishResult(){
    $result_satuki = $_SESSION['皐月賞'];
    $result_derby = $_SESSION['日本ダービー'];
    $result_kikka = $_SESSION['菊花賞'];
    $heroName = $_SESSION['hero_Name'];
    $sireName = $_SESSION['sireName'];
    $mareName = $_SESSION['mareName'];
    if($result_satuki === 1 && $result_derby === 1 && $result_kikka === 1){
      $str = '号<br>
              父：'.$sireName.'　母：'.$mareName.'<br>
              <全競走成績＞<br>
              皐月賞 '.$result_satuki.'着<br>
              日本ダービー '.$result_derby.'着<br>
              菊花賞 '.$result_kikka.'着<br>
              3冠達成おめでとうございます！歴史に残る名馬として永久に讃えられるでしょう！！
              ';
    }else{
      $str = '号<br>
              父：'.$sireName.'　母：'.$mareName.'<br>
              <全競走成績＞<br>
              皐月賞 '.$result_satuki.'着<br>
              日本ダービー '.$result_derby.'着<br>
              菊花賞 '.$result_kikka.'着
              ';
    }
    $this->reportDirector($heroName,$str);
  }
}

//障害物クラス（抽象クラス）
abstract class obstacle{
  protected $name;

  //主役馬パラメータを変更する
  abstract public function changeParam($targetObj);
  //場長へ報告
  abstract public function reportDirector($heroName,$str);
}
//トレーニングクラス（障害物クラスを継承）
class training extends obstacle{
  const slope = '坂路';
  const wood = 'ウッドコース';
  const turf = '芝コース';
  //コンディションの変更値
  protected $attackCondition;
  //変更先パラメーター
  protected $targetParam;
  //パラメーターの変化値
  protected $attackParam;
  //初期値設定（名前、画像）
  public function __construct($name,$targetParam,$attackParam,$attackCondition){
    $this->name = $name;
    $this->targetParam = $targetParam;
    $this->attackParam = $attackParam;
    $this->attackCondition = $attackCondition;
  }
  //主役馬パラメータを変更する
  public function changeParam($targetObj){
    $targetObj->setParam($targetParam,$attackParam);
  }
  //場長へ報告
  public function reportDirector($heroName,$str){
    Director::setWord($heroName,$str);
  }
  public function crushReport($crush_flg){
    Director::retire($crush_flg);
  }
  //トレーニングをする
  public function goTraining($select,$heroName,$race_flg){
    $crush_flg = '';

    switch ($select) {
      case '坂路':
        $targetParam = $_SESSION['hero_horse']->getParam('speed');
        $targetParam += 2;
        if($targetParam >= 10){
          $targetParam = 10;
        }
        $setCondition = $_SESSION['hero_horse']->getParam('condition');
        $setCondition += 1;
        $_SESSION['hero_horse']->setParam('speed',$targetParam);
        $_SESSION['hero_horse']->setParam('condition',$setCondition);
        break;
      case 'ウッドコース':
        $targetParam = $_SESSION['hero_horse']->getParam('stamina');
        $targetParam += 2;
        if($targetParam >= 10){
          $targetParam = 10;
        }
        $setCondition = $_SESSION['hero_horse']->getParam('condition');
        $setCondition += 1;
        $_SESSION['hero_horse']->setParam('stamina',$targetParam);
        $_SESSION['hero_horse']->setParam('condition',$setCondition);
        break;
      case '芝コース':
        if(!mt_rand(0,7)){
          $crush_flg = true;
          $_SESSION['crush_flg'] = $crush_flg;
        }else{
          $targetParam = $_SESSION['hero_horse']->getParam('speed');
          $targetParam += 4;
          if($targetParam >= 10){
            $targetParam = 10;
          }
          $setCondition = $_SESSION['hero_horse']->getParam('condition');
          $setCondition += 2;
          $_SESSION['hero_horse']->setParam('speed',$targetParam);
          $_SESSION['hero_horse']->setParam('condition',$setCondition);
        }
    }
      if($crush_flg){
        $this->reportDirector($heroName,'は歩様に乱れがあったので検査をしたところ、骨折が判明しました。残念ですが治療に専念しましょう。');

      }else{
        if($race_flg){
          $this->reportDirector($heroName,'の'.$select.'トレーニングは成功しました。');
        }else{
          $this->reportDirector($heroName,'の'.$select.'トレーニングは成功しました。2回目のメニューを選んでください。');
        }
      }
  }
}
//レースクラス
class race extends obstacle{
  const nige = 'とにかく逃げろ';
  const senko = 'なるべく前に行け';
  const oikomi = '抑えて直線勝負';
  protected $img_strt;
  protected $img_win;
  public function __construct($name,$img_strt,$img_win){
    $this->name = $name;
    $this->img_strt = $img_strt;
    $this->img_win = $img_win;
  }

  //レース結果計算
  public function raceResult($targetObj,$raceTitle,$finish_flg){
    $name = $targetObj->getParam('name');
    $condition = $targetObj->getParam('condition');
    $guts = $targetObj->getParam('guts');
    $speed = $targetObj->getParam('speed');
    $stamina = $targetObj->getParam('stamina');
    switch ($raceTitle) {
      case '皐月賞':
        $result_point = ($condition + $speed) * $guts;
        break;
      case '日本ダービー':
        $result_point =  (($stamina/2) + $condition + $speed) * $guts;
        break;
      case '菊花賞':
        $result_point = (($speed/2) + $condition + $stamina) * $guts;
        $finish_flg = true;
    }
    if($result_point >= 80){
      $resultMsg = 'おめでとうございます！'.$name.'は見事'.$raceTitle.'に勝利しました！';
      $num = 1;
    }else if($result_point >= 75){
      $num = mt_rand(2,3);
      $resultMsg = $name.'は惜しくも'.$num.'着でした。<br>';
    }else if($result_point >= 70){
      $num = mt_rand(4,5);
      $resultMsg = $name.'は惜しくも'.$num.'着でした。<br>';
    }else if($result_point >= 65){
      $num = mt_rand(6,8);
      $resultMsg = $name.'は'.$num.'着でした。<br>';
    }else if($result_point >= 60){
      $num = mt_rand(9,10);
      $resultMsg = $name.'は'.$num.'着でした。<br>';
    }else{
      $num = mt_rand(11,18);
      $resultMsg = '残念ながら'.$name.'は'.$num.'着でした。<br>';
    }
    if(!$finish_flg){
      if($result_point >= 80){
        $resultMsg .= 'この調子で頑張りましょう！<br>さっそくトレーニングを再開しましょう。メニューを選んでください。';
      }else if($result_point >= 60){
        $resultMsg .= '次に向けて頑張りましょう！<br>さっそくトレーニングを再開しましょう。メニューを選んでください。';
      }else{
        $resultMsg .= 'あきらめずに次に向けて頑張りましょう！<br>さっそくトレーニングを再開しましょう。メニューを選んでください。';
      }
    }

    $heroName = '';
    $this->reportDirector($heroName,$resultMsg);
    $result_flg = ($result_point >= 80) ? true : false;
    $_SESSION[$raceTitle] = $num;


}
  //主役馬コンディションを変更する
  public function changeParam($targetObj){
    $condition = $targetObj->getParam('condition');
    $health = $targetObj->getParam('health');
    switch ($health) {
      case 1:
        if($condition >= 5){
          $condition -= 5;
          $targetObj->setParam('condition',$condition);
        }
        break;
      case 2:
        if($condition >= 4){
          $condition -= 4;
          $targetObj->setParam('condition',$condition);
        }
        break;
      case 3:
        if($condition >= 3){
          $condition -= 3;
          $targetObj->setParam('condition',$condition);
        }
        break;
      case 4:
        if($condition >= 2){
          $condition -= 2;
          $targetObj->setParam('condition',$condition);
        }
        break;
      case 5:
        if($condition >= 1){
          $condition -= 1;
          $targetObj->setParam('condition',$condition);
        }
        break;
    }
  }
  public function getParam($str){
    return $this->$str;
  }

  //場長へ報告
  public function reportDirector($heroName,$str){
    Director::setWord($heroName,$str);
  }
  public function reportImgDirector($path){
    Director::setImg($path);
  }
}

interface DirectorInterface{
  public static function setWord($heroName,$str);
}
//場長クラス（インスタンス化不要のためstaticにする）
class Director implements DirectorInterface{
  const fstMsg = '「3冠馬をつくろう！」はあなただけの競走馬を育成できるゲームです。<br>
                  トレーニングを重ねて3冠レース制覇を目指しましょう！';
  const selectTraining = 'のトレーニングメニューを選んでください<br />
                          坂路はスピードに効果があります。<br>
                          ウッドコースはスタミナに効果があります。<br>
                          芝コースはスピードに大幅な効果がありますが、ケガに注意です。';

  const goSatuki = 'は3冠レースの第一関門、皐月賞に出走します！<br>
                    作戦を指示してください！';
  const goDerby = 'は3冠レースの第二関門、日本ダービーに出走します！<br>
                    作戦を指示してください！';
  const goKikka = 'は3冠レースの最終関門、菊花賞に出走します！<br>
                  作戦を指示してください！';
  //表示メッセージを変数に格納
  public static function setWord($heroName='',$str){
    global $word;
    $word = $heroName.$str;
    return $word;
  }
  public static function setImg($path){
    if(empty($path)){
      return 'image/deep.jpg';
    }
    return $path;
  }

  public static function clear(){
    session_destroy();
  }
}

//種牡馬インスタンス生成
$sires['ディープインパクト'] = new parentHorse('ディープインパクト',3,5,1,5,1,'image/sireselect.png');
$sires['キングカメハメハ'] = new parentHorse('キングカメハメハ',5,3,3,5,3,'image/sireselect.png');
$sires['ステイゴールド'] = new parentHorse('ステイゴールド',1,5,5,1,5,'image/sireselect.png');

//繁殖牝馬インスタンス生成
$mares['エアグルーヴ'] = new parentHorse('エアグルーヴ',1,4,2,2,4,'image/mareselect.png');
$mares['ビワハイジ'] = new parentHorse('ビワハイジ',4,2,4,2,2,'image/mareselect.png');
$mares['シーザリオ'] = new parentHorse('シーザリオ',2,4,1,4,4,'image/mareselect.png');

//主役馬インスタンス生成
$foal = new foal('image/foal.jpg');

//トレーニングインスタンス生成
$training['坂路'] = new training('坂路','speed',2,1);
$training['ウッドコース'] = new training('ウッドコース','stamina',2,1);
$training['芝コース'] = new training('芝コース','speed',4,2);

//レースインスタンス生成
$race['皐月賞'] = new race('皐月賞','image/race_start.jpg','image/win-satuki.png');
$race['日本ダービー'] = new race('日本ダービー','image/race_start.jpg','image/win-derby.png');
$race['菊花賞'] = new race('菊花賞','image/race_start.jpg','image/win-kikka.png');

//POSTされたら
if(isset($_POST['post_counter'])){
    $countPost = $_POST['post_counter'];
    $countPost++;
    $_SESSION['horse'] = $foal;
}
if(isset($_POST['select'])){
    $select = $_POST['select'];

    switch ($countPost) {
      case 1:
        $path = $sires['ディープインパクト']->getParam('img');
        Director::setWord($heroName,parentHorse::selectSir);
        $btnLeft = $sires['ディープインパクト']->getParam('name');
        $btnCenter = $sires['キングカメハメハ']->getParam('name');
        $btnRight = $sires['ステイゴールド']->getParam('name');
        break;
      case 2:
        $path = $mares['エアグルーヴ']->getParam('img');
        Director::setWord($heroName,parentHorse::selectMare);
        $btnLeft = $mares['エアグルーヴ']->getParam('name');
        $btnCenter = $mares['ビワハイジ']->getParam('name');
        $btnRight = $mares['シーザリオ']->getParam('name');
        $_SESSION['sire'] = $foal->searchSire($select);
        break;
      case 3:
        $path = $_SESSION['horse']->getParam('img');
        Director::setWord($heroName,foal::birthFoal);
        $names = $foal->selectName();
        $btnLeft = $names[0];
        $btnCenter = $names[1];
        $btnRight = $names[2];
        $_SESSION['horse']->getSireParam();
        $_SESSION['mare'] = $foal->searchMare($select);
        $_SESSION['horse']->getMareParam();
        $_SESSION['horse']->setParam('temper',$_SESSION['horse']->decideParam('sireTemper','mareTemper'));
        $_SESSION['horse']->setParam('guts',$_SESSION['horse']->decideParam('sireGuts','mareGuts'));
        $_SESSION['horse']->setParam('health',$_SESSION['horse']->decideParam('sireHealth','mareHealth'));
        $_SESSION['horse']->setParam('speed',$_SESSION['horse']->decideParam('sireSpeed','mareSpeed'));
        $_SESSION['horse']->setParam('stamina',$_SESSION['horse']->decideParam('sireStamina','mareStamina'));
        $_SESSION['sireName'] = $_SESSION['horse']->getParam('sireName');
        $_SESSION['mareName'] = $_SESSION['horse']->getParam('mareName');
        $temper = $_SESSION['horse']->getParam('temper');
        $guts = $_SESSION['horse']->getParam('guts');
        $health = $_SESSION['horse']->getParam('health');
        $speed = $_SESSION['horse']->getParam('speed');
        $stamina = $_SESSION['horse']->getParam('stamina');
        $hero = new heroHorse('テスト',$temper,$guts,$health,$speed,$stamina,5,'image/normal.png','image/finish.jpg');
        $_SESSION['hero_horse'] = $hero;
        $_SESSION['hero_horse']->changeOnParam();

        break;
      case 4:
        $path = $_SESSION['hero_horse']->getParam('img');
        $_SESSION['hero_horse']->setParam('name',$select);
        $_SESSION['hero_Name'] = $_SESSION['hero_horse']->getParam('name');
        Director::setWord($_SESSION['hero_Name'],Director::selectTraining);
        $btnLeft = training::slope;
        $btnCenter = training::wood;
        $btnRight = training::turf;
        break;
      case 5:
        $race_flg = false;
        $path = $_SESSION['hero_horse']->getParam('img');
        $_SESSION['crush_flg'] = false;
        $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
        if($_SESSION['crush_flg']){
          $crush_flg = true;
          $finish_flg = true;
          $btnFinish = 'スタートに戻る';
        }else{
          $btnLeft = training::slope;
          $btnCenter = training::wood;
          $btnRight = training::turf;
        }
        break;
      case 6:
        if($_SESSION['crush_flg']){
          $finish_flg = false;
          $countPost = '';
          Director::clear();
        }else{
          $race_flg = true;
          $_SESSION['crush_flg'] = false;
          $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
          if($_SESSION['crush_flg']){
            $path = $_SESSION['hero_horse']->getParam('img');
            $crush_flg = true;
            $finish_flg = true;
            $btnFinish = 'スタートに戻る';
          }else{
          $path = $race['皐月賞']->getParam('img_strt');
          Director::setWord($_SESSION['hero_Name'],Director::goSatuki);
          $btnLeft = race::nige;
          $btnCenter = race::senko;
          $btnRight = race::oikomi;
          }
        }
        break;
      case 7:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $finish_flg = false;
        $race['皐月賞']->raceResult($_SESSION['hero_horse'],'皐月賞',$finish_flg);
        if($_SESSION['皐月賞'] == 1){
          $path = $race['皐月賞']->getParam('img_win');
        }else{
          $path = $_SESSION['hero_horse']->getParam('img');
        }
        $race['皐月賞']->changeParam($_SESSION['hero_horse']);
        $btnLeft = training::slope;
        $btnCenter = training::wood;
        $btnRight = training::turf;
      }
        break;
      case 8:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $race_flg = false;
        $path = $_SESSION['hero_horse']->getParam('img');
        $_SESSION['crush_flg'] = false;
        $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
        if($_SESSION['crush_flg']){
          $crush_flg = true;
          $finish_flg = true;
          $btnFinish = 'スタートに戻る';
        }else{
        $btnLeft = training::slope;
        $btnCenter = training::wood;
        $btnRight = training::turf;
      }
    }
        break;
      case 9:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $race_flg = true;
        $_SESSION['crush_flg'] = false;
        $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
        if($_SESSION['crush_flg']){
          $path = $_SESSION['hero_horse']->getParam('img');
          $crush_flg = true;
          $finish_flg = true;
          $btnFinish = 'スタートに戻る';
        }else{
        $path = $race['日本ダービー']->getParam('img_strt');
        Director::setWord($_SESSION['hero_Name'],Director::goDerby);
        $btnLeft = race::nige;
        $btnCenter = race::senko;
        $btnRight = race::oikomi;
      }
    }
        break;
      case 10:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $finish_flg = false;
        $race['日本ダービー']->raceResult($_SESSION['hero_horse'],'日本ダービー',$finish_flg);
        if($_SESSION['日本ダービー'] == 1){
          $path = $race['日本ダービー']->getParam('img_win');
        }else{
          $path = $_SESSION['hero_horse']->getParam('img');
        }
        $race['日本ダービー']->changeParam($_SESSION['hero_horse']);
        $btnLeft = training::slope;
        $btnCenter = training::wood;
        $btnRight = training::turf;
      }
        break;
      case 11:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $race_flg = false;
        $path = $_SESSION['hero_horse']->getParam('img');
        $_SESSION['crush_flg'] = false;
        $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
        if($_SESSION['crush_flg']){
          $crush_flg = true;
          $finish_flg = true;
          $btnFinish = 'スタートに戻る';
        }else{
        $btnLeft = training::slope;
        $btnCenter = training::wood;
        $btnRight = training::turf;
      }
    }
        break;
      case 12:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $race_flg = true;
        $_SESSION['crush_flg'] = false;
        $training['坂路']->goTraining($select,$_SESSION['hero_Name'],$race_flg);
        if($_SESSION['crush_flg']){

          $path = $_SESSION['hero_horse']->getParam('img');
          $crush_flg = true;
          $finish_flg = true;
          $btnFinish = 'スタートに戻る';
        }else{
        $path = $race['菊花賞']->getParam('img_strt');
        Director::setWord($_SESSION['hero_Name'],Director::goKikka);
        $btnLeft = race::nige;
        $btnCenter = race::senko;
        $btnRight = race::oikomi;
      }
    }
        break;
      case 13:
      if($_SESSION['crush_flg']){
        $finish_flg = false;
        $countPost = '';
        Director::clear();
      }else{
        $finish_flg = true;
        $btnFinish = '全競走成績';
        $race['菊花賞']->raceResult($_SESSION['hero_horse'],'菊花賞',$finish_flg);
        if($_SESSION['菊花賞'] == 1){
          $path = $race['菊花賞']->getParam('img_win');
        }else{
          $path = $_SESSION['hero_horse']->getParam('img');
        };
      }
        break;
      case 14:
        $btnFinish = 'スタートに戻る';
        $finish_flg = true;
        $path = $_SESSION['hero_horse']->getParam('img_finish');
        $_SESSION['hero_horse']->finishResult();
        break;
      case 15:
        $finish_flg = false;
        $countPost = '';
        Director::clear();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>3冠馬をつくろう！</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap">
    <div class="wrap-contents">
      <div class="innner-contents">
        <h1>3冠馬をつくろう！</h1>
        <div class="wrap-img">
          <img src="<?php echo Director::setImg($path) ; ?>" class="img-main" width="400" height="270">
        </div>
        <div class="wrap-area-words">
          <img src="<?php echo ($crush_flg) ? 'image/master-sad.png' : 'image/master-normal.png'?>" width="100" height="100" class="img-master">
          <div class="area-words">
            <p>
              <?php echo $word ? $word : Director::fstMsg; ?>
            </p>
          </div>
          <form method="post" class="choice">
            <?php if(!empty($countPost) && $finish_flg == false){ ?>
            <input type="hidden" name="post_counter" value="<?=$countPost?>">
            <input type="submit" name="select" class="btn" value="<?=$btnLeft?>">
            <input type="hidden" name="post_counter" value="<?=$countPost?>">
            <input type="submit" name="select" class="btn" value="<?=$btnCenter?>">
            <input type="hidden" name="post_counter" value="<?=$countPost?>">
            <input type="submit" name="select" class="btn" value="<?=$btnRight?>">
            <?php }else if(!empty($countPost) && $finish_flg == true){ ?>
            <input type="hidden" name="post_counter" value="<?=$countPost?>">
            <input type="submit" name="select" class="btn" value="<?=$btnFinish?>">
          <?php }else if(empty($countPost)){ ?>
            <input type="hidden" name="post_counter" value="<?=$countPost?>">
            <div class="start-btn"><input type="submit" name="select" class="btn" value="ゲームスタート"></div>
            <?php } ?>
        </form>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
