<?php

namespace Zzyy\WechatPay\Controller\Wechat;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use SimpleSoftwareIO\QrCode\GeneratorFactory;
use Zzyy\WechatPay\Helper\Data;

class Index extends Action implements HttpGetActionInterface
{
    private $qrcodeFactory;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        Context                                   $context,
        GeneratorFactory $qrcodeFactory,
        OrderRepositoryInterface                  $orderRepository,
    )
    {
        parent::__construct($context);

        $this->qrcodeFactory = $qrcodeFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function execute()
    {
        $orderId = $this->_request->getParam('order');
        $order = $this->orderRepository->get($orderId);
        $codeUrl = $order->getPayment()->getAdditionalInformation()['code_url'];
        $content = $this->qrcodeFactory->create()->size(200)->generate($codeUrl);
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setContents($content);
    }
}
