<?php
/**
 * @author demo  
 * @date 2015年1月12日
 */
/**
 *系统管理控制器类，用于系统参数相关操作
 */
namespace Manage\Controller;
class SystemController extends BaseController  {
    var $moduleName = '参数配置';
    /**
     * 查看系统参数
     * @author demo
     */
    public function index() {
        $pageName = '系统参数配置';
        $edit = $this->getModel('System')->selectData(
            '*',
            '1=1',
            'SysID ASC'); //获取系统参数数据集
        foreach($edit as $i=>$iEdit){
            $list[$iEdit['TagName']][]=$iEdit;
        }
        /*载入模板标签*/
        $this->assign('edit', $list); //系统参数数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 更新系统参数
     * @author demo
     */
    public function save() {
        unset($_POST['__hash__']);
        $msg=$_POST;
        $newArr=array();
        foreach($msg as $i=>$iMsg){
            $strArr=explode('_',$i);
            $newArr[$strArr[0]][$strArr[2]][]=$iMsg;
        }
           $system=$this->getModel('System');
        foreach($newArr as $i=>$iNewArr){
            $data['TagName']=$i;
            foreach($newArr[$i] as $j=>$jNewArr){
                if($jNewArr[0]==''){ //判断是否有原始数据（当前为无，执行插入）
                    $data['FieldName']=$jNewArr[1];
                    $data['Content']=$jNewArr[2];
                    $data['Description']=$jNewArr[3];
                    if(!empty($data['FieldName']) && !empty($data['Content'])){
                        $result=$system->insertData(
                            $data
                        );
                        if(!$result){
                            $error[]=1;
                        }
                    }
                }else{ //执行更新
                    $data['FieldName']=$jNewArr[1];
                    $data['Content']=$jNewArr[2];
                    $data['Description']=$jNewArr[3];
                    if(!empty($data['FieldName']) && !empty($data['Content'])){
                        $result=$system->updateData(
                            $data,
                            'SysID ='.$jNewArr[0]
                        );
                        if(!$result){
                            $error[]=1;
                        }
                    }
                }
            }
        }
                $this->adminLog($this->moduleName,'修改系统参数');
                $this->showSuccess('更新成功',__URL__);

    }
    /**
     * ajax删除分配功能
     * @author demo
     */
    public function delete(){
        $sysID=$_POST['SysID'];
        $result=$this->getModel('System')->deleteData(
            'SysID in ('.$sysID.')'
        );
        $msg='';
        if($result){
            $msg['msg']='ok';
        }
        $this->setBack($msg);
    }
    /**
     * 更新文档属性缓存
     * @author demo
     */
    public function updateCache(){
        $data=array();
        $arr = scandir(RUNTIME_PATH.'Data/Index',1);
        foreach ($arr as $iArr) {
            $str = explode('.', $iArr);
            F('Index/'.$str[0],NULL);
        }
        $sys = $this->getModel('System');
        $sys->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('更新成功',__URL__);
    }
    /**
     * 压缩js代码
     * @author demo
     */
    public function pickJs(){
        import("Common.Tool.JavaScriptPacker");
        $t1 = microtime(true);
        $source_dir = realpath('./').__PUBLIC__."/default/js";

        $d = dir( $source_dir );
        //echo "Handle: " . $d->handle . "/n";
        //echo "Path: " . $d->path . "/n";
        while (false !== ($entry = $d->read())) {
            if($entry=='.') continue;
            if($entry=='..') continue;
            if(strstr($entry,'min.js')) continue;
            echo $entry."/n";
            $script = file_get_contents($source_dir. '/'. $entry);
            $packed = \JSMin::minify($script);;
            file_put_contents($source_dir. '/'. str_replace('.js','.min.js',$entry), $packed);
        }
        $d->close();
        $t2 = microtime(true);
        $time = sprintf('%.4f', ($t2 - $t1) );
        echo 'script in ', $time, ' s.', "/n";
        $this->showSuccess('压缩成功',__URL__);
        die;
    }
}