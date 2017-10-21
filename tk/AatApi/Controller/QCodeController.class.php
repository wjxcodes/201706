<?php
/**
 * @author demo
 * @date 2017年3月7日
 */
/**
 * 二维码操作接口类
 */
namespace AatApi\Controller;
class QCodeController extends BaseController
{

    public function _initialize() {
    }

    /**
     * 描述：记录专题模块训练二维码使用  http://www.tk.com/Aat/App/index.html?action=3&subID=12&type=1&klID=251中的参数数据
     * @param int phone AndroidPhone 1 AndroidPad 2 IPhone 3 IPad 4
     * @param int action 解析方式 3
     * @param int klid 知识点id
     * @param int type 类型 1
     * @return [1,'success']
     * @author demo
     */
    public function tjKl() {
        $request=$_REQUEST;
        $phoneStyle=$request['phone'];
        $action=$request['action'];
        $klid=$request['klid'];
        $type=$request['type'];
        $userID=$this->getUserID();
        $subjectID=SS('knowledge')[$klid]['SubjectID'];
        $data=[
            'UserID'=>$userID,
            'SubjectID'=>$subjectID,
            'KlID'=>$klid,
            'Type'=>$type,
            'Action'=>$action,
            'PhoneStyle'=>$phoneStyle,
            'AddTime'=>time()
        ];
        $this->getModel('EcodeTj')->insertData($data);

        $this->setBack('success');
    }

}