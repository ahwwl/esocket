<?php

/**
 * Created by IntelliJ IDEA.
 * User: ahwwl
 * Date: 16-10-10
 * Time: 下午4:03
 */
class readBytes
{
    public $bytes   = "";
    public $values  = [];

    /**
     * 读取头数据，返回剩余数据长度
     * @param $v
     * @return int
     */
    function shortHeader($v)
    {
        $bytes          = unpack("n",substr($v,0,2));
        $this->bytes    = substr($v,2);
        $len = $bytes[1];
        if($len > strlen($this->bytes))
        {
            return $len - strlen($this->bytes);
        }
        return 0;
    }

    /**
     * 判断并返回数据
     * @param $v
     * @return string
     */
    function p_value($v)
    {
        if(!empty($v))
        {
            return $v[1];
        }
        return "";
    }

    /**
     * 读取short类型
     * @param $v
     * @return $this
     */
    function short($v)
    {
        if($this->bytes)
        {
            $data               = substr($this->bytes,0,2);
            $this->values[$v]   = $this->p_value(unpack("n",$data));
            $this->bytes        = substr($this->bytes,2);
        }

        return $this;
    }
    /**
     * 读取int类型
     * @param $v
     * @return $this
     */
    function int($v)
    {
        if($this->bytes) {
            $data               = substr($this->bytes,0,4);
            $this->values[$v]   = (int)$this->p_value(unpack("N", $data));
            $this->bytes        = substr($this->bytes, 4);
        }
        return $this;
    }
    /**
     * 读取byte类型
     * @param $v
     * @return $this
     */
    function byte($v)
    {
        if($this->bytes) {
            $data               = substr($this->bytes,0,1);
            $this->values[$v]   = (int)$this->p_value(unpack("B", $data));
            $this->bytes        = substr($this->bytes, 1);
        }
        return $this;
    }
    /**
     * 读取string类型
     * @param $v
     * @return $this
     */
    function string($v)
    {
        if($this->bytes) {
            $data               = substr($this->bytes,0,2);
            $len                = (int)$this->p_value(unpack("n", $data));
            $this->values[$v]   = substr($this->bytes, 2, intval($len));
            $this->bytes        = substr($this->bytes, intval($len) + 2);
        }
        return $this;
    }
    /**
     * 读取列表string类型
     * @param $v
     * @return $this
     */
    function lists($v)
    {
        $ls             = [];
        $data           = substr($this->bytes,0,2);
        $len            = (int) $this->p_value(unpack("n",$data));
        $this->bytes    = substr($this->bytes,2);
        for($i  = 0; $i < $len; $i++)
        {
            $data           = substr($this->bytes,0,2);
            $len1           = (int) $this->p_value(unpack("n",$data));
            $ls[]           = substr($this->bytes,2,intval($len1));
            $this->bytes    = substr($this->bytes,intval($len1)+2);
        }
        $this->values[$v] = $ls;
        return $this;
    }
}