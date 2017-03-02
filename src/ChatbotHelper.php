<?php

namespace DonMarkus;


use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use pimax\FbBotApp;
use pimax\Messages\Message;

class ChatbotHelper
{

    public $config;
    protected $chatbotAI;
    protected $facebookSend;
    protected $log;
    private $input;

    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();
        $this->config = require __DIR__ . '/config.php';
        $this->chatbotAI = new ChatbotAI($this->config);
        $this->facebookSend = new FbBotApp($this->config['access_token']);
        $this->log = new Logger('general');
        $this->log->pushHandler(new StreamHandler('debug.log'));
        $this->input = $this->getInputData();
    }

    /**
     * @return mixed
     */
    private function getInputData()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Get the sender id of the message
     * @return int
     * @internal param $input
     */
    public function getSenderId()
    {
        return $this->input['entry'][0]['messaging'][0]['sender']['id'];
    }

    /**
     * Get the user's message from input
     * @return mixed
     * @internal param $input
     */
    public function getMessage()
    {
        return $this->input['entry'][0]['messaging'][0]['message']['text'];
    }

    /**
     * Check if the callback is a user message
     * @return bool
     * @internal param $input
     */
    public function isMessage()
    {
        return isset($this->input['entry'][0]['messaging'][0]['message']['text']) && !isset
            ($this->input['entry'][0]['messaging'][0]['message']['is_echo']);

    }

    /**
     * Get the answer to a given user's message
     * @param null $api
     * @param string $message
     * @return string
     */
    public function getAnswer($message, $api = null)
    {

        if ($api === 'apiai') {
            return $this->chatbotAI->getApiAIAnswer($message);
        } elseif ($api === 'witai') {
            return $this->chatbotAI->getWitAIAnswer($message);
        } elseif ($api === 'rates') {
            return $this->chatbotAI->getForeignExchangeRateAnswer($message);
        } else {
            return $this->chatbotAI->getAnswer($message);
        }

    }

    /**
     * Send a reply back to Facebook chat
     * @param $senderId
     * @param $replyMessage
     * @return array
     */
    public function send($senderId, string $replyMessage)
    {
        return $this->facebookSend->send(new Message($senderId, $replyMessage));
    }


    /**
     * Verify Facebook webhook
     * This is only needed when you setup or change the webhook
     * @param $request
     * @return mixed
     */
    public function verifyWebhook($request)
    {
        if (!isset($request['hub_challenge'])) {
            return false;
        };

        $hubVerifyToken = null;
        $hubVerifyToken = $request['hub_verify_token'];
        $hubChallenge = $request['hub_challenge'];

        if (null !== $hubChallenge && $hubVerifyToken === $this->config['webhook_verify_token']) {

            echo $hubChallenge;
        }


    }
}