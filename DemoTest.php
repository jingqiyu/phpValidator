<?php
include "Validator.php";
include "UserCustomFilter.php";


echo "****** Test1. 测试一个变量的类型是否和输入的类型相同 ******\n";
$v = 123;
$err_msg = "";
$scheme = new ValidatorScheme("typeCheck","int");
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);

echo "-----------------------\n";
echo "****** Test2. 测试一个数字是否在指定的范围内如1224是否在(0,200)开区间内 ******\n";
$v = 124;
$err_msg = "";
$scheme = new ValidatorScheme("rangeCheck",[
    "min" => 0,
    "max" => 200,
],null ); //入口验证条件是int类型
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);


echo "-----------------------\n";
echo "****** Test3. 检查一个字符串是否满足指定的正则表达式 ******\n";
$v = "hello,world";
$err_msg = "";
$patten = "/^hello/";
$scheme = new ValidatorScheme("regex", $patten,null ); //入口验证条件是int类型
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);

echo "-----------------------\n";
echo "****** Test4. 检查一个字符串的长度是否在指定范围内,如开区间(2,10)内 ******\n";
$err_msg = "";
$v = "123456789";
$scheme = new ValidatorScheme("strLength",[
    "min" => 2,
    "max" => 10,
],null);
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);

echo "-----------------------\n";
echo "****** Test5. 检查一个值是否在指定的列表中 ******\n";
$err_msg = "";
$v = "str";
$scheme = new ValidatorScheme("inList",["str","str1"] ); //入口验证条件是int类型
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);

echo "-----------------------\n";
echo "****** Test6. 遍历数组检查每一个元素是否满足特定的N个规则 ******\n";
$err_msg = "";
$v = ["string","hello","test","bcd"];
$scheme1 = new ValidatorScheme("typeCheck","string");
$scheme2 = new ValidatorScheme("strLength",[
    "min" => 2,
    "max" => 10,
],null);
$arr_scheme = [$scheme1,$scheme2];
$scheme_arr_iterator = new ValidatorScheme("iteratorCheckScheme",$arr_scheme);
$result = Validator::validate($scheme_arr_iterator,$v,$err_msg);
var_dump($result);
var_dump($err_msg);

echo "-----------------------\n";
echo "****** Test7. 检查一个数组中指定的值是否满足特定的那个规则 ******\n";
$err_msg = "";
$v = [
    "hlj" => "big",
    "bj"  => "big",
    "js"  => "small",
];
$scheme1 = new ValidatorScheme("typeCheck","string");
$scheme2 = new ValidatorScheme("strLength",[
    "min" => 2,
    "max" => 10,
],null);
$arr_scheme = [$scheme1,$scheme2];
$data = [
    "schemes" => $arr_scheme,
    "key"     => "hlj",
];
$scheme_arr_special = new ValidatorScheme("arraySpecialKeyCheck",$data);
$result = Validator::validate($scheme_arr_special,$v,$err_msg);
var_dump($result);
var_dump($err_msg);


echo "-----------------------\n";
echo "****** Test8. 检查一个数组中指定的key,如果存在则返回指定key的值是否满足特定的N个规则,否则直接通过 ******\n";
$err_msg = "";
$v = [
    "hlj" => "big",
    "bj"  => "big",
    "js"  => "small",
];
$scheme1 = new ValidatorScheme("typeCheck","int");
$arr_scheme = [$scheme1,];
$data = [
    "schemes" => $arr_scheme,
    "key"     => "notExistKey",
];
$scheme_arr_special = new ValidatorScheme("arrayOptionalKeyCheck",$data);
$result = Validator::validate($scheme_arr_special,$v,$err_msg);
var_dump($result);
var_dump($err_msg);


echo "-----------------------\n";
echo "****** Test9. 使用用户自定义的函数进行校验 ******\n";
$err_msg = "";
$v = 123;


$objUserCustomFilter = new UserCustomFilter();

$scheme = new ValidatorScheme("userFuncCheck",["UserCustomFilter","filter",]);
$result = Validator::validate($scheme,$v,$err_msg);
var_dump($result);
var_dump($err_msg);
