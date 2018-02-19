<?php

namespace Modules\Seller\Constants;


class SellerErrorConstant
{
    const ERR_STORE_CREATE_FAILED = '22101|店铺添加失败';
    const ERR_STORE_DISALLOW_CREATE = '22102|不允许创建店铺';
    const ERR_STORE_INVALID = '22103|无效的店铺';
    const ERR_STORE_DISALLOW_UPDATE = '22104|只能修改待审核店铺';
    const ERR_STORE_DISALLOW_DELETE = '22105|无效的店铺';
    const ERR_STORE_UPDATE_FAILED = '22106|店铺修改失败';
    const ERR_STORE_DELETE_FAILED = '22107|店铺删除失败';
    const ERR_STORE_VERIFY_FAILED = '22108|店铺审核失败';
    const ERR_STORE_VERIFIED = '22109|店铺已经审核过了';
    const ERR_STORE_NO_DEPLOY = '22110|未绑定店铺';
    const ERR_STORE_OPERATE_ILLEGAL = '22111|非法操作';


    const ERR_GOODS_CREATE_FAILED = '22201|商品添加失败';
    const ERR_GOODS_UPDATE_FAILED = '22202|商品修改失败';
    const ERR_GOODS_DELETE_FAILED = '22203|商品删除失败';
    const ERR_GOODS_INVALID = '22204|无效的商品';
    const ERR_GOODS_NO_DEPLOY = '22205|未添加商品';
}