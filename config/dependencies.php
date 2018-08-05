<?php
//Set aliases and import module dependencies
$root = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';

$depends = [
    'base'=> [
    //----------------------
    // Alias mapping
    //----------------------
        'common' => 'shopbay-kernel', //actual folder name
        'bot'=>'shopbay-chatbot',
        'api' => 'shopbay-api',
    ],
    //---------------------------
    // Common modules / resources
    //---------------------------    
    'module'=> [
        'common' => [
            'import'=> [
                'components.*',
                'components.behaviors.*',
                'controllers.*',
                'models.*',
                'extensions.*',
                'modules.chatbots.models.Chatbot',
                'modules.chatbots.models.ChatbotUser',
                'modules.news.models.*',
            ],
        ],
        'tasks'=>[
            'import'=> [
                'models.*',
                'behaviors.WorkflowBehavior',
            ],
        ],
        'rights' => [
            'import'=> [
                'components.*',
            ],
        ],
        'accounts' => [
            'import'=> [
                'components.*',
                'users.Identity',
                'users.Role',
                'users.Task',
                'users.WebUser',
            ],
        ],        
        'messages'=>[
            'import'=> [
                'models.Message',
            ],
        ],
        'media' => [
            'import'=> [
                'models.Media',
                'models.MediaAssociation',
                'models.SessionMedia',
            ],
        ],        
        'images' => [
            'import'=> [
                'components.*',
                'components.Img',
            ],
            'config'=> [
                'createOnDemand'=>true, // requires apache mod_rewrite enabled
            ],
        ],
        'shops' => [
            'import'=> [
                'models.ShopSetting',
                'models.ShopTheme',
                'behaviors.ShopBehavior',
                'behaviors.ShopConfigBehavior',
            ],          
        ],
        'comments' => [
            'import'=>[
                'models.Comment',
            ],
        ],
        'payments' => [
            'import'=>[
                'models.PaymentMethod',
            ],
        ],
        'likes' => [
            'import'=>[
                'models.Like',
            ],
        ],
        'pages' => [
            'import'=>[
                'models.Page',
            ],
        ],
        //plain modules contains components/behaviors/models without controllers/views
        'campaigns' => [],
        'products' => [],
        'shippings' => [],
        'taxes' => [],
        'news' => [],
    ],
    //----------------------
    // Local modules
    //----------------------
    'local'=> [
        'chatbots' => [
            'config'=> [
                'enableWebhook'=>true, 
            ],
        ],
    ],
];


// The app directory path, e.g. /path/to/shopbay-app
$appPath = dirname(dirname(__FILE__));

loadDependencies(ROOT,$depends, $appPath);

return $depends;