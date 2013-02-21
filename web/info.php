<?php
phpinfo();
$_h = curl_init(); 
curl_setopt($_h, CURLOPT_HEADER, 1); 
curl_setopt($_h, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($_h, CURLOPT_HTTPGET, 1); 
curl_setopt($_h, CURLOPT_URL, 'http://graph.facebook.com' ); 
curl_setopt($_h, CURLOPT_DNS_USE_GLOBAL_CACHE, false ); 
curl_setopt($_h, CURLOPT_DNS_CACHE_TIMEOUT, 2 ); 
var_dump(curl_exec($_h)); 
var_dump(curl_getinfo($_h)); 
var_dump(curl_error($_h)); 
$ip = gethostbyname('www.google.com');
var_dump($ip);
