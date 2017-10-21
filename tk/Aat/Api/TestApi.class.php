<?php
/**
 * @author demo
 * @date 2016年01月03日
 */

namespace Aat\Api;


class TestApi extends BaseApi
{
    /**
     * 描述：收藏试题知识点树
     * @param $subjectID
     * @param $username
     * @return array
     * @author demo
     */
    public function collectKlTree($subjectID,$username){
//        $klCache = SS('klBySubject3')[$subjectID];
        $klCache = $this->getApiCommon('Knowledge/klBySubject3')[$subjectID];
//        $amountDb = $this->getModel('UserCollectTj')->selectData(
//            'KlID,Amount',
//            ['From'=>2,'UserName'=>$username,'SubjectID'=>$subjectID]
//        );
        $amountDb = $this->getApiUser('User/selectData', 'KlID,Amount', ['From'=>2,'UserName'=>$username,'SubjectID'=>$subjectID], '', '', 'UserCollectTj');
        $amountData = [];//知识点收藏试题数量，键为KlID，值为Amount
        foreach($amountDb as $iAmountDb){
            $amountData[$iAmountDb['KlID']] = $iAmountDb['Amount'];
        }
        $result = [];
        if ($klCache) {
            foreach ($klCache as $i => $iKlCache) {
                $amount = $amountData[$iKlCache['KlID']]?$amountData[$iKlCache['KlID']]:0;
                $result[$i] = [
                    'klName'=>$iKlCache['KlName'],
                    'amount'=>$amount,
                    'klID'=>$iKlCache['KlID'],
                ];
                if (array_key_exists('sub', $iKlCache)&&is_array($iKlCache['sub'])) {
                    foreach ($iKlCache['sub'] as $j => $jKlCache) {
                        $amount = $amountData[$jKlCache['KlID']]?$amountData[$jKlCache['KlID']]:0;
                        $result[$i]['sub'][$j]  = [
                            'klName'=>$jKlCache['KlName'],
                            'amount'=>$amount,
                            'klID'=>$jKlCache['KlID'],
                        ];
                    }
                }
            }
            return [1,$result];
        } else {
            return [0,'请求数据错误，请重试！'];
        }

    }

    /**
     * 描述：收藏试题列表
     * @param $id
     * @param $username
     * @param $subjectID
     * @param $isApp
     * @return array
     * @author demo
     */
    public function collectTestList($id,$username,$subjectID,$isApp){
//        $userCollectModel = $this->getModel('UserCollect');
        if(!$id){
            return [0,'提交参数错误，请重试！'];
        }
//        $kl = SS('klList')[$id];
        $kl = $this->getApiCommon('Knowledge/klList')[$id];
        if(!$kl){
            $kl=$id;
        }
//        $klName = SS('knowledge')[$id]['KlName'];
        $klName = $this->getApiCommon('Knowledge/knowledge')[$id]['KlName'];
        if(!$subjectID){
            return [0,'请先选择学科！'];
        }
//        $count = $userCollectModel->unionSelect('userCollectSelectCount',$username,$subjectID,$kl);
        $count = $this->getApiUser('User/unionSelect', 'userCollectSelectCount',$username,$subjectID,$kl);
        $page = handlePage('init',$count,5,['type'=>1]);
        $show = $page->show(false); // 分页显示输出
        // 进行分页数据查询
        $pageStr=$page->firstRow . ',' . $page->listRows;
//        $list = $userCollectModel->unionSelect('userCollectSelectPageData',$username,$subjectID,$kl,$pageStr);
        $list = $this->getApiUser('User/unionSelect', 'userCollectSelectPageData',$username,$subjectID,$kl,$pageStr);
        $testIDArray = [];
        foreach($list as $iList){
            $testIDArray[] = $iList['TestID'];
        }
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid', 'testnormal', 'answernormal', 'analyticnormal'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDArray);
//        $testIndex = $testQuery->getResult($division =  false)[0];//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid', 'testnormal', 'answernormal', 'analyticnormal'], ['TestID'=>$testIDArray], '', ['page'=>1,'perpage'=>500], 0, 1)[0];
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid', 'testnormal', 'answernormal', 'analyticnormal'], ['TestID'=>$testIDArray], '', ['page'=>1,'perpage'=>500], 0, 1)[0];
        //查询试题
        foreach ($list as $i => $k) {
            $testID = $k['TestID'];
            $list[$i]['LoadTime'] = date('Y-m-d H:i:s', $k['LoadTime']);
            $list[$i]['Test'] = $testIndex[$testID]['testnormal'];
            $list[$i]['Answer'] = $testIndex[$testID]['answernormal'];
            $list[$i]['Analytic'] = $testIndex[$testID]['analyticnormal'];
            $list[$i]['No'] = $page->firstRow+($i+1);
        }
        $result['test'] = $list;
        $result['first'] = $page->firstRow+1;
        if(!$isApp){
            $result['show'] = $show;
        }
        $result['allAmount'] = $count;
        $result['klName']=$klName;
        if ($list) {
            return [1,$result];
        } else {
            return [0,'暂无收藏试题显示！'];
        }
    }

