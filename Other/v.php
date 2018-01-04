<?php
/**
 * 校验输入参数
 */
class Validator{
    const STATUS_ERROR_PARAM_INVALID = 1;
    const STATUS_ERROR_SCHEMA_INVALID = 2;
    /**
     * @desc 检查输入是否有错, !!!schema格式错误只在错误信息中返回
     * @param $schema array 参数约定schema
     * @param $value mixed int|float|bool|string|array
     * @param $errMsg array
     * @return bool 
     */
    public static function validate($schema, $value, &$errMsg){
        if(!is_array($errMsg)) {
            $errMsg = []; 
        }

        if(!is_array($schema) || !($type = $schema['type'])) {
            $errMsg['status'] = self::STATUS_ERROR_SCHEMA_INVALID;
            $errMsg['msg'] = 'invalid schema';
            return true; 
        }

        $ret = true;
        $method = 'validate' . ucfirst(trim($type, "\t\n\r"));
        if (method_exists(Validator, $method)) {
            $ret = call_user_func_array('Validator::' . $method, [$schema, $value, &$errMsg]);
        }
        return $ret;
    }

   /**
    * @desc array校验
    * @param $schema  array 校验的schema
    * @param $value mixed 被校验的数据
    * @param $errMsg array 错误信息
    * @return bool
    */
    private static function validateArray($schema, $value, &$errMsg){
        if(!is_array($value)){
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not a array.';
            return false; 
        }
        // 针对特定元素的校验
        if($schema['special'] && is_array($schema['special'])) {
            foreach($schema['special'] as $k => $v) {
                if (isset($v['option']) && $v['option'] && !isset($value[$k])) {
                    continue;
                }
                
                if(!isset($value[$k])){
                    $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
                    $errMsg['msg'] = $k . ' not exits.';
                    return false;
                }

                $ret = self::validate($v, $value[$k], $errMsg);
                if(!$ret){
                    $errMsg['msg'] = $k . ' ' . $errMsg['msg'];
                    return $ret;
                }
            }
        }
        if($schema['common'] && is_array($schema['common'])) {
            foreach($value as $k => $v){
                $ret = self::validate($schema['common'], $v, $errMsg);
                if(!$ret){
                    $errMsg['msg'] = $k . ' ' . $errMsg['msg'];
                    return $ret;
                }
            }
        }
        return true;
    }

    /**
     * @desc string校验
     * @param $schema array 校验的schema
     * @param $value mixed 被校验的数据
     * @param $errMsg array 错误信息
     * @return bool
    */
    private static function validateString($schema, $value, &$errMsg){
        if(!is_string($value)) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not a string.';
            return false; 
        }
        if (isset($schema['pattern'])) {
            try{
                if(!preg_match($schema['pattern'], $value)){
                    $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
                    $errMsg['msg'] = 'not match ' . $schema['pattern'];
                    return false; 
                }
            }catch(Exception $e) {
                $errMsg['status'] = self::STATUS_ERROR_SCHEMA_INVALID;
                $errMsg['msg'] = 'invalid schema';
                return true;
            }
        }
        if (isset($schema['max_length']) && mb_strlen($value, 'UTF-8') > $schema['max_length']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'len needs < ' . $schema['max_length'];
            return false; 
        }
        if (isset($schema['min_length']) && mb_strlen($value, 'UTF-8') < $schema['min_length']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'len  needs > ' . $schema['min_length'];
            return false; 
        }
        if ($schema['enum'] && !is_array($schema['enum'])) {
            $errMsg['status'] = self::STATUS_ERROR_SCHEMA_INVALID;
            $errMsg['msg'] = 'invalid schema';
            return true;
        }
        if ($schema['enum'] && is_array($schema['enum']) && !in_array($value, $schema['enum'])) {
           $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
           $errMsg['msg'] = 'value must in' . json_encode($schema['enum']);
           return false;
        }
        return true;
    }
   
    /**
     * @int校验
     * @param $schema array  校验的schema
     * @param $value mixed 被校验的数据
     * @param $errMsg array  错误信息
     * @return bool
     */
    private static function validateInt($schema, $value, &$errMsg){
        if(!is_int($value)) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not a int.';
            return false; 
        }
        if (isset($schema['max']) && $value > $schema['max']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not < ' . $schema['max'];
            return false;
        }
        if (isset($schema['min']) && $value < $schema['min']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not > ' . $schema['min'];
            return false;
        }
        return true;
    }
   
    /**
     * @desc float校验
     * @param $schema array  校验的schema
     * @param $value mixed 被校验的数据
     * @param $errMsg array 错误信息
     * @return bool
     */
    private static function validateFloat($schema, $value, &$errMsg){
        if(!is_float($value)) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not a float.';
            return false; 
        }
        if (isset($schema['max']) && $value > $schema['max']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not < ' . $schema['max'];
            return false;
        }
        if (isset($schema['min']) && $value < $schema['min']) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not > ' . $schema['min'];
            return false;
        }
        return true;
    }
    /**
     * @bool校验
     * @param $schema array  校验的schema
     * @param $value mixed 被校验的数据
     * @param $errMsg array 错误信息
     * @return bool
     */
    private static function validateBool($schema, $value, &$errMsg){
        if(!is_bool($value)) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'not a bool';
            return false; 
        }
        return true;
    }

    /**
     * @desc 用户自定义函数校验
     * @param $schema  array 校验的schema
     * @param $value mixed 被校验的数据
     * @param $errMsg array 错误信息
     * @return bool
     */
    private static function validateFunc($schema, $value, &$errMsg){
        $func = $schema['func']; 
        if(!is_callable($func)){
            $errMsg['status'] = self::STATUS_ERROR_SCHEMA_INVALID;
            $errMsg['msg'] = 'function error.';
            return true; 
        }
        try{
            $ret = call_user_func_array($func, array($value));
        }catch(Exception $e) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'function error.';
            return false;
        }

        if(!$ret) {
            $errMsg['status'] = self::STATUS_ERROR_PARAM_INVALID;
            $errMsg['msg'] = 'value invalid';
            return false;
        }
        return $ret;
    }
}
