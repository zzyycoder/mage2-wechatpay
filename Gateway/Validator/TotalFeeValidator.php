<?php

namespace Zzyy\WechatPay\Gateway\Validator;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;

class TotalFeeValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        $valid = true;
        $fails = [];
        $response = $validationSubject['response'];
        /** @var OrderAdapterInterface $order */
        $order = $validationSubject['payment']->getOrder();
        if (empty($response['fee_type']) || $response['fee_type'] != $order->getCurrencyCode()) {
            $valid = false;
            $fails[] = 'transaction fee_type error: '.$response['fee_type'];
        }
        if (empty($response['total_fee']) || $response['total_fee'] != $order->getGrandTotalAmount() * 100) {
            $valid = false;
            $fails[] = 'transaction total_fee error: '.$response['total_fee'];
        }
        if (!$valid) {
            return $this->createResult(false, $fails);
        }
        return $this->createResult(true);
    }
}
