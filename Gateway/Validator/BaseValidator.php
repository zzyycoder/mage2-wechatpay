<?php

namespace Zzyy\WechatPay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class BaseValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        if ($validationSubject['response']['return_code'] !== 'SUCCESS') {
            return $this->createResult(false, [$validationSubject['response']['return_msg']]);
        }
        if ($validationSubject['response']['result_code'] !== 'SUCCESS') {
            return $this->createResult(false, [$validationSubject['response']['err_code_des']]);
        }
        return $this->createResult(true);
    }
}