    /**
     * 描述：收藏试题
     * @param $testID
     * @param $username
     * @return array
     * @author demo
     */
    public function collectSave($testID,$username){
//        $userCollectModel = $this->getModel('UserCollect');
        if(!$testID){
            return [0,'缺少试题ID！'];
        }
//        $ifExist = $this->getModel('TestAttrReal')->findData(
//            'TestID,SubjectID',
//            ['TestID'=>$testID]);
        $ifExist = $this->getApiTest('Test/findData', 'TestID,SubjectID', ['TestID'=>$testID], '', 'TestAttrReal');
        if(!$ifExist){
            return [0,'试题不存在！'];
        }
        $subjectID = $ifExist['SubjectID'];
//        $ifCollected = $userCollectModel->findData(
//            'CollectID',
//            ['UserName'=>$username,'TestID'=>$testID,'From'=>2]
//        );
        $ifCollected = $this->getApiUser('User/findData', 'CollectID', ['UserName'=>$username,'TestID'=>$testID,'From'=>2], '', 'UserCollect');
        if ($ifCollected) {
            return [1,'试题已经收藏！'];
        }
        $data = [
            'FavName' => '',
            'TestID' => $testID,
            'LoadTime' => time(),
            'UserName' => $username,
            'SubjectID' => $subjectID,
            'CatalogID' => 0,
            'From' => 2,
        ];
//        $insertCollect = $userCollectModel->insertData($data);
        $insertCollect = $this->getApiUser('User/insertData', $data, 'UserCollect');

        $this->updateCollectTj($username,$testID,'add',$subjectID);//更新收藏统计表数据
        if ($insertCollect) {
            return [1,'收藏成功！'];
        } else {
            return [0,'收藏失败，请重试！'];
        }
    }

    /**
     * 描述：取消收藏
     * @param $testID
     * @param $username
     * @return array
     * @author demo
     */
    public function collectDel($testID,$username){
//        $userCollectModel = $this->getModel('UserCollect');
        if (!$testID) {
            return [0,'缺少试题ID！'];
        }
//        $ifExist = $userCollectModel->findData(
//            'CollectID',
//            ['UserName'=>$username, 'TestID'=>$testID, 'From'=>2]
//        );
        $ifExist = $this->getApiUser('User/findData', 'CollectID', ['UserName'=>$username, 'TestID'=>$testID, 'From'=>2], '', 'UserCollect');
        if ($ifExist) {
//            $del = $userCollectModel->deleteData(
//                'CollectID='.$ifExist['CollectID']
//            );
            $del = $this->getApiUser('User/deleteData', 'CollectID='.$ifExist['CollectID'], 'UserCollect');
            $this->updateCollectTj($username,$testID,'del');//更新收藏统计表数据
            if ($del) {
                return [1,'删除成功！'];
            } else {
                return [0,'删除失败请重试！'];
            }
        } else {
            return [0,'已经取消收藏！'];
        }

    }

