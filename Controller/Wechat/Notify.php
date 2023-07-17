<?php

namespace Zzyy\WechatPay\Controller\Wechat;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Operations\OrderOperation;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;
use Zzyy\WechatPay\Helper\Data;

class Notify extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{

    private Data $helper;

    private OrderRepositoryInterface $orderRepository;

    private ManagerInterface $transactionManager;

    private OrderOperation $orderOperation;

    public function __construct(
        Context $context,
        Data $helper, OrderRepositoryInterface $orderRepository,
        ManagerInterface $transactionManager,
        OrderOperation $orderOperation
    ){
        parent::__construct($context);
        $this->helper = $helper;
        $this->orderRepository = $orderRepository;
        $this->transactionManager = $transactionManager;
        $this->orderOperation = $orderOperation;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('content-type', 'text/plain');
        try {
            $id = $this->getRequest()->getParam('order');
            $order = $this->orderRepository->get($id);
            $payment = $order->getPayment();
            $this->helper->setCurrentPayment($payment);
            if ($info = $this->helper->getNotifyInfo()) {
                $transactionId = $info['transaction_id'];
                $exists = $this->transactionManager->isTransactionExists($transactionId, $payment->getEntityId(), $id);
                if (!$exists) {
                    $payment->setTransactionId($transactionId);
                    $payment->setTransactionAdditionalInfo('notify', json_encode($info));
                    $this->orderOperation->order($payment, $info['total_fee']);
                    // invoice
                    if ($payment->getMethodInstance()->getConfigData('auto_invoice')){
                        $invoice = $order->prepareInvoice()
                            ->setTransactionId($transactionId)
                            ->register()
                            ->pay();
                        $order->addRelatedObject($invoice);
                    }
                    $this->orderRepository->save($order);
                }
                $result->setContents($this->helper->getResponse('SUCCESS'));
            } else {
                $result->setContents($this->helper->getResponse('FAIL'));
            }
        } catch (\Exception $exception) {
            $result->setContents($this->helper->getResponse('FAIL'));
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
