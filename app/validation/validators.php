<?php

Validator::extend('yt_valid', function($attribute, $value, $parameters)
{
	if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#", $value)) return true;

    return false;
});

Validator::extend('yt_embeddable', function($attribute, $value, $parameters)
{
	if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#", $value, $matches)) {
		return yt_check($matches[2], 'embeddable');
	}
    return false;
});

Validator::extend('yt_public', function($attribute, $value, $parameters)
{
	if(preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#", $value, $matches)) {
		return yt_check($matches[2], 'privacyStatus');
	}
    return false;
});

function yt_check($code, $option) {
	static $data;

	if(!isset($data)) {
		$result = curlhelper("https://www.googleapis.com/youtube/v3/videos/?part=status&id=" . $code . "&alt=json&key=AIzaSyCSAgGi8ROocoKToii0j20BXv0HNcSqWOg");
		$data = json_decode($result);
	}
	if(count($data->items) > 0) {
		return $data->items[0]->status->$option;
	}
	return false;
}

function curlhelper($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER ,true);
	$response = curl_exec($curl);
	if(curl_errno($curl)) {
		throw new \Exception('CURL Error: ' . curl_error($curl));
	}
	return $response;
}