    /**
     * 描述：错题知识点树
     * @param $username
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function wrongKlTree($username,$subjectID){
        $klList = $this->getModel('UserTestKl')->getKlInfo($username,$subjectID);

        if ($klList) {
            //这里需要格式化一下数据，前端需要通用和收藏试题的知识点树
            $result = [];
            foreach($klList as $i=>$iKlList){
                $result[$i]['klName'] = $iKlList['klName'];
                $result[$i]['klID'] = $iKlList['klID'];
                $result[$i]['amount'] = $iKlList['wrongAmount'];
                if(array_key_exists('sub',$iKlList)&&is_array($iKlList['sub'])){
                    foreach($iKlList['sub'] as $j=>$jKlList){
                        $result[$i]['sub'][$j]['klName'] = $jKlList['klName'];
                        $result[$i]['sub'][$j]['klID'] = $jKlList['klID'];
                        $result[$i]['sub'][$j]['amount'] = $jKlList['wrongAmount'];
                    }
                }
            }
            return [1,$result];
        } else {
            return [0,'请求数据错误！'];
        }

    }

    /** 错题试题列表
     * 描述：
     * @param $id
     * @param $username
     * @param $isApp
     * @return array
     * @author demo
     */
    public function wrongTestList($id,$username,$isApp){
        $userAnswerRecordModel = $this->getModel('UserAnswerRecord');
        if(!$id){
            return [0,'提交参数错误，请重试！'];
        }
//        $kl = SS('klList')[$id];
        $kl = $this->getApiCommon('Knowledge/klList')[$id];
        if(!$kl){
            $kl=$id;
        }

//        $klName = SS('knowledge')[$id]['KlName'];
        $klName = $this->getApiCommon('Knowledge/knowledge')[$id]['KlName'];
        // 查询满足要求的总记录数
        $count = $userAnswerRecordModel->unionSelect('userAnswerRecordGroupByUserNameKlId',$username,$kl);

        $page = handlePage('init',$count,5,['type'=>1]);
        $show = $page->show(false); // 分页显示输出
        $firstRow = $page->firstRow;
        $listRows = $page->listRows;
        $pageStr = $firstRow . ',' . $listRows;
        $list = $userAnswerRecordModel->unionSelect('userAnswerRecordSelectPageData',$username,$kl,$pageStr);
        $testIDArray = [];
        foreach($list as $iList){
            $testIDArray[] = $iList['TestID'];
        }
//        $testQuery = getStaticFunction('TestQuery','getInstance');
//        $testQuery->setParams([
//            'field' => ['testid', 'testnormal', 'answernormal', 'analyticnormal'],
//            'page' => ['page'=>1,'perpage'=>500],
//            'limit' =>500,
//            'convert' => 'testid'
//        ],$testIDArray);
//        $testIndex = $testQuery->getResult($division =  false)[0];//0 试题数组 1总数量 2每页数量
//        $testIndex = $this->getApiTest('getAllIndexTest', ['testid', 'testnormal', 'answernormal', 'analyticnormal'], ['TestID' => $testIDArray], '', ['page'=>1,'perpage'=>500], 0, 1)[0];
        $testIndex = $this->getModel('TestRealQuery')->getIndexTest(['testid', 'testnormal', 'answernormal', 'analyticnormal'], ['TestID' => $testIDArray], '', ['page'=>1,'perpage'=>500], 0, 1)[0];
        foreach($list as $i=>$k){
            $testID = $k['TestID'];
            $list[$i]['Test'] = $testIndex[$testID]['testnormal'];
            $list[$i]['Answer'] = $testIndex[$testID]['answernormal'];
            $list[$i]['Analytic'] = $testIndex[$testID]['analyticnormal'];
            $list[$i]['No'] = $page->firstRow+($i+1);
        }
        if ($list) {
            $result = [
                'test'=>$list,
                'first'=>$page->firstRow+1,
                'allAmount'=>$count,
                'klName'=>$klName
            ];
            if(!$isApp){
                $result['show']=$show;
            }
            return [1,$result];
        } else {
            return [0,'还没有错题记录！'];
        }

    }

