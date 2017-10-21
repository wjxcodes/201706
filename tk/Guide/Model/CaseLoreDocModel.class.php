<?php
/**
 * @author demo
 * @date 15-5-4 上午11:28
 */
/**
 * 导学案知识文档类，用于导学案知识文档的操作
 */
namespace Guide\Model;
use Doc\Model\HandleWordModel;
class CaseLoreDocModel extends HandleWordModel{

    /**
     * 删除知识文档
     * @param $id string 要删除的文档id
     * @author demo
     */
    public function delFile($id){
        $buffer = $this->getModel('CaseLore')->selectData(
            '*',
            'DocID in (' . $id . ')'
        );
        if ($buffer){
            foreach($buffer as $iBuffer){
                $this->deleteAllFile($iBuffer);
            }
        }
    }
    //删除上传文件 zj_test_replace
    public function deleteReplaceFile($ReplaceID) {
        $buffer = $this->getModel('CaseLoreReplace')->selectData(
            '*',
            'ReplaceID=' . $ReplaceID,'',1);
        if ($buffer) $this->deleteAllFile($buffer[0]);
    }

    /**
     * 知识提取
     * @param $docID string 知识文档编号
     * @param $userName string 用户名
     * @param $idList array 知识点编号列表
     * @return array
     * @author demo
     */
    public function extractLore($docID,$userName,$idList=array()){
        $buffer = $this->getModel('CaseLoreDocLabel')->selectData(
            'DefaultStart,LoreField',
            '1=1',
            'OrderID asc'
        );
        $start = array ();
        $testField = array ();
        foreach ($buffer as $iBuffer) {
            $start[] = $iBuffer['DefaultStart'];
            $testField[] = $iBuffer['LoreField'];
        }
        $docData = $this->selectData(
            '*',
            'DocID='.$docID
        );
        $html = $this->getDocContent($docData[0]['DocHtmlPath']); //获取html数据
        $arrDoc = $this->division($html, $start,1); //分割
        $arrHtml = $this->division($html, $start,2); //分割
        $newArrDoc = $this->changeArrFormatDoc($arrDoc); //doc过滤
        $newArr = $this->changeArrFormat($arrHtml); //html过滤
        if(count($newArrDoc)!=count($newArr)){
            return '30717'; //您提取的文档标签有误!
        }
        if(!$idList){
            foreach($newArr as $i=>$iNewArr){
                $idList[]=$i+1;
            }
        }
        $testFieldArr = $this->getTestField();; //数据表字段对应数组
        foreach($idList as $i=>$iIdList){
            $data=array();
            $data['DocID'] = $docID;
            $tmpIdn=$iIdList;
            if($iIdList<10) $tmpIdn='0'.$tmpIdn;
            $data['NumbID'] = $docID . '-' . $tmpIdn;
            //是否提取过，如果提取过则覆盖
            $testNumb = $this->getModel('CaseLore')->selectData(
                'LoreID',
                'NumbID="' . $data['NumbID'] . '"');
            if ($testNumb) {
                $data['LoreID'] = $testNumb[0]['LoreID'];
            }else{
                //是否入库过，如果入库过则跳过
                $testNumb = $this->getModel('CaseLoreAttr')->selectData(
                    'LoreID',
                    'NumbID="' . $data['NumbID'] . '"');
                if ($testNumb) {
                    continue;
                }
            }
            //单条数据记录
            $tmpArr=array();
            $dataX = array ();
            foreach ($newArr[$iIdList -1] as $j => $jNewArr) {
                if($testFieldArr[$testField[$j]]=='Test'){
                    $data['Lore']=$jNewArr;
                    continue;
                }
                if(!strstr($testField[$j],'属性')){
                    $data[$testFieldArr[$testField[$j]]] = $jNewArr;
                    //设置路径后写入preg_replace('/src="([^\.]*)\.files/is','src="'.$http.'\\1.files',$str);
                    $dataX['Doc' . $testFieldArr[$testField[$j]]] = $newArrDoc[$iIdList -1][$j];
                    if($testFieldArr[$testField[$j]]=='Lore') $tmpArr['Lore']=$jNewArr;
                    if($testFieldArr[$testField[$j]]=='Answer') $tmpArr['Answer']=$jNewArr;
                }
                else{
                    $attr[$testFieldArr[$testField[$j]]]=preg_replace('/<[^>]*>/is','',$jNewArr); //只保留汉字
                }
            }

            //保存知识
            if (!empty ($data['LoreID'])) {
                $loreID = $data['LoreID'];
                unset($data['LoreID']);
                $this->updateData(
                    $data,
                    'LoreID='.$loreID,
                    'CaseLore');
            } elseif (!empty ($data)) {
                $loreID = $this->insertData(
                    $data,
                    'CaseLore'
                );
            }

            //添加知识属性
            $data = array ();
            $data['DocID'] = $docID;
            $data['NumbID'] = $docID . '-' . $tmpIdn;
            $data['AddTime'] = $data['LastUpdateTime'] = time();
            $data['SubjectID'] = $docData[0]['SubjectID'];
            $data['ChapterID'] = $docData[0]['ChapterID']; //对应最终章节ID
            $data['MenuID'] = (int)$docData[0]['MenuID']; //对应栏目ID
            $loreNumb = $this->getModel('CaseLoreAttr')->selectData(
                '*',
                'LoreID=' . $loreID);
            if($loreNumb){
                $data['MenuID'] = $loreNumb[0]['MenuID'];
                $this->getModel('CaseLoreAttr')->updateData(
                    $data,
                    'LoreID='.$loreID
                );
            }else{
                $data['LoreID']=$loreID;
                //获取栏目
                $menuID=0;
                $menuSubject=SS('menuSubject');
                $menuTmp='';
                foreach($menuSubject[$docData[0]['SubjectID']] as $iMenuSubject){
                    if($iMenuSubject['MenuName']==$attr['attr_types']){
                        $menuTmp=$iMenuSubject;
                    }
                }
                if($menuTmp){
                    $menuID=$menuTmp['MenuID'];
                }else{
                    $menuName='';
                    $buffer=$menuSubject[$docData[0]['SubjectID']];
                    foreach($buffer as $iBuffer){
                        if(strstr($attr['attr_types'],$iBuffer['MenuName'])){
                            if(mb_strlen($iBuffer['MenuName'],'UTF-8')>mb_strlen($menuName,'UTF-8')){
                                $menuID=$iBuffer['MenuID'];
                                $menuName=$iBuffer['MenuName'];
                            }
                        }
                    }
                }
                if(!empty($menuID))
                    $data['MenuID'] = (int)$menuID;
                $data['Admin'] = $userName;
                $this->getModel('CaseLoreAttr')->insertData(
                    $data
                );
            }
        }
    }

