<?php
require_once 'vendor/autoload.php';
require_once 'config.inc.php';

// 最終投稿IDを取得
$param['since_id'] = file_get_contents(LAST_ID);
if (empty($param['since_id'])) {
	$param = null;
}

// Twitterに接続
$connection = new TwitterOAuth\Api(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_SECRET);
// TwitterOAuthの名前空間の中のApi~にアクセスしている

// リプライを取得
// ライブラリ側でparamsを展開してstatuses/mentions_timeline.json?since_id=424546556435
$res = $connection->get('statuses/mentions_timeline', $param);
// resは配列. Twitterから返ってきた, リプライの入った配列.

if (!empty($res)) {
	// 新しい投稿があれば分解
	foreach($res as $post) {
		// pingという文字列が見つかれば、スクリーンネーム付きでpongと返す。
		if (preg_match('/ping/', $post->text)) { // 「$post->text」は, post(リプライ)の本文
			// ツイートの内容
			$param['status'] = sprintf('@%s %s', $post->user->screen_name, 'pong');
			// リプライのツイートのID
			$param['in_reply_to_status_id'] = $post->id_str;
			$connection->post('statuses/update', $param);
		}
		else if (preg_match('/こんにちは/', $post->text)) { 
			$param['status'] = sprintf('@%s %sさんこんにちは!', $post->user->screen_name, $post->user->screen_name);
			$param['in_reply_to_status_id'] = $post->id_str;
			$connection->post('statuses/update', $param);
		}
		else if (preg_match('/こんばんは/', $post->text)) { 
			$param['status'] = sprintf('@%s %sさんこんばんは!', $post->user->screen_name, $post->user->screen_name);
			$param['in_reply_to_status_id'] = $post->id_str;
			$connection->post('statuses/update', $param);
		}
		else if (preg_match('/おはよう/', $post->text)) { 
			$param['status'] = sprintf('@%s %sさんおはようございます!', $post->user->screen_name, $post->user->screen_name);
			$param['in_reply_to_status_id'] = $post->id_str;
			$connection->post('statuses/update', $param);
		}
	}

	// 最終投稿IDを書き込む
	file_put_contents(LAST_ID, $res[0]->id_str);


}

var_dump($res);
