<?php
// Based on / inspired by: https://github.com/PHPGangsta/GoogleAuthenticator
// Algorithms, digits, period etc. explained: https://code.google.com/p/google-authenticator/wiki/KeyUriFormat
class TwoFactorAuth {
    
    private $algorithm;
    private $period;
    private $digits;
    private $issuer;
    private $qrcodeprovider;
    private static $_base32;
    private static $_base32lookup = array();
    private static $_supportedalgos = array('sha1', 'sha256', 'sha512', 'md5');
    
    function __construct($issuer = null, $digits = 6, $period = 30, $algorithm = 'sha1', $qrcodeprovider = null) {
        $this->issuer = $issuer;

        if (!is_int($digits) || $digits <= 0)
            throw new Exception('Digits must be int > 0');
        $this->digits = $digits;
        
        if (!is_int($period) || $period <= 0)
            throw new Exception('Period must be int > 0');
        $this->period = $period;
        
        $algorithm = strtolower(trim($algorithm));
        if (!in_array($algorithm, self::$_supportedalgos))
            throw new Exception('Unsupported algorithm: ' . $algorithm);
        $this->algorithm = $algorithm;
        
        if ($qrcodeprovider==null)
            $qrcodeprovider = new GoogleQRCodeProvider();
        
        if (!($qrcodeprovider instanceof IQRCodeProvider))
            throw new Exception('QRCodeProvider must implement IQRCodeProvider');
        
        $this->qrcodeprovider = new GoogleQRCodeProvider();
        
        self::$_base32 = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=');
        for ($i = 0; $i < sizeof(self::$_base32); $i++)
            self::$_base32lookup[self::$_base32[$i]] = $i;
    }
    
    /**
     * Create a new secret
     */
    public function createSecret($length = 16) {
        $secret = '';
        $rnd = openssl_random_pseudo_bytes($length);
        for ($i = 0; $i < $length; $i++)
            $secret .= self::$_base32[ord($rnd[$i]) & 31];  //Mask out left 3 bits for 0-31 values
        return $secret;
    }
    
    /**
     * Calculate the code with given secret and point in time
     */
    public function getCode($secret, $time = null)
    {
        $secretkey = $this->base32Decode($secret);
        
        $ts = "\0\0\0\0".pack('N*', $this->getTimeSlice($this->getTime($time)));    // Pack time into binary string
        $hm = hash_hmac($this->algorithm, $ts, $secretkey, true);                   // Hash it with users secret key
        $offset = ord(substr($hm, -1)) & 0x0F;                                      // Use last nibble of result as index/offset
        $hashpart = substr($hm, $offset, 4);                                        // Grab 4 bytes of the result
        $value = unpack('N', $hashpart);                                            // Unpack binary value
        $value = $value[1] & 0x7FFFFFFF;                                            // Drop MSB, keep only 31 bits
        
        return str_pad($value % pow(10, $this->digits), $this->digits, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if the code is correct. This will accept codes starting from ($discrepancy * $period) sec ago to ($discrepancy * period) sec from now
     */
    public function verifyCode($secret, $code, $discrepancy = 1, $time = null)
    {
        $t = $this->getTime($time);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            if (strcmp($this->getCode($secret, $t + ($i * $this->period)), $code) === 0)
                return true;
        }
        
        return false;
    }
    
    /**
     * Get data-uri of QRCode
     */
    public function getQRCodeImageAsDataUri($label, $secret, $size = 200) {
        if (!is_int($size) || $size < 0)
            throw new  Exception('Size must be int > 0');
        
        return 'data:image/png;base64,'.base64_encode($this->qrcodeprovider->getQRCodeImage($this->getQRText($label, $secret), $size));
    }
    
    private function getTime($time) {
        return ($time === null) ? time() : $time;
    }
    
    private function getTimeSlice($time = null, $offset = 0) {
        return (int)floor($time / $this->period) + ($offset * $this->period);
    }
    
    /**
     * Builds a string to be encoded in a QR code
     */
    private function getQRText($label, $secret) {
        return 'otpauth://totp/' . rawurlencode($label)
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($this->issuer)
            . '&period=' . intval($this->period)
            . '&algorithm=' . rawurlencode(strtoupper($this->algorithm))
            . '&digits=' . intval($this->digits);
    }
    
    private function base32Decode($value)
    {
        if (strlen($value)==0) return '';
        
        $s = '';
        foreach (str_split($value) as $c) {
            if ($c !== '=')
                $s .= str_pad(decbin(self::$_base32lookup[$c]), 5, 0, STR_PAD_LEFT);
        }
        $l = strlen($s);
        $r = trim(chunk_split(substr($s, 0, $l - ($l % 8)), 8, ' '));
        
		$o = '';
		foreach (explode(' ', $r) as $b)
            $o .= chr(bindec(str_pad($b, 8, 0, STR_PAD_RIGHT)));

		return $o;
    }
}

interface IQRCodeProvider
{
	public function getQRCodeImage($qrtext, $size);
}

class GoogleQRCodeProvider implements IQRCodeProvider {
    private $verifyssl;

    function __construct($verifyssl = false) {
        if (!is_bool($verifyssl))
            throw new Exception('VerifySSL must be bool');

        $this->verifyssl = $verifyssl;
    }
    
    public function getQRCodeImage($qrtext, $size) {
        return $this->get_content($this->getUrl($qrtext, $size));
    }
    
    public function getUrl($qrtext, $size) {
        return 'https://chart.googleapis.com/chart?chs='.$size.'x'.$size.'&chld=M|0&cht=qr&chl='.rawurlencode($qrtext);
    }

    private function get_content($url){
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