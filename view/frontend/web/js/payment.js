/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'ko',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/payment/renderer-list',
    'Magento_Checkout/js/action/redirect-on-success',
    'mage/translate'
], function (ko, confirm, Component, rendererList, redirectOnSuccessAction, $t) {
    'use strict';

    var src;

    rendererList.push(
        {
            type: 'wechat',
            component: 'Zzyy_WechatPay/js/payment'
        }
    );

    return Component.extend({
        defaults: {
            template: 'Zzyy_WechatPay/payment/payment'
        },
        redirectAfterPlaceOrder: false,

        onConfirm: function () {
            redirectOnSuccessAction.execute();
        },

        afterPlaceOrder: function () {
            confirm({
                title: $t('Scan Qrcode With Wechat App To Pay'),
                content: '<img style="display: block; margin: 0 auto;" src='+ src +' alt="">',
                buttons: [{
                    text: $t('OK'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }],
                actions: {
                    always: this.onConfirm
                }
            });
        },

        getPlaceOrderDeferredObject: function () {
            return this._super().done(function (response) {
                src = '/wechat/wechat/index/order/' + response;
            });
        },
    });
});
