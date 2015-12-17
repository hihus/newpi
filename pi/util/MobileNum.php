<?php

/**
 * Copyright (c)  All Rights Reserved
 * @date 2015-03-04
 * @version 1.0 
 * */
final class MobileNum {

    /**
     * 判断是否是合法的手机号(单个)
     * 目前只支持中国大陆手机号验证
     * @param string $mobile_number 手机号码
     * @param string $nation 国别，目前仅支持中国大陆
     * @return boolean
     */
    public function isValidMobileNum($mobile_number, $nation = 'China/Mainland') {
        // 电话号码判断，包括虚拟运营商号段
        /**
         * 移动号码段: 134、135、136、137、138、139、147、150、151、152、157、158、159、178、182、183、184、187、188
         * 联通号码段: 130、131、132、145、155、156、176、185、186
         * 电信号码段: 133、153、177、180、181、189
         * 虚拟运营商: 170、176、178
         */
        if ($nation == 'China/Mainland') {
            $reg = "/^13[0-9]{9}$|147[0-9]{8}|15[012356789]{1}[0-9]{8}$|17[0678]{1}[0-9]{8}$|18[0-9]{9}$/";
        } else {
            $reg = "*";
        }
        if (preg_match($reg, $mobile_number)) {
            return TRUE;
        }
        return FALSE;
    }

}
