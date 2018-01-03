<?php
include "ValidatorScheme.php";


class Validator {

    public static function validate($scheme,$value,&$err_msg) {

        if ( !$scheme instanceof ValidatorScheme ) {
            $err_msg = "scheme类型错误.";
            return false;
        }

        return $scheme->checkScheme($value,$err_msg);

    }
}

