<?php

//
// タイトル管理画面
//
function subFTitle()
{
    $param = getFTitleParam();

    if ($param["sDel"] == '') {
        $param["sDel"] = 1;
    }

    if (! $param["orderBy"]) {
        $param["orderBy"] = 'CLASSNO,SEQNO';
        $param["orderTo"] = 'asc';
    }

    subMenu();
    subFTitleView($param);
}

//
// 項目管理画面
//
function subFTitleItem()
{
    $param = getFTitleParam();

    if ($param["sDel"] == '') {
        $param["sDel"] = 1;
    }

    if (! $param["orderBy"]) {
        $param["orderBy"] = 'CLASSNO,SEQNO';
        $param["orderTo"] = 'asc';
    }
    $param["sClassNo"] = htmlspecialchars($_REQUEST['sClassNo']);
    $param["sDocNo"] = htmlspecialchars($_REQUEST['sDocNo']);

    subMenu();
    subFTitleItemView($param);
}

//
// タイトル管理編集画面
//
function subFTitleEdit()
{
    $param = getFTitleParam();

    $param["DocNo"] = htmlspecialchars($_REQUEST['docNo']);

    if ($param["DocNo"]) {
        $sql = fnSqlFTitleEdit($param["DocNo"]);
        $res = mysqli_query($param["conn"], $sql);
        $row = mysqli_fetch_array($res);

        $param["DocNo"] = htmlspecialchars($row[0]);
        $param["classNo"] = htmlspecialchars($row[1]);
        $param["seqNo"] = htmlspecialchars($row[2]);
        $param["name"] = htmlspecialchars($row[3]);

        $param["purpose"] = '更新';
        $param["btnImage"] = 'btn_load.png';
    } else {
        $param["purpose"] = '登録';
        $param["btnImage"] = 'btn_enter.png';
    }

    subMenu();
    if ($param["sDocNo"]) {
        subFTitleItemEditView($param);
    } else {
        subFTitleEditView($param);
    }
}

