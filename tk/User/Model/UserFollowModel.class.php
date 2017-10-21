<?php
/**
 * 用户关注模型
 * @notice 当前业务暂未涉及关注分组问题
 * @author demo
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserFollowModel extends BaseModel{


      /**
       * 添加关注 uid关注fid
       * @param $uid int 关注者id
       * @param $fid int 被关注者id
       * @author demo
       */
      public function doFollow($uid,$fid){
          //参数处理
          $uid = intval($uid);
          $fid = intval($fid);

          //参数异常
          if($uid<=0 || $fid<=0){
              return 'Parameter Error';
          }

          //自己不能关注自己
          if($uid==$fid){
              return 'Follow Self Error';
          }

          //被关注者是否存在
          if(!$this->getModel('User')->findData(
              'UserID',
              ['UserID'=>$fid]
          )){
              return 'Followeder Not Exist';
          }

          //获取关注关系
          $followState = $this->getFollowState($uid,$fid);
          if($followState && $followState['follower'] == 0){//之前未关注
               //插入关注表
               $buffer = [];
               $buffer['UserID']     = $fid;
               $buffer['FollowerID'] = $uid;
               $buffer['AddTime']    = time();
               $result = $this->insertData($buffer);

               if($result){//粉成功
                    //通知到个人动态
                   return 'Follow Success';
               }else{
                   return 'Data Error';//数据异常
               }
          }else{
              return 'Has Followed';//已经关注
          }
      }

     /**
      * 取消关注
      * @param $uid int 关注者id
      * @param $fid int 被关注者id
      */
     public function unFollow($uid,$fid){
        //@Warning 是否是本人操作逻辑在控制器层完成

        //参数处理
        $uid = intval($uid);
        $fid = intval($fid);

         //参数异常
         if($uid<=0 || $fid<=0){
             return 'Parameter Error';
         }

         //防错
         if($uid==$fid){
             return 'Data Error';
         }

        //获取双方关注关系
        $followState = $this->getFollowState($uid,$fid);
        if($followState && $followState['follower'] == 1){//之前是关注的
             if($this->deleteData(//删除关注关系
                    ['UserID'=>$fid,'FollowerID'=>$uid]
             )){
                return 'unFollow Success';
             }else{
                 return 'Data Error';//数据异常
             }
        }else{
            return 'Not Followed';//之前未关注
        }
    }

    /**
     * 批量关注
     * @param $uid int 关注者id
     * @param $fids array 被关注者ids
     * @author demo
     */
    public function doMassFlow($uid,$fids){}

    /**
     * 获取指定用户的备注列表[类似于QQ的备注姓名]
     * @param $uid int 指定用户id
     * @author demo
     */
    public function getReamrkHash($uid){}

    /**
     * 获取指定用户的关注与粉丝数
     * @param $uids array 用户id数组
     * @param $type string 获取类型 0表示关注和粉丝 1关注 2粉丝 默认获取全部
     * @author demo
     */
    public function getFollowCount($uids,$type=0){
        $count = [];
        if(empty($uids)) return $count;
        if(!is_array($uids)) $uids = explode(',',$uids);

        //构造用户id为键值的数组
        foreach($uids as $i=>$iUids){
              $count[$iUids] = [
                  'following' => 0,
                  'follower'  => 0
              ];
        }

        //构造where条件
        $followingMap['FollowerID'] = $followerMap['UserID'] = [
            'IN',
            $uids
        ];
        if($type==0 || $type==1) {
            //关注数目
            $following = $this->unionSelect('getFollowCountByGroup','COUNT(1) as count,`FollowerID`',$followingMap,'FollowerID');
            foreach ($following as $i => $iFollowing) {
                $count[$iFollowing['FollowerID']]['following'] = $iFollowing['count'];//粉丝数
            }
        }
        if($type==0 || $type==2) {
            //粉丝数目
            $follower = $this->unionSelect('getFollowCountByGroup','COUNT(1) as count,`UserID`',$followerMap,'UserID');
            foreach ($follower as $i => $iFollower) {
                $count[$iFollower['UserID']]['follower'] = $iFollower['count'];
            }
        }

        return $count;
    }

    /**
     * 获取指定用户的关注列表,分页
     * @author demo
     */
    public function getFollowingList($uid,$page,$perPage){
        if(!$uid){//强制返回,防止错误查询
            return [];
        }

        $result = $this->selectData(
            'UserID',
            ['FollowerID'=>$uid],
            'UFID DESC',
            ($perPage*($page-1)).','.$perPage
        );

        $return = [];
        if($result){//此处获取关系
            $result = array_column($result,'UserID');
            $result = $this->getFollowStateByFids($uid,$result);

            $return[0] = array_keys($result);//返回关注的人的uid,方便获取相关信息
            
            foreach($result as $i=>$iResult){
                $return[1][$i] = $iResult['following'];//1表示相互关注 0表示单相思 -_-!
            }

        }

        return $return;
    }

    /**
     * 获取指定用户的关注列表,不分页
     * @param int $type 是否包含自身
     * @author demo
     */
    public function getFollowingListAll($uid,$type=0){
        if(!$uid){//强制返回,防止错误查询
            return [];
        }
        $result = $this->selectData(
            'UserID',
            ['FollowerID'=>$uid],
            'UFID DESC'
        );
        if($result){
            $result = array_column($result,'UserID');
        }
        if($type == 1){
           $result[]=$uid;
        }

        return $result;
    }

    /**
     * 获取指定用户的粉丝列表,分页
     * @author demo
     */
    public function getFollowerList($uid,$page,$perPage){
        if(!$uid){
            return [];
        }

        $result = $this->selectData(
            'FollowerID',
            ['UserID'=>$uid],
            'UFID DESC',
            ($perPage*($page-1)).','.$perPage
        );
        $return = [];
        if($result){//此处获取关系
            $result = array_column($result,'FollowerID');
            $result = $this->getFollowStateByFids($uid,$result);

            $return[0] = array_keys($result);//返回粉丝uid,方便获取相关信息
            foreach($result as $i=>$iResult){
                $return[1][$i] = $iResult['follower'];//1表示相互关注 0表示单相思 -_-!
            }

        }

        return $return;
    }

    /**
     * 获取指定用户的粉丝列表,不分页
     */
    public function getFollowerListAll(){}

    /**
     * 获取指定用户的相互关注分页[我们把相互关注关系称为朋友关系]
     * @author demo
     */
    public function getFriendsList(){}

    /**
     * 获取用户uid与用户fid的关注状态
     * @param $uid int 关注者id
     * @param $fid int 被关注者id
     */
    public function getFollowState($uid, $fid) {
        $followState = $this->getFollowStateByFids ( $uid, $fid );
        return $followState [$fid];
    }

   /**
    * 批量获取用户uid和多人[包括一人]的关注关系
    * @param $uid int
    * @pram $fid mixed
    * @author demo
    */
    public function getFollowStateByFids($uid, $fids) {
        $_fids = is_array($fids) ? implode ( ',', array_map ( 'intval', $fids )) : $fids;
        if (empty ( $_fids )) {
            return array ();
        }
        $followData  = $this->selectData(
              '*',
              'UserID = "'.$uid.'" AND FollowerID IN('.$_fids.') ) OR ( UserID IN('.$_fids.') AND FollowerID="' .$uid.'"',
              '',
              ''
        );

        $followStates = $this->_formatFollowState ($uid,$_fids,$followData );
        

        return $followStates [$uid];
    }

    /**
     * 格式化,用户关注数据
     * @author demo
     */
    private function _formatFollowState($uid,$fids,$followData){
        if(!is_array($fids)) $fids = explode(',',$fids);
        $followStates = [];

        foreach($fids as $i=>$iFid){
            $followStates[$uid][$iFid] = [
                'following' => 0,
                'follower'  => 0
            ];
        }

        foreach($followData as $i=>$iFollowData){
            if($iFollowData['UserID']==$uid){
                $followStates[$iFollowData['UserID']][$iFollowData['FollowerID']]['following'] = 1;//UID在UserID列,表示被FID关注
            } elseif($iFollowData['FollowerID']==$uid){
                $followStates[$iFollowData['FollowerID']][$iFollowData['UserID']]['follower'] = 1;//UID在FolloerID列,表示关注了FID
            }
        }

        return $followStates;
    }

    /**
     * 更新关注数目 [缓存使用]
     * @author demo
     */
    private function _updateFollowCount(){



    }

}
?>