<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.components.actions.api.models.ApiOauthClientTrait');
/**
 * Description of OauthController
 * 
 * Support facebook messenger account linking callback url
 * This will encode the facebook redirect_uri including its account_linking_token as 'oauth-payload' and 
 * redirect to app login url to handle.
 * When login is successful, the app will redirect back to this controller action and 
 * further rediect back to Facebook according to facebook account linking requirement.
 * 
 * @see https://developers.facebook.com/docs/messenger-platform/account-linking/authentication
 * 
 * @author kwlok
 */
class OauthController extends SController 
{
    use ApiOauthClientTrait;
    /**
     * The oauth client property
     * @var type 
     */
    private $_oauthClient;
    /**
     * @return array action filters
     */
    public function filters()
    {
        return [
            'accessControl', 
        ];
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return [
            ['allow',  
                'actions'=> ['authorize'],
                'users'=> ['*'],
            ],
            //default deny all users anything not specified       
            ['deny',  
                'users'=> ['*'],
            ],
        ];
    }    
    /**
     * Authorize action
     * Expect GET parameters: provider, app_id, user_id
     * @see AccountLinkView for use case example
     */
    public function actionAuthorize()
    {
        logTrace(__METHOD__.' get',$_GET);

        //Scenario 2:  Handles account linking after successful login redirect after user has login and authorization_code is obtained!
        if (isset($_GET['oauth_payload']) && isset($_GET['authorization_code'])){
            $redirectUri = base64_decode($_GET['oauth_payload']);//redirect uri
//            $parts = parse_url($redirectUri);
//            parse_str($parts['query'], $query);
//            $account_linking_token = $query['account_linking_token'];//can be used to call api to get PSID
            $redirectUri .= '&authorization_code='.$_GET['authorization_code'];
            logTrace(__METHOD__.' bot redirect uri='.$redirectUri);
            $this->redirect($redirectUri);
            Yii::app()->end();
        }
        //Scenario 1: Handles oauth callback url 
        else {
            $returnUrl = $this->module->getOAuthUrl([
               'oauth_payload' => base64_encode($_GET['redirect_uri']),//param redirect_uri is passed by Facebook
            ]);
            $this->redirect($this->findLoginUrl($returnUrl));
            Yii::app()->end();
        }
    }
    /**
     * Get the oauth client
     * @return type
     * @throws CException
     */
    public function getOAuthClient()
    {
        if (!isset($this->_oauthClient)){
            $client = $this->findOAuthClient(param('OAUTH_CLIENT_CHATBOT'));
            //update redirect_uri if not tally
            if ($client->redirect_uri!=$this->module->getOAuthUrl()){
                $client->redirect_uri = $this->module->getOAuthUrl();
                $client->save();
            }
            $this->_oauthClient = $client;
        }
        return $this->_oauthClient;
    }    
    
    protected function findLoginUrl($returnUrl)
    {
        $queryString = 'oauthClient='.base64_encode($this->oAuthClient->client_id).'&returnUrl='.$returnUrl;
        
        if (isset($_GET['client'])){
            $chatbot = Chatbot::model()->locateClient($_GET['client'])->find();
            if ($chatbot!=null && $chatbot->owner->domain!=null){
                return 'https://'.$chatbot->owner->domain.'/login?'.$queryString;//shop login url 
            }
        }      
        //default url if no shop subdomain
        return app()->urlManager->createHostUrl('/signin?'.$queryString,true);//platform login url
    }
}
