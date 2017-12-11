<?php
namespace luffyzhao\nears;

class Cluster
{
    /**
     * 聚合点
     * @var array
     */
    private $_data = [];

    /**
     * 获取聚合点
     * @method   getData
     * @DateTime 2017-12-07T14:38:25+0800
     * @return   [type]                   [description]
     */
    public function getData()
    {
        return $this->_data;
    }
    /**
     * 设置聚合点
     * @method   setData
     * @DateTime 2017-12-07T15:15:25+0800
     * @param    array                    $data [description]
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }
    /**
     * 添加点到聚合点
     * @method   addData
     * @DateTime 2017-12-07T14:28:45+0800
     * @param    [type]                   $data [description]
     */
    public function addData($data)
    {
        $this->_data[] = $data;
        return $this;
    }

    /**
     * 从聚合点中删除某点
     * @method   removeData
     * @DateTime 2017-12-07T14:29:03+0800
     * @param    [type]                   $data [description]
     * @return   [type]                         [description]
     */
    public function removeData($data)
    {
        if (($key = array_search($data, $this->_data)) !== false) {
            unset($this->_data[$key]);
        }
        $this->_data = array_values($this->_data);
        return $this;
    }

    /**
     * 格式化数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getData();
    }
}
