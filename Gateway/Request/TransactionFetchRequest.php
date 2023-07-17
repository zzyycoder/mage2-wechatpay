<?php

namespace Zzyy\WechatPay\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Zzyy\WechatPay\Helper\Data;

class TransactionFetchRequest implements BuilderInterface
{
    private Data $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $payment = $buildSubject['payment'];
        $paymentMethod = $payment->getPayment()->getMethodInstance();
        $this->helper->setCurrentPayment($payment->getPayment());

        return ['xml' => [
            'appid' => $paymentMethod->getConfigData('app_id'),
            'mch_id' => $paymentMethod->getConfigData('mchid'),
            'transaction_id' => $buildSubject['transactionId'],
        ]];
    }
}
