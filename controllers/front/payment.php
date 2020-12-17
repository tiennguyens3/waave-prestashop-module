<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class Waave_PgPaymentModuleFrontController extends ModuleFrontController
{
    const PROD_URL = 'https://pg.getwaave.co/waavepay/checkout';
    const SANDBOX_URL = 'https://staging-pg.getwaave.co/waavepay/checkout';

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'waave_pg') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        $waaveSandbox   = Configuration::get('WAAVE_SANDBOX');
        $actionUrl = self::PROD_URL;
        if ($waaveSandbox) {
            $actionUrl = self::SANDBOX_URL;
        }

        $accessKey = Configuration::get('ACCESS_KEY');
        $venueId = Configuration::get('VENUE_ID');

        $cancelUrl = $this->context->link->getPageLink('order', true, null, ['step' => 3]);
        $callbackUrl = $this->context->link->getModuleLink($this->module->name, 'validation', array(), true);

        $request = [
            'id_cart' => $cart->id,
            'id_module' => $this->module->id,
            'key' => $this->context->customer->secure_key
        ];
        $returnUrl = $this->context->link->getPageLink('order-confirmation', true, null, $request);

        $amount = $cart->getOrderTotal(true, Cart::BOTH);
        $referenceId = $cart->id;

        $this->addJqueryPlugin('fancybox');
        $this->registerJavascript(sha1('modules/waave_pg/views/js/waave_pg.js'), 'modules/waave_pg/views/js/waave_pg.js', ['priority' => 100]);

        $this->context->smarty->assign([
            'accessKey' => $accessKey,
            'venueId' => $venueId,
            'referenceId' => $referenceId,
            'amount' => $amount,
            'cancelUrl' => $cancelUrl,
            'returnUrl' => $returnUrl,
            'callbackUrl' => $callbackUrl,
            'actionUrl' => $actionUrl
        ]);

        $this->setTemplate('module:waave_pg/views/templates/front/payment_return.tpl');
    }
}