    /**
     * 从文档转成的html中获取试题 返回试题数组
     * @param $docID int 文档编号
     * @return array
     * @author demo
     */
    public function showLoreByDoc($docID){
        $edit = $this->selectData(
            '*',
            'DocID='. $docID);
        $buffer = $this->getModel('CaseLoreDocLabel')->selectData(
            '*',
            '',
            'OrderID asc');
        $start = array ();
        foreach ($buffer as $iBuffer) {
            $start[] = $iBuffer['DefaultStart'];
        }
        $html = $this->getDocContent($edit[0]['DocHtmlPath']); //获取html数据
        $arrHtml = $this->division($html, $start,2);//分割
        $newArr = $this->changeArrFormat($arrHtml);//html过滤

        return $newArr;
    }

    /**
     * 学案cookie字符串整理
     * @param string $cookieStr 需处理的Cookie字符串
     * @param string $typeStr 学案属性字符串
     * @return array
     * @author demo
     */
    public function formatCaseCookie($cookieStr,$typeStr){
        $cookieArray=explode(';',$cookieStr);
        if($cookieArray[0]==''){
            return '';
        }
        $host=C('WLN_DOC_HOST');
        foreach($cookieArray as $i=>$iCookieArray){
            $typeList=explode('|',$typeStr);
            $cookieList=explode('|',$iCookieArray);
            //个人知识
            if($typeList[0]==0){         //知识
                if($cookieList[1]==1){          //个人知识
                    $tableStr='CaseCustomLore';
                }else{                      //系统知识
                    $tableStr='CaseLore';
                }
                $loreData = $this->getModel($tableStr)->selectData(
                    'Lore,Answer',
                    'LoreID='.$cookieList[0]
                );
                $loreData[0]['Lore']=R('Common/TestLayer/strFormat',array($loreData[0]['Lore']));
                $resultArr[$i]='<p><span style="color:blue">【知识】</span>:'.$loreData[0]['Lore'].'</p>';
                if($typeList[1]==1){
                    $resultArr[$i].='<p><span style="color:blue">【答案】</span>'.$loreData[0]['Answer'].'</p>';
                }
            }else {             //试题
                if($cookieList[1]==1){          //校本题库
                    $tableStr='CustomTest';
                }else{                  //系统题库
                    $tableStr='TestReal';
                }
                $testData = $this->getModel($tableStr)->selectData(
                    'Test,Answer',
                    'TestID='.$cookieList[0]
                );
                $testData[0]['Test']=R('Common/TestLayer/strFormat',array($testData[0]['Test']));
                $resultArr[$i]='<p><span style="color:blue">【题文】</span>:'.$testData[0]['Test'].'</p>';
                if($typeList[1]==1){
                    $resultArr[$i].='<p><span style="color:blue">【答案】</span>'.$testData[0]['Answer'].'</p>';
                }
            }
        }
        return join('<br>',$resultArr);
    }
}