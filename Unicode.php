<?php
namespace App\Services\Util\Unicode;

/**
 * unicode编码处理
 */
class Unicode
{
    /**
     * utf8字符转换成Unicode字符
     * @param  [type] $utf8_str Utf-8字符
     * @return mixed
     */
    function utf8_str_to_unicode($utf8_str) {
        $unicode = 0;
        $unicode = (ord($utf8_str[0]) & 0x1F) << 12;
        $unicode |= (ord($utf8_str[1]) & 0x3F) << 6;
        $unicode |= (ord($utf8_str[2]) & 0x3F);
        return dechex($unicode);
    }

    /**
     * utf8字符转换成Unicode字符(批量)
     * @param $text mixed 文本
     * @return mixed
     */
    public function utf8_str_to_unicode_bat($text){
        $res = [];
        foreach (mb_str_split($text) as $key=>$value){
            $res[] = $this->utf8_str_to_unicode($value);
        }
        return $res;
    }

    /**
     * Unicode字符转换成utf8字符
     * @param  [type] $unicode_str Unicode字符
     * @return mixed
     */
    function unicode_to_utf8($unicode_str) {
        $utf8_str = '';
        $code = intval(hexdec($unicode_str));
        //这里注意转换出来的code一定得是整形，这样才会正确的按位操作
        $ord_1 = decbin(0xe0 | ($code >> 12));
        $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
        $ord_3 = decbin(0x80 | ($code & 0x3f));
        $utf8_str = chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
        return $utf8_str;
    }

    /**
     * 将文本中的utf8中文转为unicode编码
     * @param $text mixed 文本
     * @return mixed
     */
    public function text_to_unicode($text){
        preg_match_all('/[\x{4e00}-\x{9fa5}]+/u',$text,$matches);
        $str = $text;
        foreach($matches[0] as $aKey => $aVal) {
            $newVal = $this->utf8_str_to_unicode_bat($aVal);
            $nStr = '';
            foreach ($newVal as $key1=>$value){
                $nStr .= '\x{'.$value.'}';
            }
            $pattern = "/$nStr/u";
            $str = preg_replace($pattern,$nStr, $str);
        }
        return $str.'u';
    }
}
