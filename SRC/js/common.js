/**
 * 日付チェック
 * @param msg エラー時に表示したい項目名
 * @param oYMD チェックする日付
 * @return true:正常、false:異常
 */
function fnYMDCheck(msg, obj) {
    // 未入力時はチェックしない
    oYMD = obj.value;

    if (!oYMD) {
        return true;
    }

    // 日付フォーマットのチェック（yyyy/mm/dd）
    if (!oYMD.match(/^\d{4}\/\d{2}\/\d{2}$/)) {
        alert(msg + "を入力してください");
        return false;
    }

    var tmp = oYMD.split('/');
    
    // 年・月・日が適切な範囲かチェック
    var year = parseInt(tmp[0], 10);
    var month = parseInt(tmp[1], 10);
    var day = parseInt(tmp[2], 10);
    
    if (year < 1900 || year > 2100 || month < 1 || month > 12 || day < 1 || day > 31) {
        alert(msg + "を入力してください");
        return false;
    }

    var ymd = new Date(year, month - 1, day);
    
    // 実際に存在する日付かチェック（例：2月30日などの無効な日付を検出）
    if (ymd.getFullYear() !== year || ymd.getMonth() !== month - 1 || ymd.getDate() !== day) {
        alert(msg + "を入力してください");
        return false;
    }

    var vYMD = ymd.getFullYear() + '/' + ('0' + (ymd.getMonth() + 1)).slice(-2) + '/' + ('0' + ymd.getDate()).slice(-2);
    if (oYMD === vYMD) {
        return true;
    } else {
        alert(msg + "を入力してください");
        return false;
    }
}
/**
 * 入力桁数チェック
 *
 * @param length チェックしたい桁数
 * @param msg    エラー時に表示したい項目名
 * @param obj    チェックしたい項目
 * @return true:異常、false:正常
 */
function isLength(length, msg, obj) {
	rtn = false;
	if (obj.value.length > length) {
		alert(msg + "は" + length + "文字以内で入力して下さい");
		rtn = true;
	}
	return rtn;
}



/**
 * 数値桁数チェック
 *
 * @param length チェックしたい桁数
 * @param msg    エラー時に表示したい項目名
 * @param obj    チェックしたい項目
 * @return true:異常、false:正常
 */
function isNumericLength(length, msg, obj) {
	rtn = false;
	if (obj.value.length > length  || obj.value.match(/[^0-9]+/)) {
		alert(msg + "は" + length + "桁以内の半角数字で入力してください");
		rtn = true;
	}
	return rtn;
}
