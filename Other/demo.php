/** 
 * php version >= 5.4
 * @desc 校验参数
 * $schema 定义：
 *
 * int取值范围限定
 * $schema = {
 *  "type": "int",
 *  "max": 100,
 *  "min": 0,
 * }
 *
 * float取值范围限定
 * $schema = {
 *  "type": "float",
 *  "max": 100.0,
 *  "min": 0.0,
 * }
 *
 * !!!! 同一type限定条件可组合
 * string长度限定
 * $schema = {
 *  "type": "string",
 *  "max_length": 100,
 *  "min_length": 0,
 * }
 *
 * string pattern限定
 * $schema = {
 *  "type": string,
 *  "pattern": "/hello/",
 * }
 *
 * string 值枚举限定
 * $schema  = {
 *  "type": "string",
 *  "enum": ["hello", "word"],
 * }
 *
 * bool限定
 * $schema = {
 *  "type": "bool",
 * }
 *
 * array特定元素校验
 * $schema = {
 *  "type": "array",
 *  "special": [
 *      "key": {
 *          "option": true,
 *          !!!! 这里可以是预定义的任何schema
 *          "type": "int",
 *          "max": 100,
 *          "min": 0,
 *      }
 *  ]
 * }
 *
 * array全元素校验
 * $schema = {
 *  "type": "array",
 *  "common": [
 *      {
 *          !!!! 这里可以是预定义的任何schema
 *          "type": "int",
 *          "max": 100,
 *          "min": 0,
 *      }
 *  ]
 * }
 *
 *
 * fun自定义校验
 * $schema  = {
 *  "type": "func",
 *  "func": function () {}
 * }
 * */
