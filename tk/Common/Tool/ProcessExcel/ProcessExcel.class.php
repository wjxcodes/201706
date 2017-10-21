<?php

/**
 * @author demo
 * @date 2015年12月18日
 */
/**
 * Class ProcessExcel
 * 调用示例：
 * import('@.Tool.ProcessExcel.ProcessExcel');
 * $file = 'D:/test.csv';
 * $data = (new ProcessExcel($file))->read();
 * print_r($data[1][1]);
 * spout url:https://github.com/box/spout
 */
class ProcessExcel
{
    private $reader;//读handle
    private $writer;//写handle
    private $type;//Spout中定义的文档格式
    private $file;//文件路径文件名 如/www/file/1.xlsx

    public function __construct($file) {
        //引入命名空间自动加载
        if (!defined('SPOUT_ROOT')) {
            define('SPOUT_ROOT', dirname(__FILE__) . '/');
            require_cache(SPOUT_ROOT . 'Spout/Autoloader/autoload.php');
        }
        $this->file = $file;
        //类型判断
        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'xlsx':
                $this->type = \Box\Spout\Common\Type::XLSX;
                break;
            case 'ods':
                $this->type = \Box\Spout\Common\Type::ODS;
                break;
            case 'csv':
                $this->type = \Box\Spout\Common\Type::CSV;
                break;
            default:
                throw new Exception('类型不支持：文件（' . $this->file . '）不支持处理的类型！');
        }
    }

    /**
     * 描述：读文件
     * @return array 读取的文件的二位数组
     * @throws Exception
     * @author demo
     */
    public function read(){
        if ( ! is_readable($this->file)) {
            throw new Exception('文件不可读：文件（' . $this->file . '）不可读！');
        }
        $reader = $this->getReader();
        $reader->open($this->file);
        $result = [[]];
        foreach ($reader->getSheetIterator() as $sheetID=>$sheet) {
            foreach ($sheet->getRowIterator() as $rowID=>$row) {
                $result[$sheetID][$rowID] = $row;
            }
        }
        $this->close();
        return $result;
    }

    /**
     * 描述：写文件
     * @param array $data 一维数组时一次写一行 二维数组时一次写多行
     * @param array $styleArray 写入行样式
     * @author demo
     */
    public function write($data, $styleArray = null) {
        $writer = $this->getWriter();
        $writer->openToFile($this->file);
        $styleObject = $styleArray ? $this->setRowStyle($styleArray) : null;
        if (is_array($data[0])) {
            if ($styleObject) {
                $writer->addRowsWithStyle($data, $styleObject);
            } else {
                $writer->addRows($data);
            }
        } else {
            if ($styleObject) {
                $writer->addRowWithStyle($data, $styleObject);
            } else {
                $writer->addRow($data);
            }
        }
        $this->close();
    }

    /**
     * 描述：获取文档类型
     * @return string
     * @author demo
     */
    public function getType(){
        return $this->type;
    }

    /**
     * 描述：获取读handle
     * @return \Box\Spout\Reader\ReaderInterface
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @author demo
     */
    private function getReader(){
        if(!$this->reader){
            $this->reader = \Box\Spout\Reader\ReaderFactory::create($this->type);
            if($this->type===\Box\Spout\Common\Type::CSV){
                //默认xlsx转为csv的都是gbk编码
                $this->reader->setEncoding('GBK');
            }
        }
        return $this->reader;
    }

    /**
     * 描述：获取写handle
     * @return \Box\Spout\Writer\WriterInterface
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @author demo
     */
    private function getWriter(){
        if(!$this->writer){
            $this->writer = \Box\Spout\Writer\WriterFactory::create($this->type);
        }
        return $this->writer;
    }

    /**
     * 描述：设置单元格style
     * @param $styleArray
     * @return \Box\Spout\Writer\Style\Style
     * @author demo
     */
    private function setRowStyle($styleArray){
        $styleObject = (new \Box\Spout\Writer\Style\StyleBuilder());
        //可以扩展更多
        if(array_key_exists('fontBold',$styleArray)){
            $styleObject = $styleObject->setFontBold();
        }
        if(array_key_exists('fontSize',$styleArray)){
            $styleObject = $styleObject->setFontSize($styleArray['fontSize']);
        }
        if(array_key_exists('fontColor',$styleArray)){
            //fontColor 必须是标准RGB颜色字符串
            $styleObject = $styleObject->setFontColor($styleArray['fontColor']);
        }
        return $styleObject->setShouldWrapText()->build();
    }

    /**
     * 描述：关闭使用资源
     * @author demo
     */
    private function close(){
        $this->reader->close();
    }

}