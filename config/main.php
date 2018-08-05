<?php
logHttpHeader();
$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..';
$appName = basename(dirname(dirname(__FILE__)));// The app directory name, e.g. shopbay-app
$webapp = new SWebApp($appName,$basepath);
//$webapp->enableSystemTrace = true;
$webapp->import([
    'application.models.*',
    'application.components.*',
]);
$webapp->setCommonComponent('authManager',['class'=> 'BotAuthManager']);
$webapp->addComponents([
    'user'=>[
        'class'=>'BotUser',
        'allowAutoLogin'=>false,//true to cookie-based authentication
        'loginUrl'=>null,//not required
    ],
    'request' => [
        'class' => 'common.components.SHttpRequest',
        'enableCsrfValidation' => false,
        'enableCookieValidation' =>false,//bot app not using cookie
    ],    
    'session' => [
        'cookieMode' => 'none',
    ],
    'urlManager'=> [
        'class'=>'SUrlManager',
        'hostDomain'=>$webapp->params['HOST_DOMAIN'],
        'shopDomain'=>$webapp->params['SHOP_DOMAIN'],
        'cdnDomain'=>$webapp->params['SHOP_DOMAIN'],//Set to follow shop domain
        'forceSecure'=>true,
        'urlFormat'=>'path',
        'showScriptName'=>false,
        'rules'=> [
            'messenger/webhook/*' => 'chatbots/messenger/webhook',
            'oauth/authorize/*' => 'chatbots/oauth/authorize',
        ],
    ],
    'filter'=> [
        'class'=>'SFilter',
        'rules'=>[],
    ],
]);
return $webapp->toArray();