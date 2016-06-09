<?php

namespace Modules\GoShippo\Model;

class ShipmentData {

    protected $fromAddress = [];
    protected $toAddress = [];
    protected $parcel = [];
    public $reference_1 = '';
    public $reference_2 = '';
    public $metadata = '';

    public function getFromAddress() {
        return $this->fromAddress;
    }

    public function setFromAddress($name, $company, $street1, $street2, $city, $state, $zip, $country, $phone, $email) {
        $this->fromAddress = [
            'name' => $name,
            'company' => $company,
            'street1' => $street1,
            'street2' => $street2,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
            'phone' => $phone,
            'email' => $email,
            'object_purpose' => 'PURCHASE',
        ];
    }

    public function getToAddress() {
        return $this->toAddress;
    }

    public function setToAddress($name, $company, $street1, $street2, $city, $state, $zip, $country, $phone, $email) {
        $this->toAddress = [
            'name' => $name,
            'company' => $company,
            'street1' => $street1,
            'street2' => $street2,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
            'phone' => $phone,
            'email' => $email,
            'object_purpose' => 'PURCHASE',
        ];
    }

    public function getParcel() {
        return $this->parcel;
    }

    public function setParcelInches($length, $width, $height) {
        $this->parcel = [
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'distance_unit' => 'in',
        ] + $this->parcel;
    }

    public function setParcelLbs($lbs) {
        $this->parcel = [
            'weight' => intval($lbs),
            'mass_unit' => 'lb',
        ] + $this->parcel;
    }

    public function setParcelOz($oz) {
        $this->parcel = [
                'weight' => intval($oz),
                'mass_unit' => 'oz',
            ] + $this->parcel;
    }

    public function setParcelTemplate($template) {
        $this->parcel['template'] = $template;
    }

    public function setParcelMeta($meta) {
        $this->parcel['metadata'] = $meta;
    }
}
