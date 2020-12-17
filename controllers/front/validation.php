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
class Waave_PgValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cartId = (int)Tools::getValue('id_cart');
        $row = DB::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . $cartId);

        if ($row['id_customer'] == 0 || $row['id_address_delivery'] == 0 || $row['id_address_invoice'] == 0 || !$this->module->active) {
            header("HTTP/1.0 404 Not Found");
            die($this->module->l('Error, cart not found.', 'validation'));
        }

        $cart = new Cart($cartId);

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            header("HTTP/1.0 404 Not Found");
            die($this->module->l('Error, customer not found.', 'validation'));
        }

        PrestaShopLogger::addLog('Waave - process return url', 1, null, 'Waave');
        PrestaShopLogger::addLog('Validate order', 1, null, 'Waave');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $request = [
            'id_cart' => $cart->id,
            'id_module' => $this->module->id,
            'key' => $this->context->customer->secure_key
        ];
        $callbackUrl = $this->context->link->getModuleLink($this->module->name, 'validation', $request, true);

        $valid = $this->validateSignature($data, $callbackUrl);
        if (!$valid) {
            header("HTTP/1.0 500 Error");
            die('Error, signature is invalid.');
        }

        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        if ($total != $data['amount']) {
            header("HTTP/1.0 500 Error");
            die('Error, amount is invalid.');
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        $valid = $this->module->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $this->module->displayName, NULL, [], (int)$currency->id, false, $customer->secure_key);

        if (!$valid) {
            header("HTTP/1.0 500 Error");
            die('Error, order is invalid.');
        }

        $status = $data['status'];

        PrestaShopLogger::addLog('Waave - request validation is done', 1, null, 'Waave');

        die('OK-PRESTASHOP');
    }

    /**
     * Validate signature
     * 
     * @param array $data
     */
    private function validateSignature($data, $uri) {
        $secretKey = Configuration::get('PRIVATE_KEY');
        $body      = json_encode($data);

        $signature       = hash("sha256", $secretKey . $uri . $body);
        $headerSignature = isset($_SERVER['HTTP_X_API_SIGNATURE']) ? $_SERVER['HTTP_X_API_SIGNATURE'] : '';

        if ($signature === $headerSignature) {
            PrestaShopLogger::addLog('Signature is valid.', 1, null, 'Waave');
            return true;
        }

        PrestaShopLogger::addLog('Signature is invalid.', 1, null, 'Waave');
        PrestaShopLogger::addLog('Signature: ' . $signature, 1, null, 'Waave');
        PrestaShopLogger::addLog('Secret key: ' . $secretKey, 1, null, 'Waave');
        PrestaShopLogger::addLog('Uri: ' . $uri, 1, null, 'Waave');
        PrestaShopLogger::addLog('Body: ' . $body, 1, null, 'Waave');

        return false;
    }
}
