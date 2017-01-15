<?php

namespace Modules\GoShippo\Pages;

use Exception;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Configuration;
use Lightning\Tools\Database;
use Lightning\Tools\Navigation;
use Lightning\Tools\Output;
use Lightning\Tools\Request;
use Lightning\Tools\Template;
use Lightning\View\Page;
use Modules\Checkout\Model\LineItem;
use Modules\Checkout\Model\Order;
use Modules\GoShippo\Model\Shipment;
use Modules\GoShippo\Model\ShipmentData;

class Label extends Page {

    protected $page = ['ship-confirmation-print', 'GoShippo'];

    public function hasAccess() {
        return ClientUser::requireAdmin();
    }

    /**
     * The shipping page handler.
     */
    public function get() {
        $order = Order::loadByID(Request::get('id', Request::TYPE_INT));
        if (empty($order)) {
            throw new Exception('Order now found.');
        }
        if ($order->shipped > 0) {
            throw new Exception('This item has already been shipped');
        }
        $template = Template::getInstance();
        $template->set('order', $order);
    }

    /**
     * Handles shipping page submissions.
     */
    public function post() {
        $redirect_params = [];

        $order = Order::loadByID(Request::get('id', Request::TYPE_INT));
        if (empty($order)) {
            throw new Exception('Order now found.');
        }
        if ($order->shipped > 0) {
            throw new Exception('This item has already been shipped');
        }

        // Load shipping data
        $shipping_address = $order->getShippingAddress();
        $user = $order->getUser();
        $from_address = Configuration::get('modules.checkout.from_address');

        // Create the shipment.
        $shipment_data = new ShipmentData();
        $shipment_data->setFromAddress($from_address['name'], $from_address['company'], $from_address['street'], $from_address['street2'], $from_address['city'], $from_address['state'], $from_address['zip'], $from_address['country'], $from_address['phone'], $from_address['email']);
        $shipment_data->setToAddress($shipping_address->name, '', $shipping_address->street, $shipping_address->street2, $shipping_address->city, $shipping_address->state, $shipping_address->zip, $shipping_address->country, '', $user->email);

        // Set the package size.
        $height = Request::get('package-height', Request::TYPE_INT);
        $width = Request::get('package-width', Request::TYPE_INT);
        $length = Request::get('package-length', Request::TYPE_INT);
        $shipment_data->setParcelInches($length, $width, $height);
        $weight = Request::get('package-weight', Request::TYPE_INT);
        switch (Request::get('package-weight-units')) {
            case 'oz':
                $shipment_data->setParcelOz($weight);
                break;
            case 'lb':
                $shipment_data->setParcelLbs($weight);
                break;
        }

        // Create the label.
        $shipment = new Shipment($shipment_data);
        $shipment->create();
        $shipment->charge();

        // Get the popup window and redirect.
        $redirect_params['label-popup'] = $shipment->getLabelURL();

        // Mark the line items and order as shipped.
        foreach ($order->getItemsToFulfillWithHandler('goshippo') as $item) {
            /* @var LineItem $item */
            $item->markFulfilled();
        }
        $order->markFullfilled();

        // Redirect.
        Navigation::redirect('/admin/orders', $redirect_params);
    }
}