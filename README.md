# Magento2 微信支付网关集成

实现了微信支付native支付流程，有需要的朋友可以基于这个做二次开发。

![checkout](https://github.com/zzyycoder/mage2-wechatpay/blob/main/1.jpg?raw=true)
![order](https://github.com/zzyycoder/mage2-wechatpay/blob/main/2.jpg?raw=true)

参考文档：https://developer.adobe.com/commerce/php/development/payments-integrations/

依赖
- [wechatpay/wechatpay](https://github.com/wechatpay-apiv3/wechatpay-php)
- [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode)

注意

1. 依据官方v2开发文档：https://pay.weixin.qq.com/wiki/doc/api/index.html。依赖的wechatpay/wechatpay是微信官方提供的sdk，有需要可以方便地切换成v3版本的api。（参看[Readme](https://github.com/wechatpay-apiv3/wechatpay-php/blob/main/README_APIv2.md)）

2. 国内微信支付商户号只允许使用CNY下单，magento2的下单默认使用base_currency，因此需要设置base currency为CNY。
