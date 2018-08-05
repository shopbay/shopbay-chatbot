<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.shops.components.ShopPage');
/**
 * Description of BaseChatbotController
 *
 * @author kwlok
 */
class BaseChatbotController extends SController 
{
    /**
     * The action class used for webhook
     * @var type 
     */
    protected $webhookActionClass = 'undefined';
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
        $allow = ['allow'];
        if (is_bool($this->module->enableWebhook) && $this->module->enableWebhook){
            $allow['actions'] = ['webhook'];
            $allow['users'] = ['*'];
            //optional, IP whitelist 
            //$allow['ips'] = ['127.0.0.1'];
        }
        return [
            $allow,
            //default deny all users anything not specified       
            ['deny',  
                'users'=> ['*'],
            ],
        ];
    }    
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return [
            'webhook' => [
                'class'=>$this->webhookActionClass,
            ],
        ];
    }
}
