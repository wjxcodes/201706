<?php
/**
 * @author demo 
 * @date 2015年1月4日
 */
/**
 * 章节管理模型类，用于章节管理相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class ChapterModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'Lft ASC');
    }
    /**
     * 更新缓存
     * @author demo
     */
    public function setCache(){
        $parentPathList=array(); //父类路径数据集
        $childList=array(); //下一级子类数据集
        $chapterIDList=array(); //所有子类id以逗号间隔数据集
        $subject=array();

        $areaArray=$this->selectData(
            '*',
            '1=1',
            'Lft ASC');
        if($areaArray){
            $pathArray=array();
            $n=0;//隐藏章节的最右值，用于判断当前章节是否跳过，不进行缓存
            foreach($areaArray as $i=>$iAreaArray){
                //隐藏章节遵循父类不显示时，子类继承父类不显示
                //如果当前章节的最左值小于隐藏章节的最右值，
                //说明是隐藏章节的子类,所以跳过该章节
                if($iAreaArray['Lft'] < $n){
                    continue;
                }
                //生成数据缓存
                if($iAreaArray['IfShow']==0){
                    //将隐藏章节的最右值赋值给$n
                    $n = $iAreaArray['Rgt'];
                    continue;//跳出循环
                }

                $chapterList[$iAreaArray['ChapterID']]['ChapterName']=$iAreaArray['ChapterName'];
                $chapterList[$iAreaArray['ChapterID']]['SubjectID']=$iAreaArray['SubjectID'];
                //去除重复章节的时候需要Lft数据
                $chapterList[$iAreaArray['ChapterID']]['Lft']=$iAreaArray['Lft'];
                $chapterList[$iAreaArray['ChapterID']]['Rgt']=$iAreaArray['Rgt'];
                $chapterList[$iAreaArray['ChapterID']]['ChapterPic']=$iAreaArray['ChapterPic'];
                if($iAreaArray['Rgt']-$iAreaArray['Lft']==1) {
                    $chapterList[$iAreaArray['ChapterID']]['Last'] = 1;
                }else {
                    $chapterList[$iAreaArray['ChapterID']]['Last'] = 0;
                }
                if($iAreaArray['Rgt']!=$iAreaArray['Lft']+1){
                    $chapterIDList[$iAreaArray['ChapterID']]=$iAreaArray['ChapterID'];
                }
                if($pathArray){
                    foreach($pathArray as $iPathArr){
                        $chapterIDList[$iPathArr['ChapterID']].=','.$iAreaArray['ChapterID'];
                    }
                }

                //生成父类路径path缓存
                if(count($pathArray)>1){
                    $tmpArray=array();
                    foreach($pathArray as $j=>$jPathArray){
                        $tmpArray[count($pathArray)-$j-1]=$jPathArray;
                    }
                    $parentPathList[$iAreaArray['ChapterID']]=$tmpArray;
                }else{
                    $parentPathList[$iAreaArray['ChapterID']]=$pathArray;
                }

                $iAreaArray['Last'] = ($iAreaArray['Rgt']-$iAreaArray['Lft']==1 ? 1 : 0 );
                //生成所有子类list缓存
                $tmpAreaArray=formatString('removeArrayField',$iAreaArray,'Rgt,Lft,IfShow,ChapterPic');
                if(empty($pathArray)){
                    $childList[0][]=$tmpAreaArray;
                }else{
                    $childList[$pathArray[count($pathArray)-1]['ChapterID']][]=$tmpAreaArray;
                }

                if(!is_numeric($areaArray[$i+1]['Lft'])) continue;
                //增加 不变 减少
                if($iAreaArray['Rgt']==$areaArray[$i+1]['Lft']-1){
                }else if($iAreaArray['Lft']==$areaArray[$i+1]['Lft']-1){
                    $pathArray[]=array(
                        'ChapterID'=>$iAreaArray['ChapterID'],
                        'ChapterName'=>$iAreaArray['ChapterName']
                    );
                }else{
                    $k=$areaArray[$i+1]['Lft'] - $iAreaArray['Rgt'] -1;
                    for($j=0;$j<$k;$j++){
                        array_pop($pathArray);
                    }
                }
            }
        }

        //生成章节树形结构
        foreach($childList[0] as $i=>$iChildList){
            $tmpStr=$this->getArrListN($childList,$iChildList['ChapterID']);
            $key=count($subject[$iChildList['SubjectID']]);
            $subject[$iChildList['SubjectID']][$key]=$iChildList;
            if(!empty($tmpStr)){
                $subject[$iChildList['SubjectID']][$key]['sub']=$tmpStr;
            }
        }
        S('chapterParentPath',$parentPathList);  // 缓存父类路径path数据
        S('chapterChildList',$childList);  // 缓存下一级子类list数据
        S('chapterList',$chapterList);  // 缓存子类list数据
        S('chapterIDList',$chapterIDList);  // 缓存所有子类id以逗号间隔
        S('chapterBySubjectN',$subject);//根据学科区分知识点(含有N层，下一次层为sub)
    }
    /**
     * 获取缓存
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='chapterList',$num=0){
        switch($str){
            case 'chapterList':
                $buffer=S('chapterList');  // 缓存子类list数据
                break;
            case 'chapterParentPath':
                $buffer=S('chapterParentPath');  // 缓存子类list数据
                break;
            case 'chapterChildList':
                $buffer=S('chapterChildList');  // 缓存子类list数据
                break;
            case 'chapterIDList':
                $buffer=S('chapterIDList');  // 缓存子类list数据
                break;
            case 'chapterBySubjectN':
                $buffer=S('chapterBySubjectN'); //根据学科获取对应章节
                break;
            default :
                return false;
                break;

        }
        if(empty($buffer) and $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
    /**
     * 获取同一级数据集
     * @param array $left 以Lft为索引的数组
     * @param int $lft 左值 必须从父类左值加1
     * @return array
     * @author demo
     */
    public function getSimilar($left,$lft){
        if(!$left[$lft]){
            return '';
        }
        $output=array();
        $output[]=$left[$lft];
        $newLft=$left[$lft]['Rgt']+1;
        if($left[$newLft]){
            $tmpArr=$this->getSimilar($left,$newLft);
            if(!empty($tmpArr)) $output=array_merge($output,$tmpArr);
        }
        return $output;
    }
    /**
     * 递归生成子类
     * @param array $childArray 章节下一级数组
     * @param int $chapterID 章节id
     * @return string option字符串
     */
    public function getArrListN($childArray,$chapterID){
        $output=array();
        if(!$childArray[$chapterID]) return '';
        foreach($childArray[$chapterID] as $i=>$iChildArray){
            $output[$i]=$iChildArray;
            if($childArray[$iChildArray['ChapterID']]){
                $tmpArr=$this->getArrListN($childArray,$iChildArray['ChapterID']);
                if(!empty($tmpArr))  $output[$i]['sub']=$tmpArr;
            }
        }
        return $output;
    }
    /**
     * 对多个章节进行过滤
     * 规则：同一章节下的    必修 第二级只能有一个 超出一个则返回空
     *                      必修 第二级相同的则保留Lft最大的数据（rgt-lft=1）
     *                      选修 第二级每个相同的只保留一个 保留Lft最大的数据（rgt-lft=1）
     * @param array $chapterID 章节id数组
     * @return array
     * @author demo
     */
    public function filterChapterID($chapterID){
        if(!is_array($chapterID)) $chapterID=array($chapterID);
        $chapterCache=SS('chapterParentPath');
        $chapterListCache=SS('chapterList');
        $tmpArray=array(); //必修
        $tmpArray2=array(); //必修
        $tmpArrayChoose1=array(); //选修
        $tmpArrayChoose2=array(); //选修
        foreach($chapterID as $iChapterID){
            $tmpArray3=$chapterCache[$iChapterID];
            if($tmpArray3){
                $tmpCount=count($tmpArray3);
                if($tmpCount<2) continue;
                $tmpID=$tmpArray3[count($tmpArray3)-1]['ChapterID']; //第一层ChapterID
                $tmpID2=$tmpArray3[count($tmpArray3)-2]['ChapterID']; //第二层ChapterID

                if(strstr($chapterListCache[$tmpID2]['ChapterName'],'选修')){
                    $tmpArrayChoose1[$tmpID][$tmpID2][]=$iChapterID;
                    $tmpArrayChoose2[$tmpID][$tmpID2][]=$chapterListCache[$iChapterID]['Lft'];
                }else{
                    $tmpArray[$tmpID][$tmpID2][]=$iChapterID;
                    $tmpArray2[$tmpID][$tmpID2][]=$chapterListCache[$iChapterID]['Lft'];
                }
            }
        }

        $output=array();
        //必修
        if($tmpArray2){
            foreach($tmpArray2 as $i=>$iTmpArray2){
                $tmpArray3=array();
                foreach($iTmpArray2 as $j=>$jTmpArray2){
                    if(count($jTmpArray2)>1){
                        $tmp_max=array_search(max($tmpArray2[$i][$j]),$tmpArray2[$i][$j]);
                        if(!$tmpArray3 or ($tmpArray3 and $tmpArray3[1]<$tmpArray2[$i][$j][$tmp_max])){
                            $tmpArray3[0]=$tmpArray[$i][$j][$tmp_max];
                            $tmpArray3[1]=$tmpArray2[$i][$j][$tmp_max];
                        }
                    }else{
                        if(!$tmpArray3 or ($tmpArray3 and $tmpArray3[1]<$tmpArray2[$i][$j][0])){
                            $tmpArray3[0]=$tmpArray[$i][$j][0];
                            $tmpArray3[1]=$tmpArray2[$i][$j][0];
                        }
                    }
                }
                if($tmpArray3) $output[]=$tmpArray3[0];
            }
        }
        //选修
        if($tmpArrayChoose2){
            foreach($tmpArrayChoose2 as $i=>$iTmpArrayChoose2){
                foreach($iTmpArrayChoose2 as $j=>$jTmpArrayChoose2){
                    if(count($jTmpArrayChoose2)>1){
                        $tmpMax=array_search(max($tmpArrayChoose2[$i][$j]),$tmpArrayChoose2[$i][$j]);
                        $output[]=$tmpArrayChoose1[$i][$j][$tmpMax];
                    }else{
                        $output[]=$tmpArrayChoose1[$i][$j][0];
                    }
                }
            }
        }
        return $output;
    }
    /**
     * 把数组中的数据转换成option列表 待删除
     * @param array $buffer 章节数组
     * @param int $ThisID=0 选中的章节id
     * @return string option字符串
     */
    public function getListOption($buffer,$ThisID=0){
        $right=array();
        $output='';
        // 显示每一行
        foreach($buffer as $iBuffer){
            // only check stack if there is one
            if (count($right) > 0) {
                // 检查我们是否应该将节点移出堆栈
                while ($right[count($right) - 1] < $iBuffer['Rgt'] && count($right)>0) {
                    array_pop($right);
                }
            }
            $select='';
            if($ThisID!=0 && $ThisID==$iBuffer['ChapterID']) $select=' selected="selected"';
            // 缩进显示节点的名称
            $output.='<option value="'.$iBuffer['ChapterID'].'"'.$select.'>'.str_repeat('　',count($right)) . $iBuffer['ChapterName'] . "</option>";
            // 将这个节点加入到堆栈中
            $right[] = $iBuffer['Rgt'];
        }
        return $output;
    }
    /*获取父类id下的所有子类目录树 待删除
     * @param int $ParentID 章节id
     * return array 对应子类目录树
     * */
    public function getListArray($ParentID=0){
        //获取节点的左右值
        $lft=0;$rgt=0;
        if($ParentID==0){
            $buffer=$this->maxData(
                'Rgt');
            if(!$buffer) return;
            $rgt=$buffer;
        }else{
            $buffer=$this->selectData(
                '*',
                'ChapterID= '.$ParentID);
            if(!$buffer) return ;
            $lft=$buffer[0]['Lft'];
            $rgt=$buffer[0]['Rgt'];
        }
        $right=array();
        $buffer=$this->selectData(
            '*',
            'Lft>='.$lft.' AND Rgt<='.$rgt,
            'Lft asc');
        // 显示每一行
        foreach($buffer as $i=>$iBuffer){
            // only check stack if there is one
            if (count($right) > 0) {
                // 检查我们是否应该将节点移出堆栈
                while ($right[count($right) - 1] < $iBuffer['Rgt'] && count($right)>0) {
                    array_pop($right);
                }
            }
            // 缩进显示节点的名称
            $buffer[$i]['NewChapterName']=str_repeat('　',count($right)) . $iBuffer['ChapterName'];
            // 将这个节点加入到堆栈中
            $right[] = $iBuffer['Rgt'];
        }
        return $buffer;
    }
    
    //获取下一级子类 待删除
    public function getChild($PID=0){
        if(!empty($PID) && $PID>0){
            $buffer=$this->selectData(
                '*',
                ' ChapterID= '.$PID);
            $Lft=$buffer[0]['Lft']+1;
        }
        $arr=array();
        $arr=$this->getSimilar($arr,$Lft);
        return $arr;
    }
    
    //获取所有子类 待删除
    public function getChildAll($PID=0){
        $Lft=0;
        if(!empty($PID) && $PID>0){
            $buffer=$this->selectData(
                'Lft,Rgt',
                ' ChapterID= '.$PID);
            $Lft=$buffer[0]['Lft'];
            $Rgt=$buffer[0]['Rgt'];
        }else{
            $Rgt=$this->maxData('Rgt');
        }
        $arr=array();
        if($Rgt!=$Lft+1){
            $arr=$this->selectData(
                'ChapterID',
                'Lft>='.$Lft.' and Rgt<='.$Rgt);
            $output=array();
            if($arr){
                foreach($arr as $arrn){
                    $output[]=$arrn['ChapterID'];
                }
            }
            return implode(',',$output);
        }else{
            return $PID;
        }
    }
    //获取同类 待删除
    public function get_similar($arr,$Lft){
        //查询左值等于右值+1的数据
        $buffer=$this->selectData(
            '*',
            'Lft='.$Lft
        );
        if(!$buffer) return $arr;
        else{
            if($buffer[0]['Rgt']-$buffer[0]['Lft']==1){
                $buffer[0]['Last']=1;
            }else{
                $buffer[0]['Last']=0;
            }
            $Lft=$buffer[0]['Rgt']+1;
            $arr[]=$buffer[0];
            return $this->get_similar($arr,$Lft);
        }
    }
    
    //获取父类列表
    public function getParList($paramArr){
        $s=0;
        if($paramArr['SubjectID']){
            $s=$paramArr['SubjectID'];
        }
        $ChapterChildList=SS('chapterChildList');
        if($s){
            $output=array();
            if($ChapterChildList[0]){
                foreach($ChapterChildList[0] as $buffern){
                    if($buffern['SubjectID']==$s) $output[]=$buffern;
                }
                return $output;
            }
            return $this->selectData(
                '*',
                ' PID=0 AND SubjectID='.$s,
                'OrderID asc,KlID asc');
        }

        return $ChapterChildList[0];
    }


    
    /*获取知识点下一级子类数据 用做ajax调用*/
    /* param array $paramArr 父ID的参数 
     */
    public function getData($paramArr) {
        if($paramArr['pid']){
            $pid=$paramArr['pid'];
        }else{
            $pid=0;
        }
        $output=array();
        if($pid==0){
            $output[0]=0;
        }else{
            $buffer=SS('chapterChildList');
            $output[1]=$buffer[$pid];
            if(empty($output[1])) $output[0]=0;
            else{
                $output[0]=1;
                $output[2]='请选择章节';
                $output[3]=' name="ChapterID[]" ';
            }
        }
        return $output;
    }

    //递归获取数据的树型结构
    public function getChapterSub($chapterID,$buffer){
        if($buffer[$chapterID]){
            $output=$buffer[$chapterID];
            foreach($output as $i=>$outputn){
                if($outputn['Last']!=1){
                    $output[$i]['Sub']=$this->getChapterSub($outputn['ChapterID'],$buffer);
                }
            }
            return $output;
        }
        return '';
    }

    /**
     * 根据id 获取章节路径数组或路径列表
     * @param array $paramArr 数组类型
     *                  array parent 知识点父类缓存
     *                  array self 知识点当前缓存
     *                  string ID 需要显示的知识点id 支持数组和逗号间隔字符串
     *                  string ReturnString 返回字符串的间隔方式 为空则返回数据
     * @return string|Array
     * @author demo
     */
    public function getChapterPath($paramArr){
        $parent=$paramArr['parent'];
        $self=$paramArr['self'];
        $output=array();

        $ID=$paramArr['ID'];
        if(is_array($ID)) $idList=$ID;
        else $idList=explode(',',$ID);

        foreach($idList as $i=>$iIdList){
            $output[$i]['ChapterID']=$iIdList;
            if($parent[$iIdList]){
                $tmpi=count($parent[$iIdList])-1;
                for(;$tmpi>=0;$tmpi--){
                    $output[$i]['ChapterName'].='>>'.$parent[$iIdList][$tmpi]['ChapterName'];
                }
                $output[$i]['ChapterName'].='>>'.$self[$iIdList]['ChapterName'];
            }else{
                $output[$i]['ChapterName']='>>'.$self[$iIdList]['ChapterName'];
            }
        }

        if(!empty($paramArr['ReturnString'])){
            $tmp='';
            foreach($output as $i=>$iOutput){
                $tmpStr='('.($i+1).')';
                $tmp.=$tmpStr.$iOutput['ChapterName'].$paramArr['ReturnString'];
            }
            $output=$tmp;
        }

        return $output;
    }

    /**
     * 去除按章节规则，章节包含的章节问题
     * @param $chapterIDArray array 章节ID数组
     * @return array 去重后的章节ID数组
     * @author demo
     */
    public function delChapterRepeat($chapterIDArray){
        if(!is_array($chapterIDArray)) $chapterIDArray=explode(',',$chapterIDArray);

        $chapterParent=SS('chapterParentPath');
        $newIDArray=array(); //过滤后的id数据
        $filterIDArray=array(); //需要排除的id

        $count=count($chapterIDArray);
        for($i=0;$i<$count;$i++){
            if(in_array($chapterIDArray[$i],$filterIDArray)){
                continue;
            }
            if(!is_numeric($chapterIDArray[$i])){
                return false;
            }
            $tmpID=0;
            for($j=$i+1;$j<$count;$j++){
                $chapterParent[$chapterIDArray[$i]][-1]['ChapterID']=$chapterIDArray[$i];
                $chapterParent[$chapterIDArray[$j]][-1]['ChapterID']=$chapterIDArray[$j];
                $tmpID=$this->filterChapterRepeat($chapterParent[$chapterIDArray[$i]],$chapterParent[$chapterIDArray[$j]]);

                if($tmpID!=$chapterIDArray[$i] && !strstr($tmpID,',')){
                    $tmpID=0;
                    break;
                }else if($tmpID==$chapterIDArray[$i]){
                    $filterIDArray[]=$chapterIDArray[$j];
                }
            }
            if($tmpID!=0) $newIDArray[]=$chapterIDArray[$i];
            if($i==$count-1) $newIDArray[]=$chapterIDArray[$i];
        }

        return $newIDArray;
    }

    /**
     * 比对路径是否有包含关系（包括父类）
     * @param $chapterIDPath array 章节ID路径
     * @param $chapterID2Path array 章节ID2路径
     * @return int|array 返回未被包含的路径
     * @author demo
     */
    public function filterChapterRepeat($chapterIDPath,$chapterID2Path){
        if(!is_array($chapterIDPath) || !is_array($chapterID2Path)) return '';

        $count=count($chapterIDPath);
        $count2=count($chapterID2Path);
        //从最后一级开始比对
        for($i=0;$i<$count;$i++){
            $j=$count-$i-2; //最后一级
            $k=$count2-$i-2; //最后一级2

            if($chapterIDPath[$j]['ChapterID']==$chapterID2Path[$k]['ChapterID']){
                continue; //父类路径相同继续比对
            }else{
                if(empty($chapterID2Path[$k]['ChapterID'])){
                    return $chapterID2Path[-1]['ChapterID'];
                }
                return $chapterIDPath[-1]['ChapterID'].','.$chapterID2Path[-1]['ChapterID'];
            }
        }

        return $chapterIDPath[-1]['ChapterID'];
    }

    /**
     * 根据最终章节ID,够造章节树状结构
     * @param string $chapterStr 章节ID字符串  例如：,1720,1692,1749
     * @return array 树状结构
     * @author demo
     */
    public function buildChapterTree($chapterStr){
        $chapterIDArray=explode(',',trim($chapterStr,','));
        $chapterParent=SS('chapterParentPath');
        $chapter=SS('chapterList');
        foreach($chapterIDArray as $i=>$iChapterIDArray){
            $firstTop=$chapterParent[$chapterIDArray[$i]];
            //去除版本
            $firstTop[count($firstTop)]=$chapter[$chapterIDArray[$i]];
            $firstTop=array_values($firstTop);
            unset($firstTop[0]);
            $newChapter[]=array_values($firstTop);
        }
        //确定第一层内容
        $first='';
        foreach($newChapter as $i=>$iNewChapter){
            if(!in_array($newChapter[$i][0],$first)){
                if(!empty($newChapter[$i][0])){
                    $first[]=$newChapter[$i][0];
                }
            }
        }
        //获取第二层
        foreach($newChapter as $i=>$iNewChapter){
            foreach($first as $j=>$jFirst){
                if($newChapter[$i][0]['ChapterID']==$first[$j]['ChapterID']){
                    if(!in_array($newChapter[$i][1],$first[$j]['sub'])){
                        if(!empty($newChapter[$i][1])){
                            $first[$j]['sub'][]=$newChapter[$i][1];
                        }
                    }
                }
            }
        }
        //设置第三层
        foreach($newChapter as $i=>$iNewChapter){
            foreach($first as $j=>$jFirst){
                foreach($first[$j]['sub'] as $k=>$kFirst){
                    if($newChapter[$i][1]['ChapterID']==$first[$j]['sub'][$k]['ChapterID']){
                        if(!in_array($newChapter[$i][2],$first[$j]['sub'][$k]['sub'])){
                            if(!empty($newChapter[$i][2])){
                                $first[$j]['sub'][$k]['sub'][]=$newChapter[$i][2];
                            }
                        }
                    }
                }
            }
        }
        return $first;
    }
    /**
     * 输出树形结构数据
     * @param array $chapterArray 章节数据集
     * @return string 树状结构
     * @author demo
     */
    public function showChapterTree($chapterArray,$for=0){
        $output='';
        $pre='';
        if($for==0) $pre.='|';
        else{
            $pre.='|';
            for($i=0;$i<$for;$i++){
                $pre.='一';
            }
        }
        foreach($chapterArray as $iChapterArray){
            $output.=$pre.$iChapterArray['ChapterName'].'<br/>';
            if($iChapterArray['sub']){
                $thisFor=$for+1;
                $output.=$this->showChapterTree($iChapterArray['sub'],$thisFor);
            }
        }
        return $output;
    }

    /**
     * 获取章节路径
     * @param array $arr 章节id数组
     * @param array $chapterParent 章节父类缓存数据集
     * @param array $chapterList 章节id为键的数据集
     * @return array
     * @author demo
     */
    public function getChapterAll($arr,$chapterParent,$chapterList) {
        if(!$arr) return ;
        $output=array();
        if(!$chapterParent) $chapterParent=SS('chapterParentPath');
        if(!$chapterList) $chapterList=SS('chapterList');
        foreach($arr as $i=>$iArr){
            if(!$iArr) continue;
            $tmpStr='('.($i+1).')';
            foreach($chapterParent[$iArr] as $jChapterParent){
                $tmpStr.='>>'.$jChapterParent['ChapterName'];
            }
            $output[]=$tmpStr.'>>'.$chapterList[$iArr]['ChapterName'];
        }
        return $output;
    }
}