<?php

use DonMarkus\ChatbotHelper;

require_once __DIR__ . '/../vendor/autoload.php';

// Create the chatbot helper instance
$chatbotHelper = new ChatbotHelper();

// Facebook webhook verification
$chatbotHelper->verifyWebhook($_REQUEST);

// Get the fb users data

$senderId = $chatbotHelper->getSenderId();

if ($senderId && $chatbotHelper->isMessage()) {

    // Get the user's message
    $message = $chatbotHelper->getMessage();

    // Example 1: Get a static message back
    $replyMessage = $chatbotHelper->getAnswer($message);

    // Example 2: Get foreign exchange rates
//     $replyMessage = $chatbotHelper->getAnswer($message, 'rates');

    // Example 3: If you want to use a bot platform like api.ai
    // Don't forget to place your Api.ai Client access token in the .env file
//     $replyMessage = $chatbotHelper->getAnswer($message, 'apiai');

    // Example 4: If you want to use a bot platform like wit.ai
    // Don't forget to place your Wit.ai Client access token in the .env file (WITAI_TOKEN)
    // $replyMessage = $chatbotHelper->getAnswer($message, 'witai');

    // Send the answer back to the Facebook chat
    $chatbotHelper->send($senderId, $replyMessage);

}
