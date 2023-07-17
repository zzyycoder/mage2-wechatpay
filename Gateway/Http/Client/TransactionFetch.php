<?php

namespace Zzyy\WechatPay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\TransferInterface;
use WeChatPay\Transformer;
use Magento\Payment\Gateway\Http\ClientInterface;
use Zzyy\WechatPay\Helper\Data;

class TransactionFetch implements ClientInterface
{
    private Data $helper;
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $sdk = $this->helper->getWechatSdk();
        return $sdk->v2->pay->orderquery
            ->postAsync($transferObject->getBody())
            ->then(static function ($response) {
                return Transformer::toArray((string)$response->getBody());
            })
            ->otherwise(static function ($e) {
                return ['return_code' => $e->getCode(), 'return_msg' => $e->getMessage()];
            })
            ->wait();
    }
}
