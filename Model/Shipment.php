<?php

namespace Modules\GoShippo\Model;

use Lightning\Tools\Communicator\RestClient;
use Lightning\Tools\Configuration;
use Lightning\Tools\Output;

class Shipment {

    const ENDPOINT = 'https://api.goshippo.com/v1';
    const SELECTION_METHOD_LEAST = 1;

    protected $privateKey = '';
    protected $shipmentData;

    public function __construct($shipmentData) {
        $this->privateKey = Configuration::get('goshippo.private_token');
        $this->shipmentData = $shipmentData;
    }

    public function create() {
        $client = new RestClient(static::ENDPOINT);
        $client->setHeader('Authorization', 'ShippoToken ' . $this->privateKey);
        $client->sendJSON(true);
        $client->set('object_purpose', 'PURCHASE');
        $client->set('address_from', $this->shipmentData->getFromAddress());
        $client->set('address_to', $this->shipmentData->getToAddress());
        $client->set('parcel', $this->shipmentData->getParcel());
        $client->set('reference_1', $this->shipmentData->reference_1);
        $client->set('reference_2', $this->shipmentData->reference_2);
        $client->set('metadata', $this->shipmentData->metadata);
        $client->set('async', 'false');
        $client->callPost('shipments');
        $this->rateList = $client->getResults();
    }

    public function charge($providers = null, $method = self::SELECTION_METHOD_LEAST) {
        // Find the correct provider
        $selectedOption = null;
        foreach ($this->rateList['rates_list'] as $option) {
            if (!empty($providers) && !in_array($option['provider'], $providers)) {
                // This is not an approved provider.
                continue;
            }

            if (empty($selectedOption)) {
                // There are no other options, so this is the first or only.
                $selectedOption = $option;
                continue;
            }

            if ($method == self::SELECTION_METHOD_LEAST && floatval($option['amount']) < floatval($selectedOption['amount'])) {
                $selectedOption = $option;
            }
        }

        // Charge the shipment
        $client = new RestClient(static::ENDPOINT);
        $client->setHeader('Authorization', 'ShippoToken ' . $this->privateKey);
        $client->set('rate', $selectedOption['object_id']);
        $client->set('label_file_type', 'PDF');
        $client->set('async', 'false');
        $client->callPost('transactions');
        $this->labelResult = $client->getResults();
    }

    public function getLabelURL() {
        if (empty($this->labelResult)) {
            Output::error('There is no shipping label');
        } else {
            return $this->labelResult['label_url'];
        }
    }
}
