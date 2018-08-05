<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.chatbots.providers.messenger.events.*');
Yii::import('common.modules.chatbots.providers.messenger.controllers.*');
Yii::import("common.modules.plans.models.Subscription");
Yii::import("common.modules.plans.models.Feature");
Yii::import('chatbots.actions.ChatbotWebhookAction');
/**
 * Description of MessengerWebhookAction
 *
 * @author kwlok
 */
class MessengerWebhookAction extends ChatbotWebhookAction
{
    /**
     * Callback events to be handled
     * @var type 
     */
    protected static $callbackEvents = [
        'onDelivery'      => 'DeliveryController',
        'onMessage'       => 'MessageController',
        'onPostback'      => 'PostbackController',
        'onMessageRead'   => 'MessageReadController',
        'onAccountLink'   => 'AccountLinkController',
        'onAuthentication'=> 'OptInController',
    ];
    /**
     * Action logic
     */
    public function run() 
    {
        $reqBody = request()->getRawBody();
        logTrace(__METHOD__.' request body',$reqBody);
        
        //Process either GET or POST request
        if (request()->getIsPostRequest()){
            //Request must contain valid signature
            $this->verifyRequestSignature($reqBody);
            $chatbot = $this->verifyChatbot();
            $this->handleCallback($chatbot,json_decode($reqBody,true));
        }
        else {
            echo $this->verifyToken();
        }
    }
    /**
     * Verify chatbot is a "licensed" one - user has bought the package to use chatbot service
     * @return type
     */
    protected function verifyChatbot()
    {
        $bot = $this->findChatbot();
        if (!Subscription::hasService($bot->owner->subscription, Feature::$integrateFacebookMessenger))
            throwError404('Service not found');
                
        logInfo(__METHOD__.' Service '.Feature::$integrateFacebookMessenger.' found for subscription',$bot->owner->subscription->planName);
        if (!$bot->isVerified)
            $bot->saveAsVerified();
        return $bot;
    }
    /**
     * Check that the token used in the Webhook setup is the same token defined here.
     * Expecting facebook to send following query:
     *   /<webhook_route>?hub.mode=subscribe&hub.challenge=1443584512&hub.verify_token=abcd_1234_test
     * Note: e.g. $_GET hub.mode is auto translated into hub_mode, same for rest  
     * 
     * Upon successful verification, save Chatbot as verified
     * @return string challenge passed from Facebook
     */
    protected function verifyToken()
    {
        logInfo(__METHOD__.' request GET',$_GET);
        if ($_GET['hub_mode']=='subscribe' && $_GET['hub_verify_token']==$this->verifyToken){
            $this->verifyChatbot();
            return $_GET['hub_challenge'];
        }
        else
            throwError403('Invalid token');
    }
    /**
     * Verify that the callback came from Facebook. Using the App Secret from 
     * the App Dashboard, we can verify the signature that is sent with each 
     * callback in the x-hub-signature field, located in the header.
     *
     * https://developers.facebook.com/docs/graph-api/webhooks#setup
     *
     */
    protected function verifyRequestSignature($reqBody) 
    {
        //verify if to proceed, check client setting
        if ($this->verifyRequestSignatureRequired){
            logInfo(__METHOD__.' skip!');
            return;
        }
        
        //proceed verification
        if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
            logError(__METHOD__." Couldn't validate the signature.");
            throwError403('Unauthorized request signature');
        } 
        else {
            $expected = 'sha1='.hash_hmac('sha1',$reqBody,$this->appSecret);
            if ($_SERVER['HTTP_X_HUB_SIGNATURE'] != $expected) {
                logError(__METHOD__." failed!!");
                throwError403('Invalid request signature');
            }
            else
                logInfo(__METHOD__." ok.");
        }
    }    
    /**
     * All callbacks for Messenger are POST-ed. They will be sent to the same webhook. 
     * Facebook page app need to subscribe to these events to receive callbacks for facebook page. 
     * @see https://developers.facebook.com/docs/messenger-platform/product-overview/setup#subscribe_app
     * 
     * @param Chatbot $chatbot the bot model
     * @param array $reqBody the HTTP request body
     */
    protected function handleCallback($chatbot,$reqBody)
    {
        //Register callback events
        foreach (self::$callbackEvents as $event => $processor) {
            $this->attachEventHandler($event,[new $processor($this->pageAccessToken),'process']);
            logTrace(__METHOD__." callback event $event attached");
        }        
        //Make sure this is a page subscription
        if ($reqBody['object'] == 'page') {
            //Iterate over each entry; there may be multiple if batched           
            foreach ($reqBody['entry'] as $entry) {
                $pageId = $entry['id'];
                $timeOfEvent = $entry['time'];
                // Iterate over each messaging event
                foreach ($entry['messaging'] as $messaging) {
                    $sender = $messaging['sender']['id'];
                    $recipient = $messaging['recipient']['id'];
                    if (isset($messaging['delivery']))
                        $this->onDelivery(new DeliveryEvent($chatbot,$pageId, $sender, $recipient, $timeOfEvent, $messaging['delivery']));
                    elseif (isset($messaging['message']))
                        $this->onMessage(new MessageEvent($chatbot,$pageId, $sender, $recipient, $messaging['timestamp'], $messaging['message']));
                    elseif (isset($messaging['postback']))
                        $this->onPostback(new PostbackEvent($chatbot,$pageId, $sender, $recipient, $messaging['timestamp'], $messaging['postback']));
                    elseif (isset($messaging['read']))
                        $this->onMessageRead(new MessageReadEvent($chatbot,$pageId, $sender, $recipient, $timeOfEvent, $messaging['read']));
                    elseif (isset($messaging['account_linking']))
                        $this->onAccountLink(new AccountLinkEvent($chatbot,$pageId, $sender, $recipient, $timeOfEvent, $messaging['account_linking']));
                    elseif (isset($messaging['optin']))
                        $this->onAuthentication(new OptInEvent($chatbot,$pageId, $sender, $recipient, $messaging['timestamp'], $messaging['optin']));
                    else
                        logError(__METHOD__.' unknown messaging event',$messaging);
                }
            }
            // Assume all went well.
            // Must send back a 200, within 20 seconds, to let facebook know you've 
            // successfully received the callback. Otherwise, the request will time out.
            echo 'OK';//with status 200
        }
    }
    /**
     * Raises an <code>onDelivery</code> event.
     * @param DeliveryEvent the event parameter
     */
    public function onDelivery($event)
    {
        $this->raiseEvent('onDelivery', $event);
    }
    /**
     * Raises an <code>onMessage</code> event.
     * @param MessageEvent the event parameter
     */
    public function onMessage($event)
    {
        $this->raiseEvent('onMessage', $event);
    }
    /**
     * Raises an <code>onMessageRead</code> event.
     * @param MessageReadEvent the event parameter
     */
    public function onMessageRead($event)
    {
        $this->raiseEvent('onMessageRead', $event);
    }
    /**
     * Raises an <code>onPostback</code> event.
     * @param PostbackEvent the event parameter
     */
    public function onPostback($event)
    {
        $this->raiseEvent('onPostback', $event);
    }
    /**
     * Raises an <code>onAccountLink</code> event.
     * @param AccountLinkEvent the event parameter
     */
    public function onAccountLink($event)
    {
        $this->raiseEvent('onAccountLink', $event);
    }
    /**
     * Raises an <code>onAuthentication</code> event.
     * @param OptInEvent the event parameter
     */
    public function onAuthentication($event)
    {
        $this->raiseEvent('onAuthentication', $event);
    }        
    /**
     * Get the page access token
     * @return type
     */
    protected function getPageAccessToken()
    {
        return $this->findClientAttribute('fbPageAccessToken');
    }
    /**
     * Get the facebook app secret
     * @return type
     */
    protected function getAppSecret()
    {
        return $this->findClientAttribute('fbSecret');
    }
    /**
     * Get the verify token
     * @return type
     */
    protected function getVerifyToken()
    {
        return $this->findClientAttribute('fbVerifyToken');
    }
    /**
     * Check if require to verify request signature
     * @return boolean
     */
    protected function getVerifyRequestSignatureRequired()
    {
        return $this->findClientAttribute('fbVerifyRequestSignature')==false;        
    }
}
