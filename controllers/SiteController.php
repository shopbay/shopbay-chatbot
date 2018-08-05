<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Description of SiteController
 *
 * @author kwlok
 */
class SiteController extends SSiteController 
{
    /**
     * Specifies the local access control rules.
     * @see SSiteController::accessRules()
     * @return array access control rules
     */
    public function accessRules()
    {
        return array_merge([
            ['allow', 
                'actions'=>['index'], 
                'users'=>['*'],
            ]
        ],parent::accessRules());//parent access rules has to put at last
    }      
    /**
     * This is the default 'index' action that is invoked
     * when an action is explicitly requested by users.
     */
    public function actionIndex() 
    {
        echo file_get_contents('403.html');
        echo '<div class="copyright">'.Sii::t('sii','Copyright &copy; {year}.',array('{year}'=>date('Y'))).'</div>';
    }
}