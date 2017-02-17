<?php

namespace RobThree\Auth\Providers\Time;

/**
 * Takes the time from any webserver by doing a HEAD request on the specified URL and extracting the 'Date:' header
 */
class HttpTimeProvider implements ITimeProvider
{
    public $url;
    public $options;

    function __construct($url = 'https://google.com', array $options = null)
    {
        $this->url = $url;

        $this->options = $options;
        if ($this->options === null) {
            $this->options = array(
                'http' => array(
                    'method' => 'HEAD',
                    'follow_location' => false,
                    'ignore_errors' => true,
                    'max_redirects' => 0,
                    'request_fulluri' => true,
                    'header' => array(
                        'Connection: close',
                        'User-agent: TwoFactorAuth HttpTimeProvider (https://github.com/RobThree/TwoFactorAuth)'
                    )
                )
            );
        }
    }

    public function getTime() {
        try {
            $context  = stream_context_create($this->options);
            $fd = fopen($this->url, 'rb', false, $context);
            $headers = stream_get_meta_data($fd);
            fclose($fd);

            foreach ($headers['wrapper_data'] as $h) {
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