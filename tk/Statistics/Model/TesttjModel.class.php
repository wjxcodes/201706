<?php
/**
 * @author demo
 * @date 2014年8月4日
 */
/**
 * 试题统计类，用于处理试题统计相关操作
 */
namespace Statistics\Model;
use Common\Model\BaseModel;
class TesttjModel extends BaseModel{
    /**
     * 获取最后一条数据；
     * @author demo
     */
    public function getLastRecord(){
        $buffer=$this->getModel('TestTj')->selectData(
            '*',
            '1=1',
            'TjID DESC',
            '1');
        return $buffer[0];
    }
    /**
     * 查询统计数据；
     * @author demo
     */
    public function checkData(){
        $output=array();
        //试题总数
        $testReal=$this->getModel('TestReal'); 
        $output['test']=$testReal->getTotalRow();
        //文档总数
        $doc=$this->getModel('Doc');
        $output['doc']=$doc->getTotalRow();
        //下载文档总数
        $docDown=$this->getModel('DocDown');
        $output['down']=$docDown->getTotalRow();
        //登录总数
        $user=$this->getModel('User'); 
        $output['login']=$user->getTotalRow();
        return $output;
    }
    /**
     * 获取缓存；
     * @author demo
     */
    public function getCache(){
        $buffer = $this->getLastRecord(); //获取最后一条数据
        //检查缓存是否过期
        if($buffer){
            if(1==date('w',time()) && date('Ymd',$buffer['TjTimes'])!=date('Ymd',time())){
                return $this->setCache();
            }else{
                $testtj=S('testtj');
                if($testtj) return $testtj;
                else return $this->setCache($buffer);
            }
        }else{
            return $this->setCache();
        }
    }
    /**
     * 更新缓存；
     * @author demo
     */
    public function setCache($buffer){
        //查询旧数据后三条
        $tj_list=$this->getModel('TestTj')->selectData(
            '*',
            '1=1',
            'TjID DESC',
            3);
        //仅更新数据缓存
        if($buffer){
            //本周数据
            $buffer['bTotalTest']=$buffer['TjTotalTest'];
            $buffer['bTotalDoc']=$buffer['TjTotalDoc'];
            $buffer['bTotalZj']=$buffer['TjTotalZj'];
            //上周数据存在则更新本周数据
            if($tj_list[1]){ 
                //上周数据
                $buffer['sTotalTest']=$tj_list[1]['TjTotalTest'];
                $buffer['sTotalDoc']=$tj_list[1]['TjTotalDoc'];
                $buffer['sTotalZj']=$tj_list[1]['TjTotalZj'];
                $buffer['bTotalTest']=$tj_list[0]['TjTotalTest']-$tj_list[1]['TjTotalTest'];
                $buffer['bTotalDoc']=$tj_list[0]['TjTotalDoc']-$tj_list[1]['TjTotalDoc'];
                $buffer['bTotalZj']=$tj_list[0]['TjTotalZj']-$tj_list[1]['TjTotalZj'];
                if($buffer['bTotalTest']<0) $buffer['bTotalTest']=0;
                if($buffer['bTotalDoc']<0) $buffer['bTotalDoc']=0;
                if($buffer['bTotalZj']<0) $buffer['bTotalZj']=0;
                
                //上上周数据存在更新上周数据
                if($tj_list[2]){
                    $buffer['sTotalTest']=$tj_list[1]['TjTotalTest']-$tj_list[2]['TjTotalTest'];
                    $buffer['sTotalDoc']=$tj_list[1]['TjTotalDoc']-$tj_list[2]['TjTotalDoc'];
                    $buffer['sTotalZj']=$tj_list[1]['TjTotalZj']-$tj_list[2]['TjTotalZj'];
                    if($buffer['sTotalTest']<0) $buffer['sTotalTest']=0;
                    if($buffer['sTotalDoc']<0) $buffer['sTotalDoc']=0;
                    if($buffer['sTotalZj']<0) $buffer['sTotalZj']=0;
                }
            }
            S('testtj',$buffer);//更新缓存
        }else{ //更新并插入数据
            //获取新统计数据
            $tj_arr = $this->checkData();
            //插入统计数据
            $data=array();
            $data['TjTimes']=time();
            $data['TjTotalTest']=$tj_arr['test'];
            $data['TjTotalDoc']=$tj_arr['doc'];
            $data['TjTotalZj']=$tj_arr['down'];
            $data['TjVisit']=$tj_arr['login'];
            $this->insertData(
                $data);
            //更新缓存数据
            $buffer=array();
            //统计数据
            $buffer['TjTotalTest']=$tj_arr['test'];
            $buffer['TjTotalDoc']=$tj_arr['doc'];
            $buffer['TjTotalZj']=$tj_arr['down'];
            $buffer['TjVisit']=$tj_arr['login'];
            //本周数据
            $buffer['bTotalTest']=$tj_arr['test'];
            $buffer['bTotalDoc']=$tj_arr['doc'];
            $buffer['bTotalZj']=$tj_arr['down'];
            //上周数据
            $buffer['sTotalTest']=0;
            $buffer['sTotalDoc']=0;
            $buffer['sTotalZj']=0;
            if($tj_list[0]){//上周数据存在更新本周数据
                $buffer['bTotalTest']=$tj_arr['test']-$tj_list[0]['TjTotalTest'];
                $buffer['bTotalDoc']=$tj_arr['doc']-$tj_list[0]['TjTotalDoc'];
                $buffer['bTotalZj']=$tj_arr['down']-$tj_list[0]['TjTotalZj'];
                if($buffer['bTotalTest']<0) $buffer['bTotalTest']=0;
                if($buffer['bTotalDoc']<0) $buffer['bTotalDoc']=0;
                if($buffer['bTotalZj']<0) $buffer['bTotalZj']=0;
                
                if($tj_list[1]){//上上周数据存在更新上周数据
                    $buffer['sTotalTest']=$tj_list[0]['TjTotalTest']-$tj_list[1]['TjTotalTest'];
                    $buffer['sTotalDoc']=$tj_list[0]['TjTotalDoc']-$tj_list[1]['TjTotalDoc'];
                    $buffer['sTotalZj']=$tj_list[0]['TjTotalZj']-$tj_list[1]['TjTotalZj'];
                    if($buffer['sTotalTest']<0) $buffer['sTotalTest']=0;
                    if($buffer['sTotalDoc']<0) $buffer['sTotalDoc']=0;
                    if($buffer['sTotalZj']<0) $buffer['sTotalZj']=0;
                }
            }
            S('testtj',$buffer);//更新缓存
        }
        return $buffer;
    }
}
?>