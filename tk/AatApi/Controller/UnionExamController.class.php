<?php
/**
 * @author demo
 * @date 2017年3月7日
 */
/**
 * 联考接口类
 */
namespace AatApi\Controller;
class UnionExamController extends BaseController
{
    public $typeID=1; //考试类型

    public function _initialize() {
    }

    /**
     * 描述：联考手机端展示信息
     * @return [
     *      'desc'=>'html代码描述',
     *      'btn'=>[
     *          [类型1连接url地址2提示信息，按钮名称，url地址或提示信息]
     *          [类型1连接url地址2提示信息，按钮名称，url地址或提示信息]
     *          [类型1连接url地址2提示信息，按钮名称，url地址或提示信息]
     *      ]
     * ]
     * @author demo
     */
    public function showInfo() {
        $result=[
            'desc'=>'全国分区域在线联赛，考前与学霸最后一次近距离PK。激发备考冲刺无限动力，引爆考场超常发挥潜能！',
            'btn'=>[
                [1,'联赛报名','https://weidian.com/item.html?itemID=2047095663'],
                [2,'试卷答案','http://t.cn/R6myu9z 现已开启下载，请在电脑端输入以上网址下载  请注意大小写字母'],
                [1,'上传答题卡','http://www.tk.com/Index/Special/'],
                [1,'榜单','http://www.tk.com/Index/Special/'],
            ]
            ];
        $this->setBack($result);
    }

    /**
     * 描述：验证用户是否已经绑定过
     * @return ['success']
     * @author demo
     */
    public function checkIfHave() {$this->setBack('success');
        $result=$this->checkIfHaveBack();
        if($result[0]==0){
            $this->setError($result[1], 1);
        }else{
            $this->setBack('success');
        }
    }
    /**
     * 描述：验证用户是否已经绑定过
     * @return ['success']
     * @author demo
     */
    private function checkIfHaveBack() {
        return [1,$buffer];
        $userID=$this->getUserID();
        $examBuy=$this->getModel('ExamBuy');
        $buffer=$examBuy->findData('*','UserID="'.$userID.'" and PaperType='.$this->typeID);
        if(empty($buffer)){
            return [0,'用户不存在，请先绑定。'];
        }
        if($buffer['Status']===1){
            return [0,'用户已锁定，请联系管理员。'];
        }

        return [1,$buffer];
    }

    /**
     * 描述：获取手机验证码
     * @param string $phonecode 手机号
     * @param string $verify 验证码
     * @param string $verifyToken 验证码Token
     * @return ['success']
     * @author demo
     */
    public function getPhoneVerify() {
        $phonecode=$_REQUEST['phonecode'];
        $verify = $_POST['verify'];
        $verifyToken = $_POST['verifyToken'];

        //判断验证码是否正确 只有APP端的验证了
        if ((md5(date('YmdH', time()) . C('APP_KEY') . $verify) != $verifyToken) &&
            (md5(date('YmdH', time() + 3600) . C('APP_KEY') . $verify) != $verifyToken)
        ) {
            // $this->ajaxReturn(null, '验证码错误或超时！', 0);
            $this->setError( '验证码错误或超时！', 1);
        }

        $examBuy=$this->getModel('ExamBuy');
        $buffer=$examBuy->findData('*','Phonecode="'.$phonecode.'" and PaperType='.$this->typeID);
        if($buffer===false){
            $this->setError('手机号不存在，请先报名。', 1);
        }
        if($buffer['Status']===1){
            $this->setError('手机号已锁定，请联系管理员。', 1);
        }

        $result=R('Common/UserLayer/sendPhoneCodeOnly',array($phonecode,0,));

        if($result[0]==1) $this->setError( $result[1], 1);
        $this->setBack('success');
    }

