//
//物件管理チェック
//
function fnArticleEditCheck() {
	tmp = form.article.value;
	if (tmp === '') {
		alert('物件名を入力してください');
		return;
	}
	if (tmp.length > 100) {
		alert('物件名は100文字以内で入力してください');
		return;
	}

	tmp = form.room.value;
	if (tmp.length > 100) {
		alert('部屋番号は100文字以内で入力してください');
		return;
	}
	
	tmp = form.keyPlace.value;
	if (tmp.length > 200) {
		alert('鍵場所は100文字以内で入力してください');
		return;
	}

	tmp = form.address.value;
	if (tmp.length > 100) {
		alert('住所は100文字以内で入力してください');
		return;
	}

	tmp = form.articleNote.value;
	if (tmp.length > 200) {
		alert('備考は200文字以内で入力してください');
		return;
	}

	tmp = form.keyBox.value;
	if (tmp.length > 100) {
		alert('キーBox番号は100文字以内で入力してください');
		return;
	}

	tmp = form.drawing.value;
	if (tmp.length > 100) {
		alert('3Dパースは100文字以内で入力してください');
		return;
	}

	tmp = form.sellCharge.value;
	if (tmp.length > 100) {
		alert('営業担当は100文字以内で入力してください');
		return;
	}

	if (confirm('この内容で登録します。よろしいですか？')) {
		form.act.value = 'articleEditComplete';
		form.submit();
	}

}



function fnArticleDeleteCheck(no) {
	if (confirm('削除します。よろしいですか？')) {
		form.articleNo.value = no;
		form.act.value = 'articleDelete';
		form.submit();
	}
}
