<?php 
require_once('TwitterAPIExchange.php');

/** Set Access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
	'oauth_access_token' => "627941357-ZCBlLeoM25J0Qg0VJ4CuJDmr0Y1qOuTXYfeYHPfn",
	'oauth_access_token_secret' => "n3BHvVanpZzAdgfBEzndQ21elJCDeUxt5FcWCh1yTzXb7",
	'consumer_key' => "9ZLpou6BvE2qptnss9QUWUVD1",
	'consumer_secret' => "czNhg7TX2yiv6vEzcGfPj5J6RS0GQLW6UYvL3YPKOopUo7lB5T"
);

$url = "https://api.twitter.com/1.1/search/tweets.json";
$requestMethod = "GET";
$getfield = '?q=taco+bell+diarrhea%20-RT&count=100';

$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
			 ->buildOauth($url, $requestMethod)
			 ->performRequest(),$assoc = TRUE);



foreach($string['statuses'] as $items) {
	$tweet = $items['text'];
	$tweet = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $tweet);
		
	//Handles as links
	$words = explode(' ',$tweet);
	foreach ($words as $key => $value) {
		$hasAt = strpos($value,'@');
		if ($hasAt === 0) {
			$tmpValue = substr($value,1,strlen($value)-1);
			$words[$key] = '<span class="tweet-link"><a href="http://www.twitter.com/' . $tmpValue . '" target="_blank">' . $value . "</a></span>";
		}
	}
	
	foreach ($words as $key => $value) {
		$hasAt = strpos($value,'#');
		if ($hasAt === 0) {
			$tmpValue = substr($value,1,strlen($value)-1);
			$words[$key] = '<span class="tweet-link"><a href="https://twitter.com/search?q=%23' . $tmpValue . '" target="_blank">' . $value . "</a></span>";
		}
	}
	
	
	
	
	$items['text'] = implode(' ', $words); //Reassembles tweet string

	/*
	echo "<div class='tweet-container'>";
	echo "<p class='tweet-data'>".$name." <a href='http://www.twitter.com/".$screenname."'>@".$screenname."</a> ".$timestamp."</p>";
	echo "<p class='tweet-text'>".$tweet."</p></div>";
	*/
	
	$tweetHTML = getTweet($items);
	echo $tweetHTML;
}


function getTweet($array) {
	$hereDocs = <<<EOD
		<div class='tweet-container'>
		
			<div class="tweet-icon">
				<img src='{$array['user']['profile_image_url']}' />
			</div>
			
			<div class='tweet-data-container'>
			
				<div class='tweet-data'>
					<a href='http://www.twitter.com/{$array['user']['screen_name']}'>{$array['user']['name']}</a>
					<span class="tweet-date">@{$array['user']['screen_name']} &middot; <span class="prettyDate">{$array['created_at']}</span></span><br />
				</div>
				
				<div class='tweet-text'>
					{$array['text']}
				</div>
				
			</div>
			
			<div class='web-intents'>
					<a href="https://twitter.com/intent/tweet?in_reply_to={$array['id_str']}"><p class="tweet-reply"> Reply</p></a> 
					<a href="https://twitter.com/intent/retweet?tweet_id={$array['id_str']}"><p class="tweet-retweet"> Retweet</p></a> 
					<a href="https://twitter.com/intent/favorite?tweet_id={$array['id_str']}"><p class="tweet-favorite"> Favorite</p></a> 
			</div>
			
		</div>
EOD;
	return $hereDocs;
}

?>

