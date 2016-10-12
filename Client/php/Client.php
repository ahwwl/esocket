<?php

/**
 * Created by IntelliJ IDEA.
 * User: ahwwl
 * Date: 16-10-10
 * Time: 下午4:05
 * @property resource socket
 */
class Client
{
    function __construct()
    {
        $this->socket = null;
    }

    /**
     * 连接服务器
     */
    function connect($host,$port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($this->socket, $host, $port);
    }
    /**
     * 读取服务器返回
     * @return array
     */
    function readData()
    {
        $readBytes = new readBytes();
        while (True)
        {
            $out = socket_read($this->socket, 1024);
            $len = $readBytes->shortHeader($out);
            if($len == 0) break;
        }
        $values = $readBytes->short("cmd")->short("result")->string("value")->values;
        return $values;
    }

    /**
     * 发送数据
     * @param $data
     */
    function writeData($data)
    {
        $writeBytes = new writeBytes();
        if(!empty($data["cmd"]))
            $writeBytes->short($data["cmd"]);
        if(!empty($data["key"]))
            $writeBytes->string($data["key"]);
        if(!empty($data["value"]))
            $writeBytes->string($data["value"]);
        if(isset($data["expiry"]))
            $writeBytes->int(intval($data["expiry"]));
        $bytes      = $writeBytes->getValue();

        socket_write($this->socket, $bytes, strlen($bytes));
    }

    /**
     * 设置key和value的值
     * @param $key
     * @param $val
     * @param int $expiry 过期时间秒
     * @return bool 成功返回：TRUE;失败返回：FALSE
     */
    function set($key,$val,$expiry =0)
    {
        $expiry = intval($expiry);
        $data   = [
            "cmd"       => 1002,
            "key"       => $key,
            "value"     => $val,
            "expiry"    => $expiry
        ];
        $this->writeData($data);
        $values = $this->readData();
        if($values["result"] == 10000)
        {
            return true;
        }
        return false;
    }

    /**
     *  获取有关指定键的值
     * @param $key
     * @return bool|mixed *string或BOOL 如果键不存在，则返回 FALSE。否则，返回指定键对应的value值
     */
    function get($key)
    {
        $data = [
            "cmd" => 1004,
            "key" => $key,
        ];
        $this->writeData($data);
        $values = $this->readData();
        if($values["result"] == 10000)
        {
            return $values["value"];
        }
        return false;
    }

    /**
     * 删除指定的键
     * @param $key
     * @return bool
     */
    function delete($key)
    {
        $data = [
            "cmd" => 1006,
            "key" => $key,
        ];
        $this->writeData($data);
        $values = $this->readData();
        if($values["result"] == 10000)
        {
            return true;
        }
        return false;
    }

    /**
     * 如果在数据库中不存在该键，设置关键值参数
     * @param $key
     * @param $val
     * @param int $expiry
     * @return bool
     */
    function setnx($key,$val,$expiry =0)
    {
        $expiry = intval($expiry);
        $data   = [
            "cmd"       => 1008,
            "key"       => $key,
            "value"     => $val,
            "expiry"    => $expiry
        ];
        $this->writeData($data);
        $values = $this->readData();
        if($values["result"] == 10000)
        {
            return true;
        }
        return false;
    }

    /**
     * 验证指定的键是否存在
     * @param $key
     * @return bool
     */
    function exists($key)
    {
        $data = [
            "cmd" => 1010,
            "key" => $key,
        ];
        $this->writeData($data);
        $values = $this->readData();
        if($values["result"] == 10000)
        {
            return true;
        }
        return false;
    }
    function __destruct()
    {
        if($this->socket)
        {
            socket_close($this->socket);
        }

        // TODO: Implement __destruct() method.
    }
}