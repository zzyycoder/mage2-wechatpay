<?php

namespace Zzyy\WechatPay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use WeChatPay\Crypto\Hash;
use WeChatPay\Formatter;

class SignatureValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        $sign = $validationSubject['response']['sign'];
        $signType = $validationSubject['response']['sign_type'] ?? Hash::ALGO_MD5;
        $calculated = Hash::sign(
            $signType,
            Formatter::queryStringLike(Formatter::ksort($validationSubject['response'])),
            $validationSubject['payment']->getPayment()->getMethodInstance()->getConfigData('secret')
        );
        $signatureStatus = Hash::equals($calculated, $sign);
        if (!$signatureStatus) {
            return $this->createResult(false, ['Signature does not match.']);
        }
        return $this->createResult(true);
    }
}
