<?php

class ValidatorScheme {

    private $type;
    private $data;
    private $scheme;
    private static $typeList = [
        "typeCheck",
        "rangeCheck",
        "inList",
        "strLength",
        "regex",
        "arraySpecialKeyCheck",
        "arrayOptionalKeyCheck",
        "userFuncCheck",
        "iteratorCheckScheme",
    ];

    /**
     * ValidatorScheme constructor.
     * @param $type string 类型
     * @param $data
     * @param null $scheme object 附加校验scheme
     */
    public function __construct($type,$data,$scheme=null) {
        $this->type  = $type;
        $this->data = $data;
        $this->scheme = $scheme;
    }

    /**
     * 类型检查,判断输入的类型
     * @param $checkItem
     * @return bool|mixed
     */
    public function typeCheck($checkItem,&$err_msg) {

        $typeList = ["bool","int","float","string","object","array"];
        if ( in_array( $this->data, $typeList, true ) ) {

            $func = "is_{$this->data}";
            $check_result = false;
            try {
                $check_result = call_user_func($func,$checkItem);
            } catch ( Exception $e ) {
                echo "Get Exception Msg:{$e->getMessage()}\n";
            }

            if (!$check_result) {
                $err_msg = "参数类型错误,check it";
            }

            return $check_result;
        } else {
            $err_msg = "输入的参数类型错误,check it \n";
            return false;
        }
    }

    private function inList($checkItem) {

        if (!is_array($this->data)) {
            $this->data = array( $this->data );
        }

        return in_array( $checkItem, $this->data );
    }

    /**
     * 校验数字大小是否在指定区间内
     * @param $checkItem
     * @return bool
     */
    private function rangeCheck($checkItem,&$err_msg) {

        if (!is_numeric($checkItem)) {
            $err_msg = "被校验的value不是一个有效的数字类型.";
            return false;
        }

        if ( !is_array($this->data) ) {
            $err_msg = "没有输入有效的范围数组.";
            return false;
        }

        if ( !isset($this->data["min"]) && !isset($this->data["max"]) ) {
            $err_msg = "没有输入有效的范围数组.";
            return false;
        } else if ( !isset($this->data["min"]) && isset($this->data["max"]) ) {
            $check_result = is_numeric($this->data["max"]) && $this->data["max"] > $checkItem;
        } else if ( isset($this->data["min"]) && !$this->data["max"] ) {
            $check_result = is_numeric($this->data["min"]) && $this->data["min"] < $checkItem;
        } else {
            $check_result = is_numeric($this->data["max"])
                && is_numeric($this->data["min"])
                && ($this->data["max"] > $checkItem)
                && ($this->data["min"] < $checkItem);
        }

        if (!$check_result) {
            $err_msg = "不在指定范围内";
        }
        return $check_result;
    }

    /**
     * 校验字符串长度是否在指定区间内
     * @param $checkItem 需要进行校验的项目
     * @return bool
     */
    private function strLength($checkItem,&$err_msg) {

        $param = $this->data;

        //校验的主体不是一个字符串
        if (!is_string($checkItem)) {
            $err_msg = "被检查的值不是一个有效的字符串";
            return false; 
        }
        //校验参数未设置，或者不是一个有效的数组格式
        if (!isset($param) && !is_array($param)) {
            $err_msg = "没有输入有效的检测条件";
            return false;
        }

        $str_length = strlen($checkItem);
        if (!isset($paramp["min"]) && !isset($param["max"])) {
            $err_msg = "没有输入有效的检测条件";
            return false; //没设置最小长度，也没设置最大长度
        } else if (!isset($param["min"]) && isset($param["max"])) { //仅设置了最大长度
            $check_result = is_numeric($param["max"]) && ($param["max"] > $str_length);
        } else if (isset($param["min"]) && !isset($param["max"])) { //仅设置了最小长度
            $check_result = is_numeric($param["min"]) && ($param["min"] < $str_length);
        } else { //设置了最大长度也设置了最小长度
            $check_result = is_numeric($param["max"])
            && ($param["max"]>$str_length)
            && is_numeric($param["min"])
            && ($param["min"] < $str_length);
        }

        if (!$check_result) {
            $err_msg = "字符串\"{$checkItem}\"长度不满足输入情况";
        }
        return $check_result;
    }


    /**
     * 检查字符串是否满足某个正则表达式
     * @param $checkItem
     * @param $err_msg
     * @return bool
     */
    private function regex($checkItem,&$err_msg){

        $preg = "/^\/.*\/$/";
        //正则表达式错误
        if (!is_string($this->data) || !preg_match($preg, $this->data)) {
            $err_msg = "正则表达式输入错误";
            return false;
        }

        if (!is_string($checkItem)) {
            $err_msg = "输入不是一个合法的字符串";
            return false;
        }


        if (!preg_match($this->data,$checkItem)) {
            $err_msg = "输入的字符串 \"{$checkItem}\" 不符合指定的正则表达式. \"{$this->data}\" }";
            return false;

        }
        return true;

    }


    /**
     * 遍历数组的每一个元素判断是否满足某几个校验条件
     * @param $checkItem array 需要检查的数组
     * @param $err_msg string 错误提示
     * @return bool
     */
    private function iteratorCheckScheme($checkItem,&$err_msg) {

        /**
            data = [
               "schemes" => [scm1,scm2],
            ];
         */

        if (!is_array($checkItem)||empty($checkItem)) {
            $err_msg = "需要检查的值不是一个有效的非空数组";
            return false;
        }

        if (!is_array($this->data)) {
            $err_msg = "scheme参数错误";
            return false;
        }


        $result = true;
        foreach ($checkItem as $value) {

            $vResult = true; //记录

            foreach ( $this->data as $scheme ) {

                if ( !isset($scheme) || !$scheme instanceof  ValidatorScheme ) {
                    return false;
                }

                if ( !$scheme->checkScheme($value,$err_msg) ) {
                    $vResult = false;
                    break;
                }

            }

            //如果某个值
            if (!$vResult) {
                $result = false; //有一项不通过,就算未通过.
                $err_msg = "{$value} not Pass err_msg:[{$err_msg}]\n";
                break;
            }

        }

        return $result;

    }


