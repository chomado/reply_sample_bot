<?php
require_once 'vendor/autoload.php';
require_once 'config.inc.php';

// 最終投稿IDを取得
$param['since_id'] = file_get_contents(LAST_ID_FILE);
if (empty($param['since_id'])) {
	$param = null;
}

// Twitterに接続
$connection = new TwitterOAuth\Api(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_SECRET);

// リプライを取得
$res = $connection->get('statuses/mentions_timeline', $param);
if (!empty($res)) {
	// 新しい投稿があれば分解
	foreach($res as $post) {
		// pingという文字列が見つかれば、スクリーンネーム付きでpongと返す。
		if (preg_match('/ping/', $post->text)) {
			$param['status'] = sprintf('@%s %s', $post->user->screen_name, 'pong');
			$param['in_reply_to_status_id'] = $post->id_str;
			$connection->post('statuses/update', $param);
		}
	}

	// 最終投稿IDを書き込む
	file_put_contents(LAST_ID_FILE, $res[0]->id_str);
}
