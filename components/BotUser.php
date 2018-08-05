<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.accounts.users.SWebUser');
Yii::import('common.modules.chatbots.components.ChatbotContext');
Yii::import('common.modules.chatbots.payloads.LiveChatMetadata');
/**
 * Description of BotUser
 *
 * @author kwlok
 */
class BotUser extends SWebUser 
{
    private $_key = '__bot_session_id';//session key
    private $_livechat = '__bot_livechat';//livechat session key
    /**
     * OVERRIDDEN
     * @param type $fromCookie
     */
    public function afterLogin($fromCookie)
    {        
        parent::afterLogin($fromCookie);
    }
    /**
     * Check if has session id
     * @return boolean whether the current application user is a guest.
     */
    public function hasSessionId(ChatbotContext $context)
    {
        return $this->getSessionId($context)!=null;
    }
    /**
     * Get session id     
     * @param ChatbotContext $context  
     * @return type
     */
    public function getSessionId(ChatbotContext $context)
    {
        return Yii::app()->cache->get($this->_key.$context->session);
    }
    /**
     * Set session id (use cache as cookie is not used)
     * @param ChatbotContext $context  
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     */
    public function setSessionId(ChatbotContext $context,$expire=86400)
    {
        Yii::app()->cache->set($this->_key.$context->session, $context->session, $expire);//expires at 24 hours
    }      
    /**
     * Delete session session
     * @param ChatbotContext $context  
     * @return type
     */
    public function deleteSessionId(ChatbotContext $context)
    {
        return Yii::app()->cache->delete($this->_key.$context->session);
    }
    /**
     * Get session account      
     * @param ChatbotContext $context  
     * @return type
     */
    public function getSessionAccount(ChatbotContext $context)
    {
        return Yii::app()->cache->get($this->_key.$context->session.'_account');
    }
    /**
     * Set session account id (use cache as cookie is not used)
     * @param ChatbotContext $context  
     * @param int $account_id  
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     */
    public function setSessionAccount(ChatbotContext $context, $account_id,$expire=86400)
    {
        Yii::app()->cache->set($this->_key.$context->session.'_account', $account_id, $expire);//expires at 24 hours
    }      
    /**
     * Delete session session
     * @param ChatbotContext $context  
     * @return type
     */
    public function deleteSessionAccount(ChatbotContext $context)
    {
        return Yii::app()->cache->delete($this->_key.$context->session.'_account');
    }
    /**
     * Clear session
     * @param ChatbotContext $context
     */
    public function clearSession(ChatbotContext $context)
    {
        $this->deleteSessionId($context);
        $this->deleteSessionAccount($context);
    }
    /**
     * Check if user is on live chat
     * @param ChatbotContext $context  
     * @return type
     */
    public function onLiveChat(ChatbotContext $context)
    {
        return $this->getLiveChatMetadata($context)!=null;
    }
    /**
     * Get session account      
     * @param ChatbotContext $context  
     * @return type
     */
    public function getLiveChatMetadata(ChatbotContext $context)
    {
        return LiveChatMetadata::decode(Yii::app()->cache->get($this->_livechat.$context->session));
    }
    /**
     * Start live chat session
     * @param ChatbotContext $context  
     * @param LiveChatMetadata $metadata  
     * @param integer $expire the number of seconds in which the cached value will expire. 0 means never expire.
     */
    public function startLiveChat(ChatbotContext $context, LiveChatMetadata $metadata,$expire=3600)
    {
        Yii::app()->cache->set($this->_livechat.$context->session, $metadata->toString(), $expire);//expires at 1 hour
    }      
    /**
     * End live chat session 
     * @param ChatbotContext $context  
     * @return type
     */
    public function endLiveChat(ChatbotContext $context)
    {
        return Yii::app()->cache->delete($this->_livechat.$context->session);
    }
    
}

