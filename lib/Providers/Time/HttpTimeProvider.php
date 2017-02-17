<?php

namespace RobThree\Auth\Providers\Time;

/**
 * Takes the time from any webserver by doing a HEAD request on the specified URL and extracting the 'Date:' header
 */
class HttpTimeProvider implements ITimeProvider
{
    public $url;

    function __construct($url = 'https://google.com')
    {
        $this->url = $url;
    }

    public function getTime() {
        try {
            $host = parse_url($this->url, PHP_URL_HOST);
            $context  = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'follow_location' => false,
                    'ignore_errors' => true,
                    'max_redirects' => 0,
                    'request_fulluri' => true,
                    'header' => [
                        'Connection: close',
                        'User-agent: TwoFactorAuth HttpTimeProvider (https://github.com/RobThree/TwoFactorAuth)'
                    ]
                ],
                'ssl' => [
                    'SNI_enabled' => true,
                    'SNI_server_name' => $host
                ]
            ]);
            $fd = fopen($this->url, 'rb', false, $context);
            $headers = stream_get_meta_data($fd)['wrapper_data'];
            fclose($fd);

            foreach ($headers as $h) {
                if (strcasecmp(substr($h, 0, 5), 'Date:') === 0)
                    return \DateTime::createFromFormat('D, d M Y H:i:s O+', trim(substr($h,5)))->getTimestamp();
            }
            throw new \TimeException(sprintf('Unable to retrieve time from %s (Invalid or no "Date:" header found)', $this->url));
        }
        catch (Exception $ex) {
            throw new \TimeException(sprintf('Unable to retrieve time from %s (%s)', $this->url, $ex->getMessage()));
        }
    }
}