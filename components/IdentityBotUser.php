<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.accounts.users.IdentityCustomer');
Yii::import('common.components.actions.api.models.ApiOauthAuthorizationCodes');
/**
 * IdentityBotUser represents the data needed to identity a bot oauth user.
 * It simply matching the authorization code and return back matched account id
 * 
 * @property integer $id The matched account id
 * @property string $name The Oauth client id
 * @property string $password The authorization code
 * 
 * @author kwlok
 */
class IdentityBotUser extends IdentityCustomer
{
    /**
     * Disable cross app login
     */
    protected $crossAppLogin = false;
    /**
     * Authenticates a user (verify against the obtained authorization code
     *
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $oauth = ApiOauthAuthorizationCodes::model()->find('client_id=:cid AND authorization_code=:code',[':cid'=>$this->name,':code'=>$this->password]);
                    
        if ($oauth===null) {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        }
        elseif ($this->password!=$oauth->authorization_code){//password is the authorization code
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        }
        elseif ($oauth->account->isSuspended()){
            $this->errorCode=self::ERROR_USER_SUSPENDED;
        }
        elseif ($oauth->account->pendingActivation() && $oauth->activate_time==null) {
            $this->errorCode=self::ERROR_USER_INACTIVE;
        }
        elseif (!$oauth->account->isActive()){
            $this->errorCode=self::ERROR_USER_INACTIVE;
        }
        elseif ($oauth->account->isActive() && $oauth->account->activate_time!=null) {
            $this->authenticateRole($oauth->account,$this->getRole());
        }
        
        if ($this->errorCode!=self::ERROR_NONE)
            logError(__METHOD__.' error code '.$this->errorCode,[],false);
        
        return !$this->errorCode;
    }
}