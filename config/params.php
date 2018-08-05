<?php
return [
    /**
     * configuraion for local information 
     */
    'SITE_NAME' => readConfig('app','name'),
    'SITE_LOGO' => false, //indicate if to use a brand image as site logo; if false, SITE_NAME will be used
    /**
     * configuration for domain
     */
    'HOST_DOMAIN' => readConfig('domain','host'),    
    'API_DOMAIN' => readConfig('domain','api'),  
    'SHOP_DOMAIN' => readConfig('domain','shop'),  
    /**
     * configuration for Wit.ai integration
     */
    'WIT_AI_ON' => readConfig('wit','enable'),
    /**
     * configuration for oauth
     */
    'OAUTH_CLIENT_CHATBOT' => 'ShopbayChatbotApp',
];