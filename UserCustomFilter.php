<?php
/**
 * 用户自定义的过滤器
 * User: jingqiyu
 * Date: 2018/1/2
 * Time: 下午4:56
 */

class UserCustomFilter
{

    public static function filter($value) {
        return is_int($value) && ($value % 2 === 0);
    }

}