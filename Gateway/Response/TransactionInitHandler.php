<?php

namespace Zzyy\WechatPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order;

class TransactionInitHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     * @throws CommandException
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        // set state pending_payment
        $handlingSubject['stateObject']->setData('state', Order::STATE_PENDING_PAYMENT);
        /** @var Order\Payment $payment */
        $payment = $handlingSubject['payment']->getPayment();
        $payment->setAdditionalInformation('code_url', $response['code_url']);
    }
}
