<?php

namespace Zzyy\WechatPay\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Zzyy\WechatPay\Helper\Data;

class TransactionInitRequest implements BuilderInterface
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
        $order = $payment->getOrder();
        $paymentMethod = $payment->getPayment()->getMethodInstance();
        $this->helper->setCurrentPayment($payment->getPayment());

        return ['xml' => [
            'appid' => $paymentMethod->getConfigData('app_id'),
            'mch_id' => $paymentMethod->getConfigData('mchid'),
            'body' => '测试',
            'out_trade_no' => $order->getOrderIncrementId(),
            'total_fee' => $order->getGrandTotalAmount() * 100,
            'trade_type' => 'NATIVE',
            'fee_type' => $order->getCurrencyCode(),
            'notify_url' => $this->helper->getNotifyUrl($order->getId())
        ]];
    }
}
