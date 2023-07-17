<?php

namespace Zzyy\WechatPay\Helper;

use WeChatPay\Builder;
use WeChatPay\Transformer;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Validator\ValidatorCompositeFactory;
use Magento\Payment\Model\InfoInterface;
use Zzyy\WechatPay\Gateway\Validator\BaseValidator;
use Zzyy\WechatPay\Gateway\Validator\SignatureValidator;
use Zzyy\WechatPay\Gateway\Validator\TotalFeeValidator;

class Data extends AbstractHelper
{
    private ValidatorCompositeFactory $validatorFactory;
    private PaymentDataObjectFactory $paymentDataObjectFactory;

    public function __construct(
        Context $context,
        ValidatorCompositeFactory $validatorFactory,
        PaymentDataObjectFactory $paymentDataObjectFactory
    ){
        parent::__construct($context);
        $this->validatorFactory = $validatorFactory;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
    }

    const PAYMENT_CODE_WECHAT = 'wechat';

    private InfoInterface $payment;

    /**
     * @param InfoInterface $payment
     * @return void
     */
    public function setCurrentPayment(InfoInterface $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \WeChatPay\BuilderChainable
     * @throws LocalizedException
     */
    public function getWechatSdk()
    {
        $method = $this->payment->getMethodInstance();
        return Builder::factory([
            'mchid' => $method->getConfigData('mchid'),
            'serial' => 'nop',
            'privateKey' => 'any',
            'certs' => ['any' => null],
            'secret' => $method->getConfigData('secret')
        ]);
    }

    /**
     * @param $orderId
     * @return string
     */
    public function getNotifyUrl($orderId)
    {
        return $this->_getUrl('wechat/wechat/notify', ['order' => $orderId]);
    }

    /**
     * @return array|false
     * @throws LocalizedException
     */
    public function getNotifyInfo()
    {
        $content = $this->_request->getContent();
        $content = Transformer::toArray($content);
        $validator = $this->validatorFactory->create([
            'validators' => [
                'sign' => SignatureValidator::class,
                'base' => BaseValidator::class,
                'total-fee' => TotalFeeValidator::class,
            ],
            'chainBreakingValidators' => ['sign', 'base']
        ]);
        $result = $validator->validate([
            'response' => $content,
            'payment' => $this->paymentDataObjectFactory->create($this->payment)
        ]);
        if (!$result->isValid()) {
            foreach ($result->getFailsDescription() as $errorMessage) {
                $this->_logger->critical('Payment Error: ' . $errorMessage);
            }
            return false;
        }

        return $content;
    }

    /**
     * @param $code
     * @param $msg
     * @return string
     */
    public function getResponse($code, $msg = '')
    {
        return Transformer::toXml([
            'return_code' => $code,
            'return_msg' => $msg,
        ]);
    }
}
