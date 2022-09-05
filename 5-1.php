<!DOCTYPE html>
<html lang="ja">
     <head>
         <meta charset="utf-8">
         <title>Mission_5-1</title>
     </head>
     <body>
     <?php 
             /* データベースへの接続 */
             
             
             /* テーブルの作成 */
             $sql = "CREATE TABLE IF NOT EXISTS table1"
             ." ("
             . "id INT AUTO_INCREMENT PRIMARY KEY,"    /* 投稿番号 */
             . "name char(32),"    /* 氏名 */
             . "comment TEXT,"    /* コメント */
             . "password TEXT,"    /* パスワード */
             . "date TEXT"    /* 投稿日時 */
             .");";
             $stmt = $pdo->query($sql);
         ?>
         
         <!-- 新規投稿・編集フォーム -->
         <form method="post">
             <?php
                 /* 入力フォームへの反映 */
                 $hidden_number = "";
                 $init_name = "";
                 $init_comment = "";
                 $edit_switch = 0;    /* パスワード不一致の判別 */
                 /* 編集対象指定フォームが入力されたら、名前とコメントを予め表示 */
                 if (isset($_POST["edit_number"]) && $_POST["edit_number"]!=="" && isset($_POST["edit_password"]) && $_POST["edit_password"]!=="") {
                     $edit_number = $_POST["edit_number"];
                     $edit_password = $_POST["edit_password"];
                     $sql = 'SELECT * FROM table1';
                     $stmt = $pdo->query($sql);
                     $results = $stmt->fetchAll();
                     foreach ($results as $row){
                         /* 当該レコードが存在し、且つパスワードが一致した際にフォームを埋める */
                         if ($row[0]==$edit_number && $row[3]==$edit_password) {
                             $hidden_number = $edit_number;
                             $init_name = $row[1];
                             $init_comment = $row[2];
                         } elseif($row[0]==$edit_number && $row[3]!=$edit_password) {
                             $edit_switch = 1;
                         }
                     }
                     if ($edit_switch==1) {
                         echo "パスワードが違います！<br>";
                     }
                 } elseif(isset($_POST["edit_number"]) && isset($_POST["edit_password"]) && (($_POST["edit_number"]==="" && $_POST["edit_password"]!=="") || ($_POST["edit_number"]!=="" && $_POST["edit_password"]===""))) {
                     echo "編集対象番号とパスワードを両方記入して下さい！<br>";
                 }
             ?>
             <label>名前：　　
                 <input type="text" name="name" value="<?php echo $init_name?>">    <!-- 名前を入力 -->
             </label>
             <br>
             <label>コメント：
                 <input type="text" name="comment" value="<?php echo $init_comment?>">    <!-- コメントを入力 -->
             </label>
             <br>
             <label>パスワード：
                 <input type="text" name="password">    <!-- 保存するパスワードを入力 -->
             </label>
             <br>
             <input type="hidden" name="hidden_number" value="<?php echo $hidden_number?>">    <!-- 編集番号 (非表示) -->
             <input type="submit" name="submit">    <!-- 送信ボタン -->
         </form>
         <br>
         <!-- 削除フォーム -->
         <form method="post">
             <label>削除対象番号：
                 <input type="number" name="delete_number" min="1">    <!-- 削除対象番号を入力 -->
             </label>
             <br>
             <label>パスワード：
                 <input type="text" name="delete_password">    <!-- 削除用のパスワードを入力 -->
             </label>
             <br>
             <input type="submit" name="delete_submit" value="削除">    <!-- 削除ボタン -->
         </form>
         <br>
         <!-- 編集対象指定フォーム -->
         <form method="post">
             <label>編集対象番号：
                 <input type="number" name="edit_number" min="1">    <!-- 編集対象番号を入力 -->
             </label>
             <br>
             <label>パスワード：
                 <input type="text" name="edit_password">    <!-- 編集用のパスワードを入力 -->
             </label>
             <br>
             <input type="submit" name="edit_submit" value="編集">    <!-- 編集ボタン -->
         </form>
         <br>
         <br>
         
         <!-- 処理 -->
         <?php
             /* テーブルが空であるか判定 */
             $sql = "SELECT * FROM table1 WHERE id IS NOT NULL";
             $stmt = $pdo->query($sql);
             $result = $stmt->fetchAll();
             
             if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])) {
                 $name = $_POST["name"];
                 $comment = $_POST["comment"];
                 $password = $_POST["password"];
             }
             $date = date("Y/d/m H:i:s");
             
             /* 投稿 */
             if (isset($_POST["hidden_number"]) && $_POST["hidden_number"]=="" && isset($_POST["name"]) && $_POST["name"]!=="" && isset($_POST["comment"]) && $_POST["comment"]!=="" && isset($_POST["password"]) && $_POST["password"]!=="") {
                 /* レコードの追加 */
                 $sql = $pdo -> prepare("INSERT INTO table1 (name, comment, password, date) VALUES (:name, :comment, :password, :date)");
                 $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                 $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                 $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                 $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                 $sql -> execute();
             /* 投稿エラー */
             } elseif ((isset($_POST["name"]) && $_POST["name"]!="999") && ((isset($_POST["name"]) && $_POST["name"]==="") || (isset($_POST["comment"]) && $_POST["comment"]==="") 
                        || (isset($_POST["password"]) && $_POST["password"]===""))) {
                 echo "名前・コメント・パスワードを全て記入して下さい！";
             /* 投稿削除 */
             } elseif(empty($result)==FALSE && isset($_POST["delete_password"]) && $_POST["delete_password"]!=="" && isset($_POST["delete_number"]) && $_POST["delete_number"]!=="") {
                 $delete_password = $_POST["delete_password"];
                 $delete_number = $_POST["delete_number"];
                 $delete_switch = 0;
                 /* パスワードの取得 */
                 $sql = 'SELECT * FROM table1';
                 $stmt = $pdo->query($sql);
                 $results = $stmt->fetchAll();
                 foreach ($results as $row){
                     /* パスワードが一致した場合、当該レコードを削除 */
                     if ($row[0]==$delete_number && $row[3]==$delete_password) {
                         $sql = 'delete from table1 where id=:delete_number';
                         $stmt = $pdo->prepare($sql);
                         $stmt->bindParam(':delete_number', $delete_number, PDO::PARAM_INT);
                         $stmt->execute();
                     } elseif(($row[0]==$delete_number && $row[3]!=$delete_password) || ($row[0]!=$delete_number && $row[3]==$delete_password)) {
                         $delete_switch = 1;
                     }
                 }
                 if ($delete_switch==1) {
                     echo "該当する投稿が存在しないか、パスワードが違います！<br>";
                 }
             /* 削除エラー */
            } elseif(empty($result)==FALSE && ((isset($_POST["delete_password"]) && $_POST["delete_password"]==="") || (isset($_POST["delete_number"]) && $_POST["delete_number"]===""))) {
                 echo "削除対象番号とパスワードを全て記入して下さい！<br>";
             /* 投稿編集 */
             } elseif (empty($result)==FALSE && isset($_POST["hidden_number"]) && $_POST["hidden_number"]!=="" && isset($_POST["name"]) && $_POST["name"]!=="" && isset($_POST["comment"]) && $_POST["comment"]!=="" 
                       && isset($_POST["password"]) && $_POST["password"]!=="") {
                 $name = $_POST["name"];
                 $comment = $_POST["comment"];
                 $password = $_POST["password"];
                 $hidden_number = $_POST["hidden_number"];
                 $edit_switch = 0;
                 $sql = 'SELECT * FROM table1';
                 $stmt = $pdo->query($sql);
                 $results = $stmt->fetchAll();
                 foreach ($results as $row){
                     /* 当該レコードが存在し且つパスワードが一致した際に、当該レコードを編集する */
                     if ($row[0]==$hidden_number && $row[3]==$password) {
                         $sql = 'UPDATE table1 SET name=:name,comment=:comment,password=:password,date=:date WHERE id=:hidden_number';
                         $stmt = $pdo->prepare($sql);
                         $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                         $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                         $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                         $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                         $stmt->bindParam(':hidden_number', $hidden_number, PDO::PARAM_INT);
                         $stmt->execute();
                     } elseif(($row[0]==$hidden_number && $row[3]!=$password) || ($row[0]!=$hidden_number && $row[3]==$password)) {
                         $edit_switch = 1;
                     }
                 }
                 if ($edit_switch==1) {
                     echo "該当する投稿が存在しないか、パスワードが違います！<br>";
                 } elseif((isset($_POST["name"]) && $_POST["name"]==="") || (isset($_POST["comment"]) && $_POST["comment"]==="") || (isset($_POST["password"]) && $_POST["password"]==="")) {
                     echo "名前・コメント・パスワードを全て記入して下さい！<br>";
                 }
                 $hidden_number = "";
             /* 投稿表示 */
             } elseif (isset($_POST["name"]) && $_POST["name"]=="999") {
                 $sql = 'SELECT * FROM table1';
                 $stmt = $pdo->query($sql);
                 $results = $stmt->fetchAll();
                 foreach ($results as $row){
                     echo $row['id'].',';
                     echo $row['name'].',';
                     echo $row['comment'].',';
                     echo $row['password'].',';
                     echo $row['date'].'<br>';
                     echo "<hr>";
                 }
             }
         ?>
     </body>
 </html>