<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans {
    // TODO: define order model

    /**
     * CreateSnapTokenService constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();

        // TODO: assign order model to class attribute $this->order...
    }

    /**
     * Create snap token to show payment page.
     *
     * @return string $snapToken
     */
    public function getSnapToken() {
        // TODO: build params to get the midtrans snaptoken...
        $params = [
            'transaction_details' => [
                'order_id' => 'UNIQUE_ID',
                'gross_amount' => 0
            ],
            'item_details' => [],
            'customer_details' => []
        ];

        return Snap::getSnapToken($params);
    }
}
