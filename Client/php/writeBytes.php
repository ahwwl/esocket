<?php
/**
 * Created by IntelliJ IDEA.
 * User: ahwwl
 * Date: 16-10-10
 * Time: 下午4:02
 */
class writeBytes
{
    public $bytes ="";

    /**
     * 设置short类型的二进制字符串
     * @param $v
     * @return $this
     */
    function short($v)
    {
        $this->bytes    .= pack("n",$v);
        return $this;
    }

    /**
     * 设置int类型的二进制字符串
     * @param $v
     * @return $this
     */
    function int($v)
    {
        $this->bytes    .= pack("N",$v);
        return $this;
    }

    /**
     * 设置byte类型的二进制字符串
     * @param $v
     * @return $this
     */
    function byte($v)
    {
        $this->bytes    .= pack("B",$v);
        return $this;
    }

    /**
     * 设置string类型的二进制字符串
     * @param $v
     * @return $this
     */
    function string($v)
    {
        $this->short(strlen($v));
        $this->bytes    .= pack("a".strlen($v),$v);
        return $this;
    }

    /**
     * 设置string类型的二进制字符串
     * @param $v
     * @return $this
     */
    function lists($vals)
    {
        $this->short(count($vals));
        foreach ($vals as $val)
        {
            $this->string($val);
        }
        return $this;
    }

    /**
     * 获取二进制字符串
     * @return string
     */
    function getValue()
    {
        return $this->bytes;
    }
}