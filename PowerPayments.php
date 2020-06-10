<?php

namespace PowerPayments;

use Exception;

class PowerPayments
{

    private $key;
    private $secret;
    private $token;
    private $sdkUri = 'https://payments.powerbyte.com.ar';
    private $userData;
    private $orderInfo = [
        'client_id'         => '',
        'articles_quantity' => 0,
        'subtotal'      => 0,
        'shipping_price'      => 0,
        'total_amount'      => 0,
        'name'              => '',
        'dni'               => '',
        'address'           => '',
        'city'              => '',
        'state'             => '',
        'postal_code'       => '',
        'email'             => '',
        'phone'             => '',
        'cellphone'         => '',
        'comments'          => ''
    ];
    private $article = [
        'name'     => '',
        'price'    => '',
        'quantity' => '',
        'options'  => '',
    ];
    private $orderArticles = [];

    /**
     * PowerPayments constructor.
     * @param $key
     * @param $secret
     * @param $token
     * @throws Exception
     */
    public function __construct($key, $secret, $token = '')
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->token = $token;

        $this->checkUserData();

        $this->orderInfo['client_id'] = $this->userData['id'];
    }

    /**
     * @param $sandbox
     */
    public function setEnvironment($sandbox)
    {
        if ($sandbox) {
            $this->sdkUri = str_replace('payments', 'payments.sandbox', $this->sdkUri);
        }
    }

    /**
     *
     * @throws Exception
     */
    private function checkUserData()
    {
        $userData = $this->sendRequest('checkCredentials', 'post', array(
            'key'    => $this->key,
            'secret' => $this->secret,
            'token'  => $this->token,
        ));
        if ($userData['client']) {
            $this->userData = $userData['client'];
        } else {
            throw new Exception('Error de credenciales.');
        }

    }

    /**
     * @return string
     */
    public function getSdkUri()
    {
        return $this->sdkUri;
    }

    /**
     * @param $request
     * @param string $method
     * @param array $data
     * @return bool|false|mixed|string
     */
    private function sendRequest($request, $method = 'get', $data = [])
    {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => strtolower($method),
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);

        $result = file_get_contents("{$this->sdkUri}/{$request}?" . http_build_query($data), false, $context);

        $result = json_decode($result, true);

        if ($result === FALSE) {
            return false;
        }

        return $result;
    }

    /**
     * @param $header_items
     */
    public function setHeader($header_items)
    {
        foreach ($header_items as $key => $value) {
            if (array_key_exists($key, $this->orderInfo)) {
                $this->orderInfo[$key] = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->orderInfo;
    }

    /**
     * @param $article
     */
    public function setDetails($article)
    {
        foreach ($article as $key => $value) {
            if (array_key_exists($key, $this->article)) {
                $this->article[$key] = $value;

                if ($key == 'options') {
                    $options = [];

                    foreach ($value as $option => $val) {
                        $options[] = "$option:$val";
                    }

                    $this->article[$key] = implode('|', $options);
                }
            }
        }

        $this->orderArticles[] = $this->article;

        $this->clearArticle();
    }

    /**
     *
     */
    private function clearArticle()
    {
        foreach ($this->article as $key => $value) {
            $this->article[$key] = '';
        }
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->orderArticles;
    }

    /**
     * @return bool|false|mixed|string
     * @throws Exception
     */
    public function storeOrder()
    {
        $response = $this->sendRequest('orders/store', 'post', ['order_info' => $this->orderInfo, 'order_articles' => $this->orderArticles]);

        if ($response['order_id']) {
            return $response['order_id'];
        } else {
            throw new Exception('Error al guardar el pedido.');
        }
    }

    /**
     * @param $order_id
     */
    public function paymentFormRedirect($order_id)
    {
        header("Location:{$this->sdkUri}/paymentForm/{$order_id}");
    }

    /**
     * @param $order_id
     */
    public function paymentFirstDataFormRedirect($order_id)
    {
        header("Location:{$this->sdkUri}/firstData/paymentForm/{$order_id}");
    }

    public function cards(){
        $cards = $this->sendRequest('cards', 'post');
    }

    /**
     * @param $order_id
     */
    public function paymentLyraRedirect($order_id)
    {
        header("Location:{$this->sdkUri}/securePayment/{$order_id}");
    }
}