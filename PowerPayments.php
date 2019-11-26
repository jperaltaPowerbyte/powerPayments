<?php


class PowerPayments
{

    private $key;
    private $secret;
    private $token;
    private $sdkUri = 'https://payments.powerbyte.com.ar';

    /**
     * PowerPayments constructor.
     * @param $key
     * @param $secret
     * @param $token
     */
    public function __construct($key, $secret, $token)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->token = $token;
    }

    public function setEnviroment($sandbox)
    {
        if ($sandbox) {
            $this->sdkUri = str_replace('payments', 'payments.sandbox', $this->sdkUri);
        }
    }

    public function checkUserData($sandbox = false)
    {


    }

    /**
     * @return string
     */
    public function getSdkUri()
    {
        return $this->sdkUri;
    }

    private function sendCurlRequest($data, $sandbox = false)
    {

    }


}