    /**
     * 描述：验证联考手机号
     * @param string $phonecode 手机号
     * @param string $code 验证码
     * @return ['success']
     * @author demo
     */
    public function checkExamPhone() {
        $phonecode=$_REQUEST['phonecode'];
        $code=$_REQUEST['code'];
        $pregTel = checkString('checkIfPhone',$phonecode);
        if($pregTel==false){
            $this->setError('30211', 1);
        }
        //验证手机验证码是否正确
        $output=R('Common/UserLayer/checkPhoneCode',array($phonecode,$code,0,1));

        if($output[0] == 1){
            $this->setError($output[1], 1);
        }

        $examBuy=$this->getModel('ExamBuy');
        $buffer=$examBuy->findData('*','Phonecode="'.$phonecode.'" and PaperType='.$this->typeID);
        if($buffer===false){
            $this->setError('用户不存在，请先报名。', 1);
        }
        if(!empty($buffer['UserID'])){
            $this->setError('该手机号已绑定，请更换。', 1);
        }

        $userID=$this->getUserID();
        //绑定用户数据
        $data=array(
            'UserID'=>$userID,
            'CheckTime'=>time()
        );
        $result=$examBuy->updateData($data,'BuyID='.$buffer['BuyID']);
        if($result===false)  $this->setError('验证失败，请重试。', 1);
        $this->setBack('success');
    }

    /**
     * 描述：获取下载列表
     * @return [
     *      0=>[
     *              'PaperID'=>'1',
     *              'PaperName'=>'语文',
     *              'Http'=>'http://www.tk.com/a.pdf',
     *              'IfWL'=>'文科',
     *          ],
     *      1=>[
     *              'PaperID'=>'2',
     *              'PaperName'=>'语文',
     *              'IfWL'=>'文科',
     *          ]
     * ]
     * @author demo
     */
    public function getPaperList() {
        $result=$this->checkIfHaveBack();
        if($result[0]==0) $this->setError($result[1], 1);

        $examPaper=$this->getModel('ExamPaper');
        $whereIfWL='';
        if($result[1]['IfWL']>0) $whereIfWL=' and IfWL in (0,'.$result[1]['IfWL'].')';
        $buffer=$examPaper->selectData('*','PaperType='.$this->typeID.' and Status=0 '.$whereIfWL,'OrderID ASC,PaperID ASC');

        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $buffer[$i]['IfWL']=$result[1]['IfWL']==1?'文科':'理科';
                if(empty($result[1]['IfWL'])) $buffer[$i]['IfWL']='通用';
            }
        }

        $this->setBack($buffer);
    }

    /**
     * 描述：下载试卷
     * @params int $paperID 试卷id
     * @return pdf下载
     * @author demo
     */
    public function downPaper() {
        $paperID=$_REQUEST['paperID'];

        $result=$this->checkIfHaveBack();
        if($result[0]==0) $this->setError($result[1], 1);

        $examPaper=$this->getModel('ExamPaper');
        $buffer=$examPaper->findData('*','PaperID='.$paperID.' and Status=0 and PaperType='.$this->typeID);
        if($buffer===false){
            $this->setError('试卷不存在，请刷新重试。', 1);
        }

        if(empty($buffer['Http']) || !strstr($buffer['Http'],'/Uploads')){
            $this->setError('30706', 1); //该文件不存在
        }
        //$url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($buffer['Http'],'down','bbs',$buffer['PaperName']));
        $url=C('WLN_DOC_HOST').$buffer['Http'];
        $this->setBack($url);exit();
            $strHtml=file_get_contents($url);
            // Begin writing headers
            header ( "Pragma: public" );
            header ( "Expires: 0" );
            header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
            header ( "Cache-Control: public" );
            header ( "Content-Description: File Transfer" );
            // Use the switch-generated Content-Type
            if(strstr($buffer['Http'],'.mp3')){
                header ( "Content-Type: audio/mp3" );
                $header = "Content-Disposition: attachment; filename=" . $buffer['PaperName'] . ".mp3;";
            }
            if(strstr($buffer['Http'],'.pdf')){
                header ( "Content-Type: application/pdf" );
                // Force the download
                $header = "Content-Disposition: attachment; filename=" . $buffer['PaperName'] . ".pdf;";
            }

            header ( $header );
            header ( "Content-Transfer-Encoding: binary" );
            header ( "Content-Length: " . strlen($strHtml) );

            echo $strHtml;
    }
}