<?php

/**
 * Class Postage
 *
 * Original PostageApp class taken from:
 *  http://help.postageapp.com/kb/quick-start-guides/php
 *
 * Modified by:
 * @author Ronald Castillo <ronaldcastillo@gmail.com>
 * @package Postage
 */
class Postage
{

    /**
     * @var string
     */
    private $hostname = 'https://api.postageapp.com';

    /**
     * @var string
     */
    private $key;

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     * @return Postage
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Postage
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param $recipient
     * @param $subject
     * @param $body
     * @param $header
     * @param null $variables
     * @return mixed
     */
    public function mail($recipient, $subject, $body, $header, $variables = null)
    {

        $content = array(
            'recipients' => $recipient,
            'headers' => array_merge($header, array('Subject' => $subject)),
            'variables' => $variables,
            'uid' => time()
        );

        if (is_string($body)) {
            $content['template'] = $body;
        } else {
            $content['content'] = $body;
        }

        return $this->post(
            'send_message',
            json_encode(
                array(
                    'api_key' => $this->getKey(),
                    'arguments' => $content
                )
            )
        );
    }

    /**
     * @param $method
     * @param $content
     * @return mixed
     */
    public function post($method, $content)
    {
        $ch = curl_init($this->getHostname() . '/v.1.0/' . $method . '.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
}