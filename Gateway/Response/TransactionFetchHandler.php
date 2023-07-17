<?php

namespace Zzyy\WechatPay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

class TransactionFetchHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     * @throws CommandException
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var OrderPaymentInterface $payment */
        $payment = $handlingSubject['payment']->getPayment();
        $payment->setTransactionAdditionalInfo('query-order', json_encode($response, JSON_UNESCAPED_UNICODE));
    }
}
