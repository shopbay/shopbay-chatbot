<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
Yii::import('common.modules.chatbots.ChatbotWebhookTrait');
/**
 * Description of ChatbotsModule
 *
 * @author kwlok
 */
class ChatbotsModule extends SModule 
{
    use ChatbotWebhookTrait;
    /**
     * Init
     */
    public function init()
    {
        // import the module-level models and components
        $this->setImport([
            'chatbots.actions.*',
            'chatbots.controllers.*',
            'common.modules.chatbots.components.*',
            'common.modules.chatbots.models.*',
        ]);

        // import module dependencies classes
        $this->setDependencies([
            'classes'=>[],
            'sii'=>[],
        ]);  

        //$this->defaultController = 'to_be_implemented';
        
        if (!isset($this->webhookDomain))
            $this->webhookDomain = param('HOST_DOMAIN');
    }
    /**
     * Get the oauth url
     * @return string $chatbot The chatbot type
     */
    public function getOAuthUrl($params=[])
    {
        $url = Yii::app()->urlManager->createDomainUrl($this->webhookDomain,'/oauth/authorize',true);
        if (!empty($params))
            return $url.'?'.http_build_query($params);
        else
            return $url;
    }
    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        // Set the required components.
        $this->setComponents([
            'servicemanager'=>[
                'class'=>'common.services.ChatbotManager',
                'model'=>'Chatbot',
            ],
        ]);
        return $this->getComponent('servicemanager');
    }    
}