    /**
     * 更新收藏统计表数据 收藏和取消收藏都需要更新
     * @param $username
     * @param $testID
     * @param $subjectID
     * @param string $operate 操作，收藏add 取消收藏del
     * @author demo
     */
    private function updateCollectTj($username,$testID,$operate='add',$subjectID=0){
//        $userCollectTjModel = $this->getModel('UserCollectTj');
//        $testKlDb = $this->getModel('TestKlReal')->selectData(
//            'TestID,KlID',
//            ['TestID'=>$testID]
//        );
        $testKlDb = $this->getApiTest('Test/selectData', 'TestID,KlID', ['TestID'=>$testID], '', '', 'TestKlReal');
//        $klCache = SS('knowledgeParent');
        $klCache = $this->getApiCommon('Knowledge/knowledgeParent');
        $klLevel1 = [];
        $klLevel2 = [];
        $klLevel3 = [];
        foreach($testKlDb as $iTestKlDb){
            $klID = $iTestKlDb['KlID'];
            $parentKl = $klCache[$klID];
            if(is_array($parentKl)){
                if(count($parentKl)===2){
                    array_push($klLevel1,$parentKl[1]['KlID']);
                    array_push($klLevel2,$parentKl[0]['KlID']);
                    array_push($klLevel3,$klID);
                }elseif(count($parentKl)===1){
                    array_push($klLevel1,$parentKl[0]['KlID']);
                    array_push($klLevel2,$klID);
                }
            }else{
                //一级知识点
                array_push($klLevel1,$klID);
            }
        }
        $klLevel1=array_unique($klLevel1);
        $klLevel2=array_unique($klLevel2);
        $klLevel3=array_unique($klLevel3);
        $needUpdateKlID = array_merge($klLevel1,$klLevel2,$klLevel3);
        foreach($needUpdateKlID as $iNeedUpdateKlID){
            $klID = $iNeedUpdateKlID;
//            $collectTjDb = $userCollectTjModel->findData(
//                'CollectTjID,Amount',
//                ['From'=>2,'UserName'=>$username,'KlID'=>$klID]
//            );
            $collectTjDb = $this->getApiUser('User/findData', 'CollectTjID,Amount', ['From'=>2,'UserName'=>$username,'KlID'=>$klID], '', 'UserCollectTj');
            if($collectTjDb){
                //更新
                if($operate === 'add'){
                    $amount = $collectTjDb['Amount']+1;
                }elseif($operate === 'del'){
                    $amount = ($collectTjDb['Amount']-1)>-1?($collectTjDb['Amount']-1):0;
                }
//                $userCollectTjModel->updateData(
//                    ['Amount'=>$amount,'UpdateTime'=>time()],
//                    ['CollectTjID'=>$collectTjDb['CollectTjID']]
//                );
                $this->getApiUser('User/updateData', ['Amount'=>$amount,'UpdateTime'=>time()], ['CollectTjID'=>$collectTjDb['CollectTjID']], 'UserCollectTj');
            }else{
                //插入
                if($operate === 'add'){
//                    $userCollectTjModel->insertData(
//                        ['From'=>2,'UserName'=>$username,'SubjectID'=>$subjectID,'KlID'=>$klID,'Amount'=>1,'UpdateTime'=>time()]
//                    );
                    $this->getApiUser('User/insertData', ['From'=>2,'UserName'=>$username,'SubjectID'=>$subjectID,'KlID'=>$klID,'Amount'=>1,'UpdateTime'=>time()], 'UserCollectTj');
                }
            }
        }
    }

}