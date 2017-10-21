<?php
/**
 * @author demo
 * @date 2014年12月29日
 */
/**
 * 文档类型管理模型类，用于文档类型管理相关操作
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserExpModel extends BaseModel{

    /**
     * 设置缓存
     * @author demo
     */
    public function setCache(){
        $userExp=$this->selectData(
            '*',
            '1=1'
        );
        $url = $this->expUrl();
        foreach($userExp as $i=>$iUserExp){
            $expList[$iUserExp['ExpName']]=$iUserExp;
            $expList[$iUserExp['ExpName']]['url']=U($url[$iUserExp['ExpName']]['url']);
            $expList[$iUserExp['ExpName']]['indexUrl']=U($url[$iUserExp['ExpName']]['indexUrl']);
            $expList[$iUserExp['ExpName']]['target']=$url[$iUserExp['ExpName']]['target'];
            $expList[$iUserExp['ExpName']]['open']=$url[$iUserExp['ExpName']]['open'];
        }
        S('expList',$expList);//按照经验标示排序
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='expList',$num=0){
        switch($str){
            case 'expList':
                $buffer=S('expList');
                break;
            default:
                return false;
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
    /**
     * 根据经验标示及用户ID，获取对应ExpName名称的经验值
     * @param string $expName 经验任务标示
     * @param string $userID 用户ID
     * @return array
     * @author demo
     */
    public function getExpList($expName,$userID){
        $thisExpMsg=$this->unionSelect('getUserExpByMsg',$expName,$userID);
        return $thisExpMsg[0];
    }

    /**
     * 验证是否获取经验
     * @param string $userName 用户名称
     * @param string $expName 任务名称
     * @param int $ifDel 获取验证
     * @return int 对应的经验点数
     * @author demo
     *
     */
    public function getExp($userName,$expName,$ifDel=0){
        $expCache=SS('expList');
        $userMsg=$this->getModel('User')->getInfoByName($userName);   //获取用户信息

        $thisExpMsg=$this->getExpList($expName,$userMsg[0]['UserID']);
        //没有该经验任务的相关记录
        if(empty($thisExpMsg)){
            /*            $newData['ExpID']=$thisExpMsg['ExpID'];
                        $newData['UserID']=$userMsg[0]['UserID'];
                        $newData['LastTime']=time();
                        $insertResult=$this->getModel('UserExpRecord')->insertData(
                            $newData
                        );
                        if($insertResult){ //插入数据成功*/
            $expNum=$expCache[$expName]['ExpPoint'];
            if($userMsg[0]['IfAuth']==1){
                $expNum=$expCache[$expName]['ExpAuthPoint'];
            }
            return $expNum;
            //    }
        }

        //任务有结果，任务是一次性的
        if($thisExpMsg['ExpTime']==0){
            if($ifDel==1){
                $delMsg=$this->getModel('UserExpRecord')->deleteData(
                    'RecordID in ('.$thisExpMsg['RecordID'].')'
                );
                if($thisExpMsg['IfAuth']){
                    return '-'.$thisExpMsg['ExpAuthPoint']; //返回认证负值
                }
                return '-'.$thisExpMsg['ExpPoint']; //返回未认证负值
            }
            return 0;
        }

        //任务是每天1次的
        if($thisExpMsg['ExpTime']==1){
            //判断当天
            if(date('Y-m-d',time()) == date('Y-m-d',$thisExpMsg['AddTime'])){
                if($thisExpMsg['IfAuth']){
                    return '-'.$thisExpMsg['ExpAuthPoint']; //返回认证负值
                }
                return '-'.$thisExpMsg['ExpPoint']; //返回未认证负值
            }else{
                if($thisExpMsg['IfAuth']){
                    return $thisExpMsg['ExpAuthPoint']; //返回应当录入经验
                }
                return $thisExpMsg['ExpPoint']; //返回应当录入经验
            }
        }

        //任务是每天多次
        if($thisExpMsg['ExpTime']==2){
            if($thisExpMsg['IfAuth']){
                return $thisExpMsg['ExpAuthPoint']; //返回经验值
            }
            return $thisExpMsg['ExpPoint']; //返回经验值

        }
    }

    /**
     * 添加经验日志，用户经验字段累加
     * @param string $userName 用户名
     * @param string $tagName 用户操作标签说明
     * @return bool
     * @author demo
     */
    public function addUserExpAll($userName,$tagName){
        $lastResult='';
        $thisExpMsg=$this->getExp($userName,$tagName);
        if($thisExpMsg>0){
            //执行经验记录
            $logResult=$this->getModel('UserExpRecord')->addUserExpLog($userName,$tagName,$thisExpMsg);
            if($logResult){
                //对用户的经验字段累加
                $lastResult=$this->getModel('User')->addUserExp($userName,$thisExpMsg);
            }
        }
        return $lastResult;
    }

    /**
     * 完成任务地址链接
     * 数组中，键为动作标记
     * 对应数组 [url] 跳转地址
     *         [target] 是否新页面打开 0否 1是
     *         [open] 是否开启任务 0否 1开启
     * @return array
     * @author demo
     */
    protected function expUrl(){
        return array(
            'checkTest'=>array(
                'url'=>'User/Home/myTask',
                'indexUrl'=>"Home/Index/main?u=User_Home_myTask",
                'target'=>'0',
                'open'=>'1'
            ),
            'prefectTest'=>array(
                'url'=>'User/Home/myTask',
                'indexUrl'=>"Home/Index/main?u=User_Home_myTask",
                'target'=>'0',
                'open'=>'1'
            ),
            'shareTest'=>array(
                'url'=>'Doc/Doc/show',
                'indexUrl'=>"Doc/Doc/show",
                'target'=>'1',
                'open'=>'1'
            ),
            'passCheck'=>array(
                'url'=>'Custom/CustomTestStore/add',
                'indexUrl'=>"Home/Index/main?u=Custom_CustomTestStore_add",
                'target'=>'0',
                'open'=>'1'
            ),
            'upContent'=>array(
                'url'=>'Custom/CustomTestStore/docList',
                'indexUrl'=>"Home/Index/main?u=Custom_CustomTestStore_docList",
                'target'=>'0',
                'open'=>'1'
            ),
            'login'=>array(
                'url'=>'',
                'target'=>'1',
                'open'=>'1'
            ),
            'invitation'=>array(
                'url'=>'',
                'target'=>'1',
                'open'=>'0'
            ),
            'firstUpPic'=>array(
                'url'=>'Custom/CustomTestStore/photograph',
                'indexUrl'=>"Home/Index/main?u=Custom_CustomTestStore_photograph",
                'target'=>'0',
                'open'=>'1'
            ),
            'firstUpTest'=>array(
                'url'=>'Custom/CustomTestStore/add',
                'indexUrl'=>"Home/Index/main?u=Custom_CustomTestStore_add",
                'target'=>'0',
                'open'=>'1'
            ),
            'auth'=>array(
                'url'=>'User/Index/authTeacher',
                'indexUrl'=>'User/Index/showAuthInfo',
                'target'=>'1',
                'open'=>'1'
            ),
            'email'=>array(
                'url'=>'User/Home/info',
                'indexUrl'=>"Home/Index/main?u=User_Home_info",
                'target'=>'0',
                'open'=>'1'
            ),
            'mobile'=>array(
                'url'=>'User/Home/info',
                'indexUrl'=>"Home/Index/main?u=User_Home_info",
                'target'=>'0',
                'open'=>'1'
            )
        );
    }
}
?>