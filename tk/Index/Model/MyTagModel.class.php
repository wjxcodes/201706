<?php
/**
 * @author demo  
 * @date 2015年1月7日
 */
/**
 * 自定义标签表
 */
namespace Index\Model;
class MyTagModel extends BaseModel{
    /**
     * 根据type获取该类型下的单页标签数据
     * @param string $tagName 标签名称
     * @return string 标签内容
     * @author demo
     */
    public function pageTag($tagName)
    {
        $tagDb = $this->selectData(
            'Content',
            'TagName="'.$tagName.'"',
            '',
            1);
         return R('Common/TestLayer/strFormat',array($tagDb[0]['Content']));
    }

}
