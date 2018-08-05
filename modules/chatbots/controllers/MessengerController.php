<?php
/**
 * This file is part of Shopbay.org (http://shopbay.org)
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Description of MessengerController
 *
 * @author kwlok
 */
class MessengerController extends BaseChatbotController 
{
    /**
     * The action class used for webhook
     * @var type 
     */
    protected $webhookActionClass = 'MessengerWebhookAction';
}