//
// タイトル管理編集完了処理
//
function subFTitleEditComplete()
{
    $param = getFTitleParam();

    $param["DocNo"] = mysqli_real_escape_string($param["conn"], $_REQUEST['DocNo']);
    $param["classNo"] = mysqli_real_escape_string($param["conn"], $_REQUEST['classNo']);
    $param["seqNo"] = mysqli_real_escape_string($param["conn"], $_REQUEST['seqNo']);
    $param["name"] = mysqli_real_escape_string($param["conn"], $_REQUEST['name']);
    $param["sClassNo"] = mysqli_real_escape_string($param["conn"], $_REQUEST['sClassNo']);
    $param["sDocNo"] = mysqli_real_escape_string($param["conn"], $_REQUEST['sDocNo']);

    $ErrClassNo = subFTitleRepetition($param["classNo"], $param["DocNo"]);

    if ($param["DocNo"]) {
        if ($param["seqNo"] == 0) {
            // タイトルの更新処理
            if (! $ErrClassNo) {
                // 更新前の情報を取得
                $sql = fnSqlFTitleEdit($param["DocNo"]);
                $res = mysqli_query($param["conn"], $sql);
                $row = mysqli_fetch_array($res);

                $beforeClassNo = htmlspecialchars($row[1]);

                // タイトルの更新
                $sql = fnSqlFTitleUpdate($param);
                $res = mysqli_query($param["conn"], $sql);

                // 紐付く項目を取得
                $sql = fnSqlFTitleRepetition($beforeClassNo);
                $result = mysqli_query($param["conn"], $sql);
                while ($row = mysqli_fetch_array($result)) {
                    if ($row['SEQNO'] !== '0') {
                        $param["DocNo"] = $row['DOCNO'];
                        // $param["classNo"] = $row['CLASSQNO'];
                        $param["seqNo"] = $row['SEQNO'];
                        $param["name"] = $row["NAME"];
                        $sql = fnSqlFTitleItemUpdate($param);
                        $ret = mysqli_query($param["conn"], $sql);
                    }
                }
                subTitlePage0();
            } else {
                // 重複時
                $param["purpose"] = '更新';
                $param["btnImage"] = 'btn_load.png';
                subFTitleMsg($param);
            }
        } else {
            // 項目名の更新処理 - 重複チェック追加
            // 同じクラス内で同じ表示順が存在するかチェック
            $sql = "SELECT COUNT(*) AS cnt FROM TBLDOC 
                    WHERE DEL = 1 
                    AND CLASSNO = '" . $param["classNo"] . "' 
                    AND SEQNO = '" . $param["seqNo"] . "' 
                    AND DOCNO <> '" . $param["DocNo"] . "'";
            $result = mysqli_query($param["conn"], $sql);
            $row = mysqli_fetch_array($result);
            
            if ($row['cnt'] > 0) {
                // 重複エラー
                $param["seqNoChk"] = "既に登録されている表示順です";
                $param["purpose"] = '更新';
                $param["btnImage"] = 'btn_load.png';
                
                // タイトル情報を取得して保持
                $sql = fnSqlFTitleEdit($param["sDocNo"]);
                $res = mysqli_query($param["conn"], $sql);
                $row = mysqli_fetch_array($res);
                $param["titleName"] = htmlspecialchars($row[3]);
                
                // エラーメッセージ表示
                subMenu();
                subFTitleItemEditView($param);
                print "</body>\n</html>";
                exit();
            } else {
                // 重複がなければ更新処理
                $sql = fnSqlFTitleUpdate($param);
                $res = mysqli_query($param["conn"], $sql);
                subTitlePage1();
            }
        }
    } else {
        $param["DocNo"] = fnNextNo('DOC');
        if (! $param["seqNo"]) {
            if (! $ErrClassNo) {
                $param["seqNo"] = 0;
                $sql = fnSqlFTitleInsert($param);
                $res = mysqli_query($param["conn"], $sql);
                $param["ttl_flg"] = 0;
            } else {
                $param["DocNo"] = "";
                $param["purpose"] = '登録';
                $param["btnImage"] = 'btn_enter.png';
                subFTitleMsg($param);
            }
            subTitlePage0();
        } else {
            // 項目名の登録処理 - 重複チェック追加
            // 同じクラス内で同じ表示順が存在するかチェック
            $sql = "SELECT COUNT(*) AS cnt FROM TBLDOC 
                    WHERE DEL = 1 
                    AND CLASSNO = '" . $param["classNo"] . "' 
                    AND SEQNO = '" . $param["seqNo"] . "'";
            $result = mysqli_query($param["conn"], $sql);
            $row = mysqli_fetch_array($result);
            
            if ($row['cnt'] > 0) {
                // 重複エラー
                $param["DocNo"] = "";
                $param["seqNoChk"] = "既に登録されている表示順です";
                $param["purpose"] = '登録';
                $param["btnImage"] = 'btn_enter.png';
                
                // タイトル情報を取得して保持
                $sql = fnSqlFTitleEdit($param["sDocNo"]);
                $res = mysqli_query($param["conn"], $sql);
                $row = mysqli_fetch_array($res);
                $param["titleName"] = htmlspecialchars($row[3]);
                
                // エラーメッセージ表示
                subMenu();
                subFTitleItemEditView($param);
                print "</body>\n</html>";
                exit();
            } else {
                // 重複がなければ登録処理
                $sql = fnSqlFTitleInsert($param);
                $res = mysqli_query($param["conn"], $sql);
                subTitlePage1();
            }
        }
    }
}

function subFTitleItemRepetition($classNo, $seqNo, $DocNo)
{
    $conn = fnDbConnect();

    $sql = "SELECT DOCNO, CLASSNO, SEQNO, NAME FROM TBLDOC 
            WHERE DEL = 1 AND CLASSNO = '$classNo' AND SEQNO = '$seqNo'";
    if ($DocNo) {
        $sql .= " AND DOCNO <> '$DocNo'";
    }
    
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
        return true; // 重複あり
    }
    return false; // 重複なし
}

//
// 画面振り分け
//
function subTitlePage0()
{
    $_REQUEST['act'] = 'fTitleSearch';
    subFTitle();
}

function subTitlePage1()
{
    $_REQUEST['act'] = 'fTitleItemSearch';
    subFTitleItem();
}

//
// タイトル管理削除処理
//
function subFTitleDelete()
{
    $conn = fnDbConnect();

    $DocNo = $_REQUEST['DocNo'];

    if ($_REQUEST['seqNo'] == 0) {
        $sql = fnSqlFTitleRepetition($_REQUEST['classNo']);
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($res)) {
            $sql = fnSqlFTitleDelete($row['DOCNO']);
            $result = mysqli_query($conn, $sql);
        }
    } else {
        $sql = fnSqlFTitleDelete($DocNo);
        $res = mysqli_query($conn, $sql);
    }

    $_REQUEST['act'] = 'fTitleSearch';
    subFTitle();
}

//
// 画面間引継ぎ情報
//
function getFTitleParam()
{
    $param = array();

    // DB接続
    $param["conn"] = fnDbConnect();

    $param["orderBy"] = $_REQUEST['orderBy'];
    $param["orderTo"] = $_REQUEST['orderTo'];

    return $param;
}

//
// 項目名管理編集画面
//
function subFTitleItemEdit()
{
    $param = getFTitleParam();

    $param["DocNo"] = htmlspecialchars($_REQUEST['docNo']);
    $param["sDocNo"] = htmlspecialchars($_REQUEST['sDocNo']);
    $param["sClassNo"] = htmlspecialchars($_REQUEST['sClassNo']);

    if ($param["DocNo"]) {
        $sql = fnSqlFTitleEdit($param["DocNo"]);
        $res = mysqli_query($param["conn"], $sql);
        $row = mysqli_fetch_array($res);

        $param["DocNo"] = htmlspecialchars($row[0]);
        $param["classNo"] = htmlspecialchars($row[1]);
        $param["seqNo"] = htmlspecialchars($row[2]);
        $param["name"] = htmlspecialchars($row[3]);

        $param["purpose"] = '更新';
        $param["btnImage"] = 'btn_load.png';
    } else {
        $param["purpose"] = '登録';
        $param["btnImage"] = 'btn_enter.png';
    }

    subMenu();
    subFTitleItemEditView($param);
}

//
// 重複チェック
//
function subFTitleRepetition($classNo, $DocNo)
{
    $conn = fnDbConnect();

    $sql = fnSqlFTitleRepetition($classNo);
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($res)) {
        if ($row['CLASSNO'] == $classNo && $row['SEQNO'] == 0) {
            if ($row['DOCNO'] !== $DocNo) {
                return $row['CLASSNO'];
            }
        }
    }
}

//
// エラー表示
//
function subFTitleMsg($param)
{
    $param["classNoChk"] = "既に登録されている表示順です";
    
    // DocNoがある場合のみfnSqlFTitleEditを実行
    if ($param["DocNo"]) {
        $sql = fnSqlFTitleEdit($param["DocNo"]);
        $res = mysqli_query($param["conn"], $sql);
        $row = mysqli_fetch_array($res);
        
        // 以前の値で上書きしないよう、既に値がセットされていない場合のみDBの値を使用
        if (!isset($param["classNo"]) || $param["classNo"] === '') {
            $param["classNo"] = htmlspecialchars($row[1]);
        }
        if (!isset($param["name"]) || $param["name"] === '') {
            $param["name"] = htmlspecialchars($row[3]);
        }
    }
    
    // 入力値を保持（既に値がセットされている場合はそれを優先）
    $param["classNo"] = isset($_REQUEST['classNo']) ? htmlspecialchars($_REQUEST['classNo']) : $param["classNo"];
    $param["name"] = isset($_REQUEST['name']) ? htmlspecialchars($_REQUEST['name']) : $param["name"];

    $_REQUEST['act'] = 'fTitleEdit';
    subMenu();
    subFTitleEditView($param);
    print "</body>\n</html>";
    exit();
}