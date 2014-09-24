<?php

namespace RobThree\TwoFactorAuth\Providers;

abstract class BaseHTTPQRCodeProvider implements IQRCodeProvider
{
    protected $verifyssl;

	protected function getContent($url)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_DNS_CACHE_TIMEOUT => 10,
			CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => $this->verifyssl,
			CURLOPT_USERAGENT => 'TwoFactorAuth'
        ));
        $data = curl_exec($ch);
        
        curl_close($ch);
        return $data;
    }
}