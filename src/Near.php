<?php
namespace luffyzhao\nears;

class Near
{
    // 原始参照点
    private $_originalReference = [113.927889, 22.518047];
    // 参照坐标点
    private $_reference = [0, 0];
    // 坐标点
    private $_data = [];
    // 分隔长度
    private $_clusterCount = 2;
    // 集合
    private $_clusters = [];
    // 经度key
    private $xKey = 0;
    // 纬度key
    private $yKey = 1;

    public function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function setOriginalReference(array $reference)
    {
        $this->_originalReference = $reference;
        return $this;
    }

    public function setClusterCount(int $count)
    {
        $this->_clusterCount = $count;
        return $this;
    }

    public function setXKey($key)
    {
        $this->xKey = $key;
        return $this;
    }

    public function setYKey($key)
    {
        $this->yKey = $key;
        return $this;
    }

    public function solve()
    {
        $this->_initialiseClusters();
        while ($this->_iterate()) {}

        return $this;
    }

    private function _initialiseClusters()
    {
        for ($i = 0; $i <= $this->_clusterCount; $i++) {
            $this->_clusters[] = new Cluster();
        }

        if ($this->_clusterCount) {
            $this->_clusters[0]->setData($this->_data);
        }
    }

    private function _iterate()
    {
        $ceil = (int) (count($this->_data) / $this->_clusterCount);

        foreach ($this->_clusters as $index => $cluster) {
            if ($index === 0) {
                continue;
            }

            for ($key = 0; $key < $ceil; $key++) {
                $data = $this->_clusters[0]->getData();
                if (empty($data)) {
                    break;
                }

                if ($key === 0) {
                    $this->_reference = $this->_originalReference;
                }
                $nearData = $this->_near($this->_reference, $data);
                $this->_clusters[$index]->addData($nearData);
                $this->_clusters[0]->removeData($nearData);
                $this->_reference = $nearData;
            }
        }
        return false;
    }

    private function _near($reference, $data)
    {
        $min = 6378.137;
        $item = [0, 0];
        foreach ($data as $key => $value) {
            if (($distance = $this->_distance($reference, $value)) < $min) {
                $min = $distance;
                $item = $value;
            }
        }
        return $item;
    }

    private function _distance($loc, $loc2)
    {
        $loc = array_map([$this, '_pi'], [
            $this->xKey => $loc[$this->xKey],
            $this->yKey => $loc[$this->yKey],
        ]);
        $loc2 = array_map([$this, '_pi'], [
            $this->xKey => $loc[$this->xKey],
            $this->yKey => $loc[$this->yKey],
        ]);

        $calcLongitude = $loc[$this->yKey] - $loc2[$this->yKey];
        $calcLatitude = $loc[$this->xKey] - $loc2[$this->xKey];

        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($loc[$this->yKey]) * cos($loc2[$this->yKey]) * pow(sin($calcLongitude / 2), 2);

        return round(2 * asin(min(1, sqrt($stepOne))) * 6378.137, 2);
    }

    private function _pi($value)
    {
        return ($value * 3.1415926) / 180;
    }

    /**
     * 格式化数组
     *
     * @return array
     */
    public function toArray()
    {
        $clusters = [];
        foreach ($this->_clusters as $key => $value) {
            $clusters[$key]['data'] = $value->toArray();
        }
        return $clusters;
    }
}
