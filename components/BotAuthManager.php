<?php
/**
 * This file is part of Shopbay.org (https://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.chatbots.components.ChatbotContext');
/**
 * Description of BotAuthManager
 * Extends SAuthManager to act as AccountManager to handle login/logout
 * 
 * @author kwlok
 */
class BotAuthManager extends SAuthManager
{
    /**
     * Login user using its authorization code (obtained via oauth)
     * @param ChatbotContext $context
     * @param ChatbotUser $user
     * @return boolean
     */
    public function oauthLogin(ChatbotContext $context, ChatbotUser $user)
    {
        $cid = IdentityCustomer::createCid($context->chatbot->owner->id, param('OAUTH_CLIENT_CHATBOT'));
        $identity = new IdentityBotUser($cid,$user->authorizationCode);
        $identity->authenticate();
        if ($identity->errorCode==Identity::ERROR_NONE){
            Yii::app()->user->login($identity);
            logInfo(__METHOD__." account is logged in.",$identity->getId());
            $user->bindTo($identity->getId());
            if ($user->isBond)
                logInfo(__METHOD__." ChatbotUser is linked.",$user->account_id);

            Yii::app()->user->setSessionId($context);
            Yii::app()->user->setSessionAccount($context,$identity->getId());
            //todo record activity (login)
            return true;
        }
        else{
            return false;
        }
    }     
    /**
     * Logout user (obtained via oauth)
     * @param ChatbotUser $user
     * @return boolean
     */
    public function oauthLogout(ChatbotUser $user)
    {
        $user->clearSessionData();
        $context = new ChatbotContext($user->client_id, $user->app_id, $user->user_id);
        Yii::app()->user->clearSession($context);
        Yii::app()->user->logout();
        logInfo(__METHOD__." account logged out");
    }         
}
