<?php

namespace Zzyy\WechatPay\Gateway\Validator;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;

class OutTradeNoValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];
        /** @var OrderAdapterInterface $order */
        $order = $validationSubject['payment']->getOrder();
        if (empty($response['out_trade_no']) || $response['out_trade_no'] != $order->getOrderIncrementId()) {
            $fails = ['transaction out_trade_no error: '.$response['out_trade_no']];
            return $this->createResult(false, $fails);
        }
        return $this->createResult(true);
    }
}