    /**
     * 校验数组中指定key是否满足若干验证规则.
     * @param $checkItem
     * @param $err_msg
     * @return bool
     */
    private function arraySpecialKeyCheck($checkItem,&$err_msg) {


        /*$demoData = [
            "schemes" => [$scheme1,$scheme2],
            "key" => "someKey"
        ];*/

        if (!is_array($checkItem)||empty($checkItem)) {
            $err_msg = "需要检查的值不是一个有效的非空数组";
            return false;
        }


        if (!is_array($this->data) || !isset($this->data["schemes"]) || !isset($this->data["key"])) {
            $err_msg = "scheme参数错误";
            return false;
        }

        $arr_scheme = $this->data["schemes"];
        $key = $this->data["key"];

        //判断指定的元素是否存在
        if (!isset($checkItem[$key])){
            $err_msg = "数组中指定的key:[{$key}}]不存在";
            return false;
        }


        $result = true;
        $to_check_v = $checkItem[$key];

        foreach ( $arr_scheme as $scheme ) {

            if ( !isset($scheme) || !$scheme instanceof  ValidatorScheme ) {
                return false;
            }

            if ( !$scheme->checkScheme($to_check_v,$err_msg) ) {
                $result = false;
                break;
            }

        }

        if (!$result) {
            $err_msg = "the Key:[{$key}}], value:[{$to_check_v}] not Pass err_msg:[{$err_msg}]\n";
        }

        return $result;
    }


    /**
     * 校验数组中指定key 如果存在,则判断是否满足若干验证规则. 如果不存在, 则不进行校验
     * @param $checkItem
     * @param $err_msg
     * @return bool
     */
    private function arrayOptionalKeyCheck($checkItem,&$err_msg) {


        /*$demoData = [
            "schemes" => [$scheme1,$scheme2],
            "key" => "someKey"
        ];*/

        if ( !is_array($checkItem) || empty($checkItem) ) {
            $err_msg = "需要检查的值不是一个有效的非空数组";
            return false;
        }


        if ( !is_array($this->data) || !isset($this->data["schemes"]) || !isset($this->data["key"]) ) {
            $err_msg = "scheme参数错误";
            return false;
        }

        $arr_scheme = $this->data["schemes"];
        $key = $this->data["key"];

        //判断指定的元素是否存在
        if (!isset($checkItem[$key])){
            return true;
        }


        $result = true;
        $to_check_v = $checkItem[$key];

        foreach ( $arr_scheme as $scheme ) {

            if ( !isset($scheme) || !$scheme instanceof  ValidatorScheme ) {
                return false;
            }

            if ( !$scheme->checkScheme($to_check_v,$err_msg) ) {
                $result = false;
                break;
            }

        }

        if (!$result) {
            $err_msg = "the Key:[{$key}}], value:[{$to_check_v}] not Pass err_msg:[{$err_msg}]\n";
        }

        return $result;
    }



    private function userFuncCheck($checkItem,&$err_msg) {

        /**
         * 输入demo 调用对应ClassName::MethodName();
         * $this->data = ["ClassName","Method'Name"];
         */
        if ( !is_array($this->data) ) {
            $err_msg = "用户自定义函数信息输入有误";
            return false;
        }

        $user_func = $this->data;
        $class_name = isset($user_func[0]) ? $user_func[0] : "";
        $method_name = isset($user_func[1]) ? $user_func[1] : "";

        if ( $class_name === "" || !is_string($class_name) || $method_name === "" || !is_string($method_name) ) {
            $err_msg = "用户自定义函数信息输入有误";
            return false;
        }


        if ( !class_exists($class_name) ) {
            $err_msg = "指定的类{$class_name}不存在";
            return false;
        }

        if ( !method_exists($class_name,$method_name) ) {
            $err_msg = "指定类{$class_name}的函数{$method_name}不存在";
            return false;
        }

        try {
            $func_result = call_user_func_array($user_func, [$checkItem,]);
            if ( !is_bool($func_result) ) {
                $result = false;
            } else {
                $result = $func_result;
            }

        } catch ( Exception $e ) {
            $err_msg = "Catch Exception:{$e->getMessage()}";
            return false;
        }
        return $result;

    }





    /**
     * 根据scheme规则检查
     * @param $checkItem 待检查的内容
     * @param $err_msg string 错误信息
     * @return bool
     */
    public function checkScheme($checkItem,&$err_msg) {
        if (
            isset($this->type)
            && isset($this->data)
            && is_string($this->type)
            && in_array($this->type, self::$typeList, true) 
        ) {

            if ( $this->scheme instanceof ValidatorScheme ) {

                // 需要递归的进行校验参数
                // 1.需要校验的不是数组 
                // 2.需要校验的是一个数组
                $extra_scheme_check_result = $this->scheme->checkScheme($checkItem,$err_msg);
                if (!$extra_scheme_check_result ) {

                    return false;
                }
            }

            $func = $this->type;
            //jreturn call_user_func_array([$this,$func], [$checkItem,$err_msg] ); // 参数校验
            return $this->$func($checkItem,$err_msg);
            /*
            $typeValue = self::$valueList[$this->type];


            if ( is_array($typeValue) ){
                return in_array($this->data,self::$valueList[$this->type],true);
            }**/
        } else {
            $err_msg = "Scheme 输入错误.";
            return false;
        }

    } 
}





