<?php

namespace Zzyy\WechatPay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class TradeStateValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        $response = $validationSubject['response'];
        if (empty($response['trade_state']) || $response['trade_state'] !== 'SUCCESS') {
            return $this->createResult(false, ['transaction trade_state error: '.$response['trade_state']]);
        }
        return $this->createResult(true);
    }
}
