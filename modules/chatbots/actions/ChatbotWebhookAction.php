<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.chatbots.models.Chatbot');
/**
 * Description of ChatbotWebhookAction
 *
 * @author kwlok
 */
abstract class ChatbotWebhookAction extends CAction
{
    protected $chatbot;
    protected $clientOwner;
    /**
     * Find the client object
     * @see ChatbotsModule::getChatbotClient()
     */
    protected function findClient()
    {
        foreach ($_GET as $key => $value) {//assuming webhook token is the only GET params without values
            if (strlen($value)==0){
                $webhookToken = $key;
                return $webhookToken;//webbook token is client id in fact
            }
        }
        throwError403('Client not found');
    }   
    /**
     * Find the chatbot
     * Internally it will check if user has subscription for paid service
     * @see Chatbot::locateClient()
     */
    protected function findChatbot()
    {
        if (!isset($this->chatbot)){
            $client = $this->findClient();
            $chatbot = Chatbot::findClient($client);
            if ($chatbot!=null)
                $this->chatbot = $chatbot;
            else
                throwError403('Chatbot not found');
        }
        return $this->chatbot;
    }   
    /**
     * Find the client owner (its underlying model class)
     * @see Chatbot::locateClient()
     */
    protected function findClientOwner()
    {
        if (!isset($this->clientOwner)){
            $chatbot = $this->findChatbot();
            $this->clientOwner = $chatbot->owner;
        }
        return $this->clientOwner;
    }   
    /**
     * Find the client owner (its underlying model class)
     * @see ShopSetting::getValue()
     */
    protected function findClientAttribute($attribute)
    {
        $clientOwner = $this->findClientOwner();
        $value = $clientOwner->getClientAttribte($attribute);
        if ($value!=null)
            return $value;
        else
            throwError403($attribute.' not found');
        
    }      
}
