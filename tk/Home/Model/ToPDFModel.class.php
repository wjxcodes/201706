<?php

namespace Home\Model;
require_once(COMMON_PATH.'Tool/fpdf181/cn.php');
/**
 * 输出PDF文件类
 *
 * @author demo
 * @date 2016年11月15日
 */

/**
 * PDF处理类
 *
 * @author demo
 * @date 2016年11月15日
 */
class PDF extends \PDF_Chinese {

    //图片路径
    const IMG_URL='../www/Public/default/image/answer/';
    //a4纸张宽度 像素
    const WIDTH_A4=794;
    //a4纸张宽度 像素
    const WIDTH_A3=1587;
    //纸张高度 像素
    const HIGH=1123;
    //标识的左右边距 像素
    const MARK_LEFT=25;
    //标识的上下边距 像素
    const MARK_TOP=35;
    //内容左右页边距
    const LEFT=45;
    //内容上下页边距
    const TOP=57;
    //A3分多少列
    const COLUMN_A3=3;
    //主观题行高
    const ROW=45;

//页头
    function Header(){
        //画布背景色
        $this->setFillColor(255,255,255);
        $this->rect(0,0,2000,1000,'F');
//         Logo
        $this->SetFont('GB-hw','',12*0.75);
        $this->SetTextColor(228,3,127);
        $this->Cell(0);
        //A3
        if($this->GetX()>1000*0.75){
            $pageWidth=  self::WIDTH_A3;//页宽
            //正反标识
            $this->SetDrawColor(0,0,0);
            $this->SetFillColor(0,0,0);
            $this->Rect(self::WIDTH_A3/3*0.75,self::MARK_TOP*0.75,20*0.75,10*0.75,'F');
            $this->Rect(self::WIDTH_A3/3*0.75,(self::HIGH-self::MARK_TOP-10)*0.75,20*0.75,10*0.75,'F');
//            $this->Image('../www/Public/static/images/top_l.png',25*0.75,35*0.75,54.25*0.75,20*0.75);
//            $this->Image('../www/Public/static/images/top_r.png',(1587-25-54.25)*0.75,35*0.75,20*0.75,54.25*0.75);
            //第一页
            if($this->PageNo()==1){
                for($i=1;$i<self::COLUMN_A3;$i++){
                    $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*$i)*0.75);
                    //红框
                    $this->getRedBorder((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3);
                    //提示
                    $this->Cell((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*0.75,16*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'),0,0,'C');
                }
//                $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3)*0.75);
//                $this->getRedBorder((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3);
//                $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3)*0.75);
//                $this->getRedBorder((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3);
//                $this->Text(230*0.75,(57+12)*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'));
//                $this->Ln(18*0.75);
            }
            else{
                for($i=0;$i<self::COLUMN_A3;$i++){
                    $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*$i)*0.75);
                    $this->getRedBorder((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3);
                    $this->Cell((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*0.75,16*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'),0,0,'C');
                }
                $this->Ln();
            }

        }
        //A4
        else{
            $pageWidth=  self::WIDTH_A4;//页宽
//            $this->SetFont('GB-hw','',12*0.75);
            $this->SetTextColor(228,3,127);
            $this->Ln(0);
//            $this->Image('../www/Public/static/images/top.png',25*0.75,35*0.75,742*0.75,20*0.75);
            if($this->PageNo()>1){
                $this->getRedBorder();
                $this->Cell((self::WIDTH_A4-self::LEFT*2)*0.75,16*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'),0,1,'C');
//                $this->Ln(19*0.75);
            }
        }
        //头部标识
        $this->Image(self::IMG_URL.'top_l.png',self::MARK_LEFT*0.75,self::MARK_TOP*0.75,54.25*0.75,20*0.75);
        $this->Image(self::IMG_URL.'top_r.png',($pageWidth-self::MARK_LEFT-19)*0.75,self::MARK_TOP*0.75,20*0.75,54.25*0.75);
        //底部标识
        $this->Image(self::IMG_URL.'bottom_l.png',self::MARK_LEFT*0.75,(self::HIGH-self::MARK_TOP-20)*0.75,54.25*0.75,20*0.75);
        $this->Image(self::IMG_URL.'bottom_r.png',($pageWidth-self::MARK_LEFT-19)*0.75,(self::HIGH-self::MARK_TOP-54.25)*0.75,20*0.75,54.25*0.75);
        //页码标识
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $startX=(self::MARK_LEFT+54.25+10)*0.75;//左标识开始X
        $topStartY=(self::MARK_TOP+54.25+10)*0.75;//右上标识开始Y
        $bottomStartY=(self::HIGH-self::MARK_TOP-54.25-30)*0.75;//右下标识开始Y
        for($i=1;$i<$this->PageNo();$i++){
            $this->Rect($startX,self::MARK_TOP*0.75,20*0.75,10*0.75,'F');//左上角
            $this->Rect($startX,(self::HIGH-self::MARK_TOP-10)*0.75,20*0.75,10*0.75,'F');//左下角
            $this->Rect(($pageWidth-self::MARK_LEFT-9)*0.75,$topStartY,10*0.75,20*0.75,'F');//右上角
            $this->Rect(($pageWidth-self::MARK_LEFT-9)*0.75,$bottomStartY,10*0.75,20*0.75,'F');//右下角
            $startX+=30*0.75;
            $topStartY+=30*0.75;
            $bottomStartY-=30*0.75;
        }


//        $this->Ln(16*0.75);
    }

    //页尾
    function Footer(){
        $this->Ln(0);
        $this->Cell(0);
        $this->SetFontSize(12*0.75);
        $this->SetTextColor(228,3,127);
        //A3
        if($this->GetX()>1000*0.75){

//            $this->Image('../www/Public/static/images/bottom_l.png',25*0.75,(self::HIGH-35-20)*0.75,54.25*0.75,20*0.75);
//            $this->Image('../www/Public/static/images/bottom_r.png',(1587-25-54.25)*0.75,(self::HIGH-35-20)*0.75,54.25*0.75,20*0.75);
            for($i=0;$i<self::COLUMN_A3;$i++){
                $this->SetY((self::HIGH-self::TOP-16)*0.75);
                $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*$i)*0.75);
                $this->Cell((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*0.75,16*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'),0,0,'C');
//                $this->SetY(-16);
//                $this->setX((self::LEFT+(self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*$i)*0.75);
//                $pageNum='{nb}';
//                $this->Cell((self::WIDTH_A3-self::LEFT*2)/self::COLUMN_A3*0.75,16*0.75,iconv('utf-8','gbk','第'.(($this->PageNo()-1)*3+$i+1).'页/共'.($pageNum*3).'页'),0,0,'C');
            }
        }
        //A4
        else{
//            $this->Image('../www/Public/static/images/bottom.png',25*0.75,(self::HIGH-35-20)*0.75,742*0.75,20*0.75);
            $this->SetY((self::HIGH-self::TOP-16)*0.75);
            $this->setX(self::LEFT*0.75);
            $this->Cell((self::WIDTH_A4-self::LEFT*2)*0.75,16*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'),0,0,'C');
            $this->SetY(-15);
//            $this->SetFontSize(12*0.75);
//            $this->SetTextColor(228,3,127);
            $this->Cell(0,0,iconv('utf-8','gbk','第'.$this->PageNo().'页/共{nb}页'),0,0,'C');
//            $this->SetFontSize(12*0.75);
//            $this->SetTextColor(228,3,127);
//            $this->Text(230*0.75,(self::HIGH-55-12*0.75)*0.75,iconv('utf-8','gbk','请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效'));
        }
    }

    /**
     * 获取页面红色圆角边框
     * @param int $x 边框宽度 默认0，为A4宽度
     */
    public function getRedBorder($x=0){
        $this->SetDrawColor(228,3,127);
        //作答区域红色上边框
        $width=(self::WIDTH_A4-self::LEFT*2-14)*0.75;//A4线长度
        $startX=$this->getX();
        //A3
        if($x){
            $width=($x-10-14)*0.75;//A3
            $startX=$this->getX()+5*0.75;
        }
        //上边框
        $this->Image(self::IMG_URL.'tl.png',$startX,$this->getY(),7*0.75,7*0.75);
        $this->Line($startX+7*0.75,$this->getY(),$startX+7*0.75+$width,$this->getY());
        $this->Image(self::IMG_URL.'tr.png',$startX+7*0.75+$width,$this->getY(),7*0.75,7*0.75);
        //作答区域左右竖线
        $this->Line($startX,$this->getY()+7*0.75,$startX,(self::HIGH-self::TOP)*0.75-7*0.75);
        $this->Line($startX+$width+14*0.75,$this->getY()+7*0.75,$startX+$width+14*0.75,(self::HIGH-self::TOP)*0.75-7*0.75);
        //下边框
        $this->Image(self::IMG_URL.'bl.png',$startX,(self::HIGH-self::TOP)*0.75-7*0.75,7*0.75,7*0.75);
        $this->Line($startX+7*0.75,(self::HIGH-self::TOP)*0.75,$startX+7*0.75+$width,(self::HIGH-self::TOP)*0.75);
        $this->Image(self::IMG_URL.'br.png',$startX+7*0.75+$width,(self::HIGH-self::TOP)*0.75-7*0.75,7*0.75,7*0.75);
        //作答区域红色下边框
//        $this->Image('../www/Public/static/images/border_b.png',$x,$y+$height-7*0.75,704*0.75,7*0.75);
    }
//    public function getRedBorder($x,$y,$height){
//        //作答区域红色上边框
//        $this->Image('../www/Public/static/images/border_t.png',$x,$y,704*0.75,7*0.75);
//        //作答区域左右竖线
//        $this->SetDrawColor(228,3,127);
//        $this->Line($x,$y+7*0.75,$x,$y+($height-7)*0.75);
//        $this->Line($x+704*0.75,$y+7*0.75,$x+704*0.75,$y+($height-7)*0.75);
//        //作答区域红色下边框
//        $this->Image('../www/Public/static/images/border_b.png',$x,$y+($height-7)*0.75,704*0.75,7*0.75);
//    }
}
class ToPDFModel extends BaseModel {

    //客观题类型
    protected $chooseType=[1=>['A','B','C','D','E','F','G'],2=>['a','b','c','d','e','f','g'],3=>['√','×']];

    /**
     * 输出pdf
     * @param str $json
     * @author demo
     */
    function index($json)
    {
        //创建初始pdf
        $pdf=$this->getlayout($json['layout']['style']);
        //内容页边距
        $pdf->SetMargins($pdf::LEFT*0.75,$pdf::TOP*0.75);
        //别名
        $pdf->AliasNbPages();
        //加入字体
        $pdf->AddGBhwFont();
        //创建页
        $pdf->AddPage();
        //是否分栏,默认A4不分栏
        $x=0;//A4不分栏
        //A3一栏宽度 像素
        if($json['layout']['style']=='A3'){
            $x=($pdf::WIDTH_A3-$pdf::LEFT*2)/$pdf::COLUMN_A3;
        }
        //顶部,标题,副标题信息
        $this->getTop($pdf,$x,$json['top'],$json['title'],$json['sub']);
        //间隔
        $pdf->Ln(10*0.75);
        //顶部类型
        $this->getType($pdf,$x,$json['type']['display'],$json['code'],$json['num'],$json['care'],$json['miss']);
        //第一页红色答题圆角边框
        $this->getRedBorder($pdf,$x);
        //试题内容
        $this->getPaper($pdf,$x,$json['score']['display'],$json['paper']);
        //输出文件
        return $pdf->Output('S');
    }

    /**
     * 获取答题卡试题坐标信息
     * @param str $json
     * @author demo
     */
    public function getCoordinate($json,$pixel=96)
    {
        //分辨率
        if($pixel<=0) $pixel=96;
        $pixel=$pixel/96;
        $coordinate=[];//坐标信息
        //创建初始pdf
        $pdf=$this->getlayout($json['layout']['style']);
        //内容页边距
        $pdf->SetMargins($pdf::LEFT*0.75,$pdf::TOP*0.75);
        //别名
        $pdf->AliasNbPages();
        //加入字体
        $pdf->AddGBhwFont();
        //创建页
        $pdf->AddPage();
        //是否分栏,默认A4不分栏
        $x=0;//A4不分栏
        $pageWidth=$pdf::WIDTH_A4;
        //A3一栏宽度 像素
        if($json['layout']['style']=='A3'){
            $x=($pdf::WIDTH_A3-$pdf::LEFT*2)/$pdf::COLUMN_A3;
            $pageWidth=$pdf::WIDTH_A3;
        }
        //顶部,标题,副标题信息
        $this->getTop($pdf,$x,$json['top'],$json['title'],$json['sub']);
        //间隔
        $pdf->Ln(10*0.75);
        //顶部类型
        $typeCoor=$this->getType($pdf,$x,$json['type']['display'],$json['code'],$json['num'],$json['care'],$json['miss'],$pixel);
        //第一页红色答题圆角边框
        $this->getRedBorder($pdf,$x);
        //试题内容
        $paperCoor=$this->getPaper($pdf,$x,$json['score']['display'],$json['paper'],$pixel);
        //每个页面标识坐标信息
        $coordinate[]=[
            'OrderID'=>0,
            'Style'=>4,//0考号 1缺考 2客观题 3主观题 4页面标示
            'TestOrderStart'=>0,
            'TestOrderEnd'=>0,
            'TestSmallID'=>0,
            'IfChooseTest'=>0,
        ];
        for($i=1;$i<=$pdf->pageNo();$i++){
            //左上角
            $coordinate[0]['Coordinate'][0][]=[
                'x'=>round($pdf::MARK_LEFT*$pixel).','.round($pdf::MARK_TOP*$pixel),
                'y'=>round(($pdf::MARK_LEFT+54+($i-1)*30)*$pixel).','.round(($pdf::MARK_TOP+20)*$pixel),
                'sheet'=>$i
            ];
            //右上角
            $coordinate[0]['Coordinate'][0][]=[
                'x'=>round(($pageWidth-$pdf::MARK_LEFT-19)*$pixel).','.round($pdf::MARK_TOP*$pixel),
                'y'=>round(($pageWidth-$pdf::MARK_LEFT+1)*$pixel).','.round(($pdf::MARK_TOP+54+($i-1)*30)*$pixel),
                'sheet'=>$i
            ];
            //右下角
            $coordinate[0]['Coordinate'][0][]=[
               'x'=>round(($pageWidth-$pdf::MARK_LEFT-19)*$pixel).','.round(($pdf::HIGH-$pdf::MARK_TOP-54-($i-1)*30)*$pixel),
               'y'=>round(($pageWidth-$pdf::MARK_LEFT+1)*$pixel).','.round(($pdf::HIGH-$pdf::MARK_TOP)*$pixel),
               'sheet'=>$i
            ];
            //左下角
            $coordinate[0]['Coordinate'][0][]=[
                'x'=>round($pdf::MARK_LEFT*$pixel).','.round(($pdf::HIGH-$pdf::MARK_TOP-20)*$pixel),
                'y'=>round(($pdf::MARK_LEFT+54+($i-1)*30)*$pixel).','.round(($pdf::HIGH-$pdf::MARK_TOP)*$pixel),
                'sheet'=>$i
            ];
        }
        //考号，条形码坐标信息
        $coordinate[]=$typeCoor['code'];
        //缺考标记坐标信息
        $coordinate[]=$typeCoor['miss'];
        //试题坐标信息
        foreach($paperCoor as $iPaperCoor){
            $coordinate[]=$iPaperCoor;
        }
        return $coordinate;
    }
    /**
     * 获取试卷信息
     * @param object $pdf 绘画对象
     * @param int $x 一栏宽度
     * @param arr $paper 试卷信息
     * @author demo
     */
    public function getPaper($pdf,$x,$score,$paper,$pixel){
        $row=$pdf::ROW;//行高
        $columnX=0;//列开始X
        $column=0;//开始列数
        //坐标信息orderID开始数字
        $orderIDStart=3;
        //坐标信息数组
        $coordinate=[];
        //A3
        if($x){
            $column=1;//列
        }
        //A4
        else{
            $x=$pdf::WIDTH_A4-$pdf::LEFT*2;//列宽
        }
        foreach($paper as $iPaper){
            if($column) $columnX=($column-1)*$x*0.75;//A3列开始X
            $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
            $textStartX=$lineStartX+10*0.75;//文字开始X
            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
            //换页
            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                //完成之前黑框
//                $pdf->SetDrawColor(0,0,0);
//                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                //禁填区
//                $pdf->line($lineStartX,$lineStartY,$lineStartX+($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75);
//                $pdf->line($lineStartX,($pdf::HIGH-$pdf::TOP-16)*0.75,$lineStartX+($x-20)*0.75,$lineStartY);
                //A3
                if($column){
                    //需要换页
                    if($column==3){
                        $column=1;
                        $pdf->AddPage();
                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                        $textStartX=$lineStartX+10*0.75;//文字开始X
                    }
                    //换列
                    else{
                        $column++;
                        $pdf->SetY(($pdf::TOP+16)*0.75);
                        $lineStartX+=$x*0.75;//黑框开始X
                        $textStartX=$lineStartX+10*0.75;//文字开始X
                    }
                }
                //A4
                else{
                    $pdf->AddPage();
                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                        $pdf->setX($textStartX);
                }
            }
            //当前卷的标题，描述
            $pdf->SetFontSize(15*0.75);
            $pdf->SetTextColor(228,3,127);
            $pdf->SetX($lineStartX-10*0.75);
            $pdf->MultiCell($x*0.75,30*0.75,iconv('utf-8','gbk',$iPaper['title'].$iPaper['desc']),0,'C');
//            $pdf->text($pdf->getX(),$pdf->getY(),$pdf->getY());
//            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//            $pdf->text($pdf->getX(),$pdf->getY(),$pdf->getY());
            //题型
            foreach($iPaper['list'] as $iList){
//                $columnX=0;
                if($column) $columnX=($column-1)*$x*0.75;//列宽度
                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                $textStartX=$lineStartX+10*0.75;//文字开始X
                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                $pdf->text($pdf->getX(),$pdf->getY(),$lineStartY);
//                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                //是否需要换页
                if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                    //完成之前黑框
//                    $pdf->SetDrawColor(0,0,0);
//                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                    //禁填区
//                    $pdf->SetFontSize(15*0.75);
//                    $pdf->SetTextColor(0,0,0);
//                    $pdf->Text($lineStartX+);
//                    $pdf->line($lineStartX,$lineStartY,$lineStartX+($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75);
//                    $pdf->line($lineStartX,($pdf::HIGH-$pdf::TOP-16)*0.75,$lineStartX+($x-20)*0.75,$lineStartY);
                    //A3
                    if($column){
                        //需要换页
                        if($column==3){
                            $column=1;
                            $pdf->AddPage();
                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                            $textStartX=$lineStartX+10*0.75;//文字开始X
                            $chooseStartX=$textStartX;
                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                            $pdf->setX($textStartX);
                            $imgStartY=$pdf->getY();//图片开始Y
                        }
                        //换列
                        else{
                            $column++;
                            $pdf->SetY(($pdf::TOP+16)*0.75);
                            $columnX=$column*$x*0.75;//列开始X
                            $lineStartX+=$x*0.75;//黑框开始X
                            $textStartX=$lineStartX+10*0.75;//文字开始X
                            $chooseStartX=$textStartX;
                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                            $pdf->setX($textStartX);
                            $imgStartY=$pdf->getY();//图片开始Y
                        }
                    }
                    //A4
                    else{
                        $pdf->AddPage();
                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                        $textStartX=$lineStartX+10*0.75;//文字开始X
                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                        $pdf->setX($textStartX);
                        $imgStartY=$pdf->getY();//图片开始Y
                    }
                }
                //是否显示标题
                if($iList['display']){
                    $pdf->ln(10*0.75);
                    $pdf->setX($textStartX);
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->MultiCell($x*0.75-40*0.75,14*0.75,iconv('utf-8','gbk',$iList['title']));
                    $pdf->setX($textStartX);
                }
                //客观题
                if($iList['type']==0){
                    $pdf->ln(10*0.75);
                    $chooseStartY=$pdf->getY();//块开始Y
                    $pdf->SetFontSize(12*0.75);
                    //5题一块
                    $partNum=ceil(count($iList['content'])/5);
                    //横排
                    if($iList['style']==0){
                        if($column){
                            $chooseStartX=$textStartX+20*0.75;//A3块开始X
                        }else{
                            $chooseStartX=$textStartX+30*0.75;//A4块开始X
                        }
                        for($i=0;$i<$partNum;$i++){
                            //获取5题中最长的字符串长度
                            $width=0;
                            for($j=0;$j<5;$j++){
                                //如果有此题
                                if($iList['content'][$i*5+$j]['order']){
                                    //选项数量
                                    $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                    $iWidth=($optionNum*26+22)*0.75;
                                    if($width<$iWidth){
                                        $width=$iWidth;
                                    }
                                }
                            }
                            //每块试题数量和选项数量
                            $yn=$xn=0;
                            //不需要换段落的块
                            if($chooseStartX+$width+30*0.75<=$lineStartX+($x-20)*0.75){
                                for($j=0;$j<5;$j++){
                                    $pdf->setX($chooseStartX);
                                    //题号
                                    $order=$iList['content'][$i*5+$j]['order'];
                                    if($iList['content'][$i*5+$j]['small']) $order.='.'.$iList['content'][$i*5+$j]['small'];
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Cell(22*0.75,24*0.75,iconv('utf-8','gbk',$order),0,0,'C');
                                    //开始试题
                                    $testOrderStart=$iList['content'][$i*5]['order'];
                                    //结束试题
                                    if($iList['content'][$i*5+$j]['order']){
                                        $testOrderEnd=$iList['content'][$i*5+$j]['order'];
                                        //试题数量
                                        $yn++;
                                    }
                                    //第一个涂块开始坐标
                                    if($j==0){
                                        $coorStartX=$pdf->getX()/0.75;
                                        $coorStartY=$pdf->getY()/0.75;
                                    }
                                    //最多几个选项
                                    if($xn<$iList['content'][$i*5+$j]['num']){
                                        $xn=$iList['content'][$i*5+$j]['num'];
                                    }
                                    //选项数量
                                    $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                    //选项
                                    $pdf->SetTextColor(228,3,127);
                                    //如果有此题
                                    if($iList['content'][$i*5+$j]['order']){
                                        for($k=0;$k<$optionNum;$k++){
                                            $pdf->Cell(8*0.75,24*0.75,'[',0,0,'C');
                                            $pdf->Cell(10*0.75,24*0.75,iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]),0,0,'C');
                                            $pdf->Cell(8*0.75,24*0.75,']',0,0,'C');
    //                                        $pdf->Cell(25*0.75,24*0.75,'['.iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]).']',0,0,'C');
                                            if($k+1==$optionNum){
                                                $pdf->ln();
                                            }
                                        }
                                    }
                                }
                            }
                            //换段落的块
                            else{
                                if($column){
                                    $chooseStartX=$textStartX+20*0.75;//A3块开始X
                                }else{
                                    $chooseStartX=$textStartX+30*0.75;//A4块开始X
                                }
//                                $chooseStartX=$textStartX+20*0.75;
                                $pdf->setY($pdf->getY()+5*24*0.75+10*0.75);
                                //是否换页
                                if($pdf->getY()+5*24*0.75+10*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX+20*0.75;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                    }
                                }
                                //写入填涂区
                                for($j=0;$j<5;$j++){
                                    $pdf->setX($chooseStartX);
                                    //题号
                                    $order=$iList['content'][$i*5+$j]['order'];
                                    if($iList['content'][$i*5+$j]['small']) $order.='.'.$iList['content'][$i*5+$j]['small'];
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Cell(22*0.75,24*0.75,iconv('utf-8','gbk',$order),0,0,'C');
                                    //开始试题
                                    $testOrderStart=$iList['content'][$i*5]['order'];
                                    //结束试题
                                    if($iList['content'][$i*5+$j]['order']){
                                        $testOrderEnd=$iList['content'][$i*5+$j]['order'];
                                        //试题数量
                                        $yn++;
                                    }
                                    //第一个涂块开始坐标
                                    if($j==0){
                                        $coorStartX=$pdf->getX()/0.75;
                                        $coorStartY=$pdf->getY()/0.75;
                                    }
                                    //最多几个选项
                                    if($xn<$iList['content'][$i*5+$j]['num']){
                                        $xn=$iList['content'][$i*5+$j]['num'];
                                    }
                                    //选项数量
                                    $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                    //选项
                                    $pdf->SetTextColor(228,3,127);
                                    //如果有此题
                                    if($iList['content'][$i*5+$j]['order']){
                                        for($k=0;$k<$optionNum;$k++){
                                            $pdf->Cell(8*0.75,24*0.75,'[',0,0,'C');
                                            $pdf->Cell(10*0.75,24*0.75,iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]),0,0,'C');
                                            $pdf->Cell(8*0.75,24*0.75,']',0,0,'C');
    //                                        $pdf->Cell(25*0.75,24*0.75,'['.iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]).']',0,0,'C');
                                            if($k+1==$optionNum){
                                                $pdf->ln();
                                            }
                                        }
                                    }
                                }
                            }
                            //坐标信息
                            $orderNum=0;//小块数量
                            for($j=0;$j<$yn;$j++){
                                //开始试题
                                if(!$j){
                                    $testOrderStart=$iList['content'][$i*5+$j]['order'];
                                }
                                $orderNum++;
                                //此切割块试题数量,相邻试题才能在一块
                                //试题序号不相邻
                                if($iList['content'][$i*5+$j+1]['order']-$iList['content'][$i*5+$j]['order']>1){
                                    //此块坐标
                                    $coordinate[]=[
                                        'OrderID'=>$orderIDStart++,
                                        'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                        'TestOrderStart'=>$testOrderStart, //开始试题
                                        'TestOrderEnd'=>$iList['content'][$i*5+$j]['order'], //结束试题
                                        'TestSmallID'=>0, //试题包含（1）等数据
                                        'IfChooseTest'=>0, //是否是选做题
                                        'Coordinate'=>[
                                            0=>[ //a卷
                                                0=>[ // 默认客观题5题为一个块
                                                    'x'=>round($coorStartX*$pixel).','.round(($coorStartY+24*($j+1-$orderNum))*$pixel),
                                                    'y'=>round(($chooseStartX+$width)/0.75*$pixel).','.round(($coorStartY+24*($j+1))*$pixel),
                                                    'sheet'=>$pdf->PageNo(),
                                                    "sub"=>[
                                                        'x'=>round(($coorStartX+2)*$pixel).','.round(($coorStartY+24*($j+1-$orderNum)+7)*$pixel),//第一个矩形的左上
                                                        'y'=>round(($coorStartX+22)*$pixel).','.round(($coorStartY+24*($j+1-$orderNum)+7+11)*$pixel),//第一个矩形的右下
                                                        'z'=>round(6*$pixel).','.round(12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                                                        'xn'=>$xn, //水平数量
                                                        'yn'=>$orderNum, //垂直数量
                                                        'd'=>$iList['style'], //选项方向 0横 1纵
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                    $orderNum=0;
                                    $testOrderStart=$iList['content'][$i*5+$j+1]['order'];
                                    continue;
                                }
                                if(($j+1)==$yn){
                                    //此块坐标
                                    $coordinate[]=[
                                        'OrderID'=>$orderIDStart++,
                                        'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                        'TestOrderStart'=>$testOrderStart, //开始试题
                                        'TestOrderEnd'=>$iList['content'][$i*5+$j]['order'], //结束试题
                                        'TestSmallID'=>0, //试题包含（1）等数据
                                        'IfChooseTest'=>0, //是否是选做题
                                        'Coordinate'=>[
                                            0=>[ //a卷
                                                0=>[ // 默认客观题5题为一个块
                                                    'x'=>round($coorStartX*$pixel).','.round(($coorStartY+24*($j+1-$orderNum))*$pixel),
                                                    'y'=>round(($chooseStartX+$width)/0.75*$pixel).','.round(($coorStartY+24*($j+1))*$pixel),
                                                    'sheet'=>$pdf->PageNo(),
                                                    "sub"=>[
                                                        'x'=>round(($coorStartX+2)*$pixel).','.round(($coorStartY+24*($j+1-$orderNum)+7)*$pixel),//第一个矩形的左上
                                                        'y'=>round(($coorStartX+22)*$pixel).','.round(($coorStartY+24*($j+1-$orderNum)+7+11)*$pixel),//第一个矩形的右下
                                                        'z'=>round(6*$pixel).','.round(12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                                                        'xn'=>$xn, //水平数量
                                                        'yn'=>$orderNum, //垂直数量
                                                        'd'=>$iList['style'], //选项方向 0横 1纵
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                }
                            }
//                            //坐标信息
//                            $coordinate[]=[
//                                'OrderID'=>$orderIDStart++,
//                                'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
//                                'TestOrderStart'=>$testOrderStart, //开始试题
//                                'TestOrderEnd'=>$testOrderEnd, //结束试题
//                                'TestSmallID'=>0, //试题包含（1）等数据
//                                'IfChooseTest'=>0, //是否是选做题
//                                'Coordinate'=>[
//                                    0=>[ //a卷
//                                        0=>[ // 默认客观题5题为一个块
//                                            'x'=>round($coorStartX*$pixel).','.round($coorStartY*$pixel,
//                                            'y'=>($chooseStartX+$width)/0.75*$pixel).','.round(($pdf->getY()/0.75)*$pixel,
//                                            'sheet'=>$pdf->PageNo(),
//                                            "sub"=>[
//                                                'x'=>round(($coorStartX+3)*$pixel).','.round(($coorStartY+6)*$pixel,//第一个矩形的左上
//                                                'y'=>($coorStartX+23)*$pixel).','.round(($coorStartY+6+12)*$pixel,//第一个矩形的右下
//                                                'z'=>(6*$pixel)).','.round((12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
//                                                'xn'=>$xn, //水平数量
//                                                'yn'=>$yn, //垂直数量
//                                                'd'=>$iList['style'], //选项方向 0横 1纵
//                                            ]
//                                        ]
//                                    ]
//                                ]
//                            ];
                            //黑框结束Y
                            if($lineEndY<$pdf->getY())
                                {
                                    $lineEndY=$pdf->getY();
                                }
                            //A3块间距
                            if($column){
                                $chooseStartX=$chooseStartX+$width+20*0.75;
                            }
                            //A4块间距
                            else{
                                $chooseStartX=$chooseStartX+$width+30*0.75;
                            }
                            $pdf->setY($pdf->getY()-5*24*0.75);
                        }
                    }
                    //竖排
                    else{
                        if($column){
                            $chooseStartX=$textStartX+10*0.75;//A3块开始X
                        }else{
                            $chooseStartX=$textStartX+30*0.75;//A4块开始X
                        }
                        for($i=0;$i<$partNum;$i++){
                            //获取5题中最高的高度
                            $height=0;
                            for($j=0;$j<5;$j++){
                                //如果有此题
                                if($iList['content'][$i*5+$j]['order']){
                                    //选项数量
                                    $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                    $iHeight=$optionNum*24*0.75+12*0.75+10*0.75;
                                    if($height<$iHeight){
                                        $height=$iHeight;
                                    }
                                }
                            }
                            //是否换页
                            if($pdf->getY()+$height>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                }
                            }
                            //每块试题数量和选项数量
                            $xn=$yn=0;
                            //不需要换段落的块
                            if($chooseStartX+5*26*0.75<=$lineStartX+($x-20)*0.75){
                                for($j=0;$j<5;$j++){
                                    //如果有此题
                                    if($iList['content'][$i*5+$j]['order']){
                                        $pdf->setX($chooseStartX);
                                        //题号
                                        $order=$iList['content'][$i*5+$j]['order'];
                                        if($iList['content'][$i*5+$j]['small']) $order.='.'.$iList['content'][$i*5+$j]['small'];
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->Cell(26*0.75,12*0.75,iconv('utf-8','gbk',$order),0,2,'C');
                                        //开始试题
                                        $testOrderStart=$iList['content'][$i*5]['order'];
                                        //结束试题
                                        if($iList['content'][$i*5+$j]['order']){
                                            $testOrderEnd=$iList['content'][$i*5+$j]['order'];
                                            //试题数量
                                            $xn++;
                                        }
                                        //第一个涂块开始坐标
                                        if($j==0){
                                            $coorStartX=$pdf->getX()/0.75;
                                            $coorStartY=$pdf->getY()/0.75;
                                        }
                                        //最多几个选项
                                        if($yn<$iList['content'][$i*5+$j]['num']){
                                            $yn=$iList['content'][$i*5+$j]['num'];
                                        }
                                        //选项数量
                                        $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                        //选项
                                        $numStartX=$pdf->getX();
                                        $pdf->SetTextColor(228,3,127);
                                        for($k=0;$k<$optionNum;$k++){
                                            $pdf->Cell(8*0.75,24*0.75,'[',0,0,'C');
                                            $pdf->Cell(10*0.75,24*0.75,iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]),0,0,'C');
                                            $pdf->Cell(8*0.75,24*0.75,']',0,0,'C');
                                            $pdf->ln();
                                            $pdf->setX($numStartX);
    //                                        $pdf->Cell(30*0.75,20*0.75,'['.iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]).']',0,2,'C');
                                        }
                                        $pdf->setY($pdf->getY()-($optionNum*24*0.75+12*0.75));
                                        $chooseStartX+=26*0.75;
                                    }
                                }
//                                //黑框结束Y
//                                if($lineEndY<$pdf->getY()+$height)
//                                {
//                                    $lineEndY=$pdf->getY()+$height;
//                                }
//                                $chooseStartX=$chooseStartX+10*0.75;
                            }
                            else{
                                if($column){
                                    $chooseStartX=$textStartX+10*0.75;//A3块开始X
                                }else{
                                    $chooseStartX=$textStartX+30*0.75;//A4块开始X
                                }
                                $pdf->setY($lineEndY);
                                //是否换页
                                if($pdf->getY()+$height>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX+10*0.75;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                    }
                                }
                                //写入填涂区
                                for($j=0;$j<5;$j++){
                                    //如果有此题
                                    if($iList['content'][$i*5+$j]['order']){
                                        $pdf->setX($chooseStartX);
                                        //题号
                                        $order=$iList['content'][$i*5+$j]['order'];
                                        if($iList['content'][$i*5+$j]['small']) $order.='.'.$iList['content'][$i*5+$j]['small'];
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->Cell(26*0.75,12*0.75,iconv('utf-8','gbk',$order),0,2,'C');
                                        //开始试题
                                        $testOrderStart=$iList['content'][$i*5]['order'];
                                        //结束试题
                                        if($iList['content'][$i*5+$j]['order']){
                                            $testOrderEnd=$iList['content'][$i*5+$j]['order'];
                                            //试题数量
                                            $xn++;
                                        }
                                        //第一个涂块开始坐标
                                        if($j==0){
                                            $coorStartX=$pdf->getX()/0.75;
                                            $coorStartY=$pdf->getY()/0.75;
                                        }
                                        //最多几个选项
                                        if($yn<$iList['content'][$i*5+$j]['num']){
                                            $yn=$iList['content'][$i*5+$j]['num'];
                                        }
                                        //选项数量
                                        $optionNum=$this->getOptionNum($iList['content'][$i*5+$j]['style'],$iList['content'][$i*5+$j]['num']);
                                        //选项
                                        $numStartX=$pdf->getX();
                                        $pdf->SetTextColor(228,3,127);
                                        for($k=0;$k<$iList['content'][$i*5+$j]['num'];$k++){
                                            $pdf->Cell(8*0.75,24*0.75,'[',0,0,'C');
                                            $pdf->Cell(10*0.75,24*0.75,iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]),0,0,'C');
                                            $pdf->Cell(8*0.75,24*0.75,']',0,0,'C');
                                            $pdf->ln();
                                            $pdf->setX($numStartX);
    //                                        $pdf->Cell(30*0.75,20*0.75,'['.iconv('utf-8','gbk',$this->chooseType[$iList['content'][$i*5+$j]['style']][$k]).']',0,2,'C');
                                        }
                                        $pdf->setY($pdf->getY()-($optionNum*24*0.75+12*0.75));
                                        $chooseStartX+=26*0.75;
                                    }
                                }
                            }
                            //坐标信息
                            $orderNum=0;//小块数量
                            for($j=0;$j<$xn;$j++){
                                //开始试题
                                if(!$j){
                                    $testOrderStart=$iList['content'][$i*5+$j]['order'];
                                }
                                $orderNum++;
                                //此切割块试题数量,相邻试题才能在一块
                                //试题序号不相邻
                                if($iList['content'][$i*5+$j+1]['order']-$iList['content'][$i*5+$j]['order']>1){
                                    //此块坐标
                                    $coordinate[]=[
                                        'OrderID'=>$orderIDStart++,
                                        'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                        'TestOrderStart'=>$testOrderStart, //开始试题
                                        'TestOrderEnd'=>$iList['content'][$i*5+$j]['order'], //结束试题
                                        'TestSmallID'=>0, //试题包含（1）等数据
                                        'IfChooseTest'=>0, //是否是选做题
                                        'Coordinate'=>[
                                            0=>[ //a卷
                                                0=>[ // 默认客观题5题为一个块
                                                    'x'=>round(($coorStartX+26*($j+1-$orderNum))*$pixel).','.round($coorStartY*$pixel),
                                                    'y'=>round(($coorStartX+26*($j+1))*$pixel).','.round($pixel*(($pdf->getY()+$height)/0.75-12)),
                                                    'sheet'=>$pdf->PageNo(),
                                                    "sub"=>[
                                                        'x'=>round(($coorStartX+26*($j+1-$orderNum)+2)*$pixel).','.round(($coorStartY+7)*$pixel),//第一个矩形的左上
                                                        'y'=>round(($coorStartX+26*($j+1-$orderNum)+22)*$pixel).','.round(($coorStartY+7+11)*$pixel),//第一个矩形的右下
                                                        'z'=>round(6*$pixel).','.round(12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                                                        'xn'=>$orderNum, //水平数量
                                                        'yn'=>$yn, //垂直数量
                                                        'd'=>$iList['style'], //选项方向 0横 1纵
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                    $orderNum=0;
                                    $testOrderStart=$iList['content'][$i*5+$j+1]['order'];
                                    continue;
                                }
                                if(($j+1)==$xn){
                                    //此块坐标
                                    $coordinate[]=[
                                        'OrderID'=>$orderIDStart++,
                                        'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                        'TestOrderStart'=>$testOrderStart, //开始试题
                                        'TestOrderEnd'=>$iList['content'][$i*5+$j]['order'], //结束试题
                                        'TestSmallID'=>0, //试题包含（1）等数据
                                        'IfChooseTest'=>0, //是否是选做题
                                        'Coordinate'=>[
                                            0=>[ //a卷
                                                0=>[ // 默认客观题5题为一个块
                                                    'x'=>round(($coorStartX+26*($j+1-$orderNum))*$pixel).','.round($coorStartY*$pixel),
                                                    'y'=>round(($coorStartX+26*($j+1))*$pixel).','.round($pixel*(($pdf->getY()+$height)/0.75-12)),
                                                    'sheet'=>$pdf->PageNo(),
                                                    "sub"=>[
                                                        'x'=>round(($coorStartX+26*($j+1-$orderNum)+2)*$pixel).','.round(($coorStartY+7)*$pixel),//第一个矩形的左上
                                                        'y'=>round(($coorStartX+26*($j+1-$orderNum)+22)*$pixel).','.round(($coorStartY+7+11)*$pixel),//第一个矩形的右下
                                                        'z'=>round(6*$pixel).','.round(12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                                                        'xn'=>$orderNum, //水平数量
                                                        'yn'=>$yn, //垂直数量
                                                        'd'=>$iList['style'], //选项方向 0横 1纵
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ];
                                }
                            }
//                            //坐标信息
//                            $coordinate[]=[
//                                'OrderID'=>$orderIDStart++,
//                                'Style'=>2,//默认0考号 1缺考 2客观题 3主观题 4页面标示
//                                'TestOrderStart'=>$testOrderStart, //开始试题
//                                'TestOrderEnd'=>$testOrderEnd, //结束试题
//                                'TestSmallID'=>0, //试题包含（1）等数据
//                                'IfChooseTest'=>0, //是否是选做题
//                                'Coordinate'=>[
//                                    0=>[ //a卷
//                                        0=>[ // 默认客观题5题为一个块
//                                            'x'=>round($coorStartX*$pixel).','.round($coorStartY*$pixel,
//                                            'y'=>($coorStartX+26*$xn)*$pixel).','.round($pixel*(($pdf->getY()+$height)/0.75-12),
//                                            'sheet'=>$pdf->PageNo(),
//                                            "sub"=>[
//                                                'x'=>round(($coorStartX+3)*$pixel).','.round($pixel*($coorStartY+6),//第一个矩形的左上
//                                                'y'=>($coorStartX+23)*$pixel).','.round($pixel*($coorStartY+6+12),//第一个矩形的右下
//                                                'z'=>(6*$pixel)).','.round((12*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
//                                                'xn'=>$xn, //水平数量
//                                                'yn'=>$yn, //垂直数量
//                                                'd'=>$iList['style'], //选项方向 0横 1纵
//                                            ]
//                                        ]
//                                    ]
//                                ]
//                            ];
                            //黑框结束Y
                            if($lineEndY<$pdf->getY()+$height)
                            {
                                $lineEndY=$pdf->getY()+$height;
                            }
                            //A3块间距
                            if($column){
                                $chooseStartX=$chooseStartX+20*0.75;
                            }
                            //A4块间距
                            else{
                                $chooseStartX=$chooseStartX+$width+25*0.75;
                            }

                        }
                    }
                    //黑框
                    $pdf->setY($lineEndY);
                    $pdf->SetDrawColor(0,0,0);
//                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
//                    //黑框
//                    $pdf->ln(10*0.75);
//                    $pdf->SetDrawColor(0,0,0);
                    //要换页时的黑框高度
                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                    }
                    //正常情况的黑框高度
                    else{
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                    }
                }
//                //解答题
//                if($iList['type']==1){
////                    if($pdf->getX()>$textStartX) $pdf->ln();//换行
////                    $pdf->ln(10*0.75);
//                    $pdf->setX($textStartX);
//                    $orderNum=0;//试题数量
//                    for($k=0;$k<count($iList['content']);$k++){
//                        $iContent=$iList['content'][$k];
////                    foreach($iList['content'] as $k=>$iContent){
////                        //换页
////                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
////                            //完成之前黑框
////                            $pdf->SetDrawColor(0,0,0);
////                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
////                            //A3
////                            if($column){
////                                //需要换页
////                                if($column==3){
////                                    $column=1;
////                                    $pdf->AddPage();
////                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
////                                    $textStartX=$lineStartX+10*0.75;//文字开始X
////                                }
////                                //换列
////                                else{
////                                    $column++;
//////                                    $lineStartX+=$x*0.75;//黑框开始X
//////                                    $textStartX=$lineStartX+10*0.75;//文字开始X
////                                    $pdf->SetY(($pdf::TOP+16)*0.75);
////                                    $columnX=$column*$x*0.75;//列开始X
////                                    $lineStartX+=$x*0.75;//黑框开始X
////                                    $textStartX=$lineStartX+10*0.75;//文字开始X
////                                    $chooseStartX=$textStartX;
////                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
////                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
////                                    $pdf->setX($textStartX);
////                                    $imgStartY=$pdf->getY();//图片开始Y
////                                }
////                            }
////                            //A4
////                            else{
////                                $pdf->AddPage();
////                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
////                                $textStartX=$lineStartX+10*0.75;//文字开始X
////                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
////                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
////                                $pdf->setX($textStartX);
////                            }
////                        }
//                        //下划线
//                        $uline=0;
//                        if($iContent['uline']){
//                           $uline='B';
//                        }
//                        //题号宽度
//                        $order=$iContent['order'];//题号
//                        $orderWidth=15*0.75;//题号宽度
//                        //有小题
//                        if($iContent['small']){
//                            $order.='('.$iContent['small'].')';//小题号
//                            $orderWidth+=15*0.75;//题号宽度
//                        }
//                        //有分值
//                        if($score){
//                            $order.='('.$iContent['score'].'分)';//分值
//                            $orderWidth+=40*0.75;//题号宽度
//                        }
//                        //空白长度
//                        $hline=($x-40)*0.75;//整行
//                        //没描述，题号下沉
//                        if(!$iContent['desc']){
//                            //单空小于一行,排版
//                            if($iContent['hline']<1){
//                                //总空长度不够一行
//                                if($iContent['hline']*$iContent['kong']<0.9){
//                                    //目前X加总长度够不够一行
//                                    if()
//                                    $orderNum++;//目前试题数量
//                                    $kCount+=$iContent['hline']*$iContent['kong'];//空总长度
//                                    //不够一行
//                                    if($kCount<0.8){
//                                        //看后面试题空多长，直到够一行
//                                        for($i=1;$i<3;$i++){
//                                            //如果下一题有描述或空不小于1行
//                                            if(){
//                                                $iList['content'][$k+$i][]
//                                            }
//                                        }
//                                    }
//                                }
//                                //总空够一行，可以写
//                                else{
//                                    //每空长度
//                                    if($iContent['hline']==0.3){
//                                        $hline=$hline-$orderWidth;//题号宽度
//                                        $hline=$hline-20*3*0.75;//空间隔
//                                        $hline=$hline/3;
//                                    }
//                                    if($iContent['hline']==0.5){
//                                        $hline=$hline-$orderWidth;//题号宽度
//                                        $hline=$hline-20*2*0.75;//空间隔
//                                        $hline=$hline/2;
//                                    }
//                                    //换页
//                                    if($pdf->getY()+$row*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                        //完成之前黑框
//                                        $pdf->SetDrawColor(0,0,0);
//                                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                        //A3
//                                        if($column){
//                                            //需要换页
//                                            if($column==3){
//                                                $column=1;
//                                                $pdf->AddPage();
//                                                $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            }
//                                            //换列
//                                            else{
//                                                $column++;
//                                                $pdf->SetY(($pdf::TOP+16)*0.75);
//                                                $columnX=$column*$x*0.75;//列开始X
//                                                $lineStartX+=$x*0.75;//黑框开始X
//                                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                $chooseStartX=$textStartX;
//                                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                $pdf->setX($textStartX);
//                                                $imgStartY=$pdf->getY();//图片开始Y
//                                            }
//                                        }
//                                        //A4
//                                        else{
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                        }
//                                    }
//                                    //题号
//                                    $pdf->SetTextColor(0,0,0);
//                                    $pdf->SetFontSize(12*0.75);
//                                    $nowX=$pdf->getX();//到此题时的X
//                                    $pdf->setY($pdf->getY()+($row-14)*0.75);
//                                    $pdf->setX($nowX);
//                                    $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
//                                    $pdf->setY($pdf->getY()-($row-14)*0.75);
//                                    $pdf->setX($nowX+$orderWidth);
//                                    //空
//                                    for($i=0;$i<$iContent['hline'];$i++){
//                                        //换行
//                                        if($pdf->getX()+$hline>$lineStartX+($x-40)*0.75){
//                                            $pdf->ln();
//                                            $pdf->setX($textStartX+$orderWidth);
//                                        }
//                                        //换页
//                                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                            //完成之前黑框
//                                            $pdf->SetDrawColor(0,0,0);
//                                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                            //A3
//                                            if($column){
//                                                //需要换页
//                                                if($column==3){
//                                                    $column=1;
//                                                    $pdf->AddPage();
//                                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                    $chooseStartX=$textStartX;
//                                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                    $pdf->setX($textStartX);
//                                                    $imgStartY=$pdf->getY();//图片开始Y
//                                                }
//                                                //换列
//                                                else{
//                                                    $column++;
//                                                    $pdf->SetY(($pdf::TOP+16)*0.75);
//                                                    $columnX=$column*$x*0.75;//列开始X
//                                                    $lineStartX+=$x*0.75;//黑框开始X
//                                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                    $chooseStartX=$textStartX;
//                                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                    $pdf->setX($textStartX);
//                                                    $imgStartY=$pdf->getY();//图片开始Y
//                                                }
//                                            }
//                                            //A4
//                                            else{
//                                                $pdf->AddPage();
//                                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                $pdf->setX($textStartX);
//                                                $imgStartY=$pdf->getY();//图片开始Y
//                                            }
//                                        }
//                                        $hwidth=$hline;
//                                        if($i==0){
//                                            $hwidth=$hline-$orderWidth;
//                                        }
//                                        else{
//                                            $pdf->setX($textStartX);
//                                        }
//                                        $pdf->Cell($hline,$row*0.75,'',$uline);
//                                        $pdf->Cell(20*0.75,$row*0.75,'');
//                                    }
//                                }
//                            }
//                            //空大于等于1行不用排版
//                            else{
//                                //换页
//                                if($pdf->getY()+$row*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                    }
//                                }
//                                //题号
//                                $pdf->SetTextColor(0,0,0);
//                                $pdf->SetFontSize(12*0.75);
//                                $nowX=$pdf->getX();//到此题时的X
//                                $pdf->setY($pdf->getY()+($row-14)*0.75);
//                                $pdf->setX($nowX);
//                                $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
//                                $pdf->setY($pdf->getY()-($row-14)*0.75);
//                                $pdf->setX($nowX+$orderWidth);
//                                //空
//                                for($i=0;$i<$iContent['hline'];$i++){
//                                    //换页
//                                    if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                        //完成之前黑框
//                                        $pdf->SetDrawColor(0,0,0);
//                                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                        //A3
//                                        if($column){
//                                            //需要换页
//                                            if($column==3){
//                                                $column=1;
//                                                $pdf->AddPage();
//                                                $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                $chooseStartX=$textStartX;
//                                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                $pdf->setX($textStartX);
//                                                $imgStartY=$pdf->getY();//图片开始Y
//                                            }
//                                            //换列
//                                            else{
//                                                $column++;
//                                                $pdf->SetY(($pdf::TOP+16)*0.75);
//                                                $columnX=$column*$x*0.75;//列开始X
//                                                $lineStartX+=$x*0.75;//黑框开始X
//                                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                                $chooseStartX=$textStartX;
//                                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                                $pdf->setX($textStartX);
//                                                $imgStartY=$pdf->getY();//图片开始Y
//                                            }
//                                        }
//                                        //A4
//                                        else{
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                    }
//                                    $hwidth=$hline;
//                                    if($i==0){
//                                        $hwidth=$hline-$orderWidth;
//                                    }
//                                    else{
//                                        $pdf->setX($textStartX);
//                                    }
//                                    $pdf->Cell($hwidth,$row*0.75,'',$uline,1);
//                                }
//                            }
//                        }
//                        //有描述
//                        else{
//                            if($iContent['hline']==0.3){
//                                $hline=$hline/3;
//                            }
//                            if($iContent['hline']==0.5){
//                                $hline=$hline/2;
//                            }
//
////                        if($iContent['hline']==0.3){
//////                            $hline=$hline-15*0.75;//题号宽
//////                            if($iContent['small']) $hline=$hline-15*0.75;//小题号宽
//////                            if($score) $hline=$hline-30*0.75;//分值宽
////                            $hline=$hline-$orderWidth;//题号宽度
////                            $hline=$hline-20*3*0.75;//空间隔
////                            $hline=$hline/3;
////                        }
////                        if($iContent['hline']==0.5){
//////                            $hline=$hline-15*0.75;//题号宽
//////                            if($iContent['small']) $hline=$hline-15*0.75;//小题号宽
//////                            if($score) $hline=$hline-30*0.75;//分值宽
////                            $hline=$hline-$orderWidth;//题号宽度
////                            $hline=$hline-20*2*0.75;//空间隔
////                            $hline=$hline/2;
////                        }
////                        //下划线
////                        $uline=0;
////                        if($iContent['uline']){
////                           $uline='B';
////                        }
////                        //换行
////                        if($pdf->getX()+$orderWidth+$hline*$iContent['kong']>$textStartX+($x-40)*0.75&&$pdf->getX()!=$textStartX){
////                            $pdf->ln();
////                            $pdf->setX($textStartX);
////                        }
//                            //换页
//                            if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                }
//                            }
//                            //题号
//                            $pdf->SetTextColor(0,0,0);
//                            $pdf->SetFontSize(12*0.75);
//                            $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,1,'L');
//                            //换页
//                            if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                }
//                            }
//                            //试题描述
//                            if($iContent['desc']){
//                                $pdf->setX($textStartX);
//                                $strWidth=$pdf->GetStringWidth($iContent['desc']);//描述宽度
//                                $strHeight=ceil($strWidth/(($x-40)*0.75))*24*0.75;//说明高度
////                                $pdf->text($pdf->getX(),$pdf->getY(),ceil($strWidth/(($x-40)*0.75)));
////                                $strLineStartY=$pdf->getY();//说明开始Y
//                                //是否需要换页
//                                if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                    }
//                                }
//                                //试题描述部分
//                                $pdf->SetFontSize(12*0.75);
//                                $pdf->setX($textStartX);
//                                $pdf->MultiCell(($x-40)*0.75,24*0.75,iconv('utf-8','gbk',$iContent['desc']));
//                                $pdf->setX($textStartX);
//                            }
//                            $imgStartY=$pdf->getY();//图片开始Y
//                            $imgNum=0;
//                            //空
//                            for($i=0;$i<$iContent['hline'];$i++){
//                                //换页
//                                if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                $hwidth=$hline;
//                                if($i==0){
//                                    if(!$iContent['desc']){
//                                        $hwidth=$hline-($pdf->getX()-$textStartX);
//                                    }
//                                }
//                                $pdf->setX($textStartX);
//                                $pdf->Cell($hwidth,$row*0.75,'',$uline,1);
//                                //是否有图片
//                                if($iContent['img'][$imgNum]){
//                                    $kongY=$pdf->getY();
//                                    $imgSize=getimagesize($iContent['img'][$imgNum][0]);
//                                    //能否放下图片
//                                    if($pdf->getY()-$imgStartY>($imgSize[1]+18)*0.75){
//                                        $pdf->SetFillColor(255,255,255);
//                                        $pdf->Rect($lineStartX+($x-20-10-$imgSize[0]-10)*0.75,$imgStartY,10*0.75,($imgSize[1]+18)*0.75,'F');
//                                        $pdf->Image($iContent['img'][$imgNum][0],$lineStartX+($x-20-10-$imgSize[0])*0.75,$imgStartY);
//                                        $pdf->setY($imgStartY+$imgSize[1]*0.75);
//                                        $pdf->setX($lineStartX+($x-20-10-$imgSize[0])*0.75);
//                                        $pdf->SetFontSize(12*0.75);
//                                        $pdf->SetTextColor(0,0,0);
//                                        $pdf->Cell($imgSize[0]*0.75+1,18*0.75,iconv('utf-8','gbk',$iContent['img'][$imgNum][1]),0,1,'C',1);
//                                        $imgStartY=$pdf->getY();
//                                        $pdf->setY($kongY);
//                                        $imgNum++;
//                                    }
//                                }
//                                $pdf->setX($textStartX);
//                            }
//                        }
//                        //填空题
//                        if($iContent['kong']){
//                            //换行
//                            if($pdf->getX()+$orderWidth+$hline*$iContent['kong']>$textStartX+($x-40)*0.75&&$pdf->getX()!=$textStartX){
//                                $pdf->ln();
//                                $pdf->setX($textStartX);
//                            }
//                            //换页
//                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                }
//                            }
////                            $order=$iContent['order'];//题号
////                            $orderWidth=15*0.75;//题号宽度
////                            //有小题
////                            if($iContent['small']){
////                                $order.='('.$iContent['small'].')';//小题号
////                                $orderWidth+=15*0.75;//题号宽度
////                            }
////                            //有分值
////                            if($score){
////                                $order.='('.$iContent['score'].'分)';//分值
////                                $orderWidth+=30*0.75;//题号宽度
////                            }
//                            $pdf->SetTextColor(0,0,0);
//                            $pdf->SetFontSize(12*0.75);
//                            $nowX=$pdf->getX();//到此题时的X
//                            $pdf->setY($pdf->getY()+($row-14)*0.75);
//                            $pdf->setX($nowX);
//                            $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
//                            $pdf->setY($pdf->getY()-($row-14)*0.75);
//                            $pdf->setX($nowX+$orderWidth);
////                            $order=$iContent['order'];
////                            if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
////                            if($score) $order.='('.$iContent['score'].'分)';
////                            $pdf->SetTextColor(0,0,0);
////                            $pdf->SetFontSize(12*0.75);
////                            $nowX=$pdf->getX();//到此题时的X
////                            $pdf->setY($pdf->getY()+($row-14)*0.75);
////                            $pdf->setX($nowX);
////                            $pdf->Cell(60*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
////                            $pdf->setY($pdf->getY()-($row-14)*0.75);
////                            $pdf->setX($nowX+60*0.75);
//                            //空
//                            for($i=0;$i<$iContent['kong'];$i++){
//                                //换行
//                                if($pdf->getX()+$hline>$lineStartX+($x-40)*0.75){
//                                    $pdf->ln();
//                                    $pdf->setX($textStartX+$orderWidth);
//                                }
//                                //换页
//                                if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $pdf->setX($textStartX+$orderWidth);
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX+$orderWidth);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX+$orderWidth);
//                                    }
//                                }
////                                if($i>0){
////                                    if($pdf->getX()!=$textStartX+60*0.75){
////                                        $pdf->setX($pdf->getX()+60*0.75);
////                                    }
////                                }
//                                $pdf->Cell($hline,$row*0.75,'',$uline);
//                                $pdf->Cell(20*0.75,$row*0.75,'');
//                            }
//                        }
//                        //大题
//                        else{
//                            if($pdf->getX()>$textStartX) $pdf->ln();//换行
//                            $pdf->ln(10*0.75);
//                            //换页
//                            if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                }
//                            }
//                            //换行
////                            if($pdf->getX()!=$textStartX){
////                                $pdf->ln();
////                            }
//                            $pdf->setX($textStartX);
//                            //题号
//                            $order=$iContent['order'];
//                            if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
//                            if($score) $order.='('.$iContent['score'].'分)';
//                            $pdf->SetTextColor(0,0,0);
//                            $pdf->SetFontSize(12*0.75);
//                            $pdf->Cell(60*0.75,24*0.75,iconv('utf-8','gbk',$order),0,1,'L');
//                            //换页
//                            if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                }
//                            }
//                            //试题描述
//                            if($iContent['desc']){
//                                $pdf->setX($textStartX);
//                                $strWidth=$pdf->GetStringWidth($iContent['desc']);//描述宽度
//                                $strHeight=ceil($strWidth/(($x-40)*0.75))*24*0.75;//说明高度
////                                $pdf->text($pdf->getX(),$pdf->getY(),ceil($strWidth/(($x-40)*0.75)));
////                                $strLineStartY=$pdf->getY();//说明开始Y
//                                //是否需要换页
//                                if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                    }
//                                }
//                                //试题描述部分
//                                $pdf->SetFontSize(12*0.75);
//                                $pdf->setX($textStartX);
//                                $pdf->MultiCell(($x-40)*0.75,24*0.75,iconv('utf-8','gbk',$iContent['desc']));
//                                $pdf->setX($textStartX);
//                            }
//                            $imgStartY=$pdf->getY();//图片开始Y
//                            $imgNum=0;
//                            //空
//                            for($i=0;$i<$iContent['hline'];$i++){
//                                //换页
//                                if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                            $pdf->setX($textStartX);
//                                            $imgStartY=$pdf->getY();//图片开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                $hwidth=$hline;
//                                if($i==0){
//                                    if(!$iContent['desc']){
//                                        $hwidth=$hline-($pdf->getX()-$textStartX);
//                                    }
//                                }
//                                $pdf->setX($textStartX);
//                                $pdf->Cell($hwidth,$row*0.75,'',$uline,1);
//                                //是否有图片
//                                if($iContent['img'][$imgNum]){
//                                    $kongY=$pdf->getY();
//                                    $imgSize=getimagesize($iContent['img'][$imgNum][0]);
//                                    //能否放下图片
//                                    if($pdf->getY()-$imgStartY>($imgSize[1]+18)*0.75){
//                                        $pdf->SetFillColor(255,255,255);
//                                        $pdf->Rect($lineStartX+($x-20-10-$imgSize[0]-10)*0.75,$imgStartY,10*0.75,($imgSize[1]+18)*0.75,'F');
//                                        $pdf->Image($iContent['img'][$imgNum][0],$lineStartX+($x-20-10-$imgSize[0])*0.75,$imgStartY);
//                                        $pdf->setY($imgStartY+$imgSize[1]*0.75);
//                                        $pdf->setX($lineStartX+($x-20-10-$imgSize[0])*0.75);
//                                        $pdf->SetFontSize(12*0.75);
//                                        $pdf->SetTextColor(0,0,0);
//                                        $pdf->Cell($imgSize[0]*0.75+1,18*0.75,iconv('utf-8','gbk',$iContent['img'][$imgNum][1]),0,1,'C',1);
//                                        $imgStartY=$pdf->getY();
//                                        $pdf->setY($kongY);
//                                        $imgNum++;
//                                    }
//                                }
//                                $pdf->setX($textStartX);
//                            }
//                        }
//                    }
//                    //黑框
//                    if($pdf->getX()>$textStartX) $pdf->ln();//换行
//                    $pdf->ln(10*0.75);
//                    $pdf->SetDrawColor(0,0,0);
//                    //要换页时的黑框高度
//                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                    }
//                    //正常情况的黑框高度
//                    else{
//                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
//                    }
////                    $pdf->ln(10*0.75);
////                    //黑框
////                    $pdf->SetDrawColor(0,0,0);
////                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
//                }
                //解答题
                if($iList['type']==1){
                    $pdf->setX($textStartX);
                    for($k=0;$k<count($iList['content']);$k++){
                        $iContent=$iList['content'][$k];
                        //下划线
                        $uline=0;
                        if($iContent['uline']){
                           $uline='B';
                        }
                        //题号宽度
                        $order=$iContent['order'].'.';//题号
                        $orderWidth=25*0.75;//题号宽度
                        //有小题
                        if($iContent['small']){
                            $order.='('.$iContent['small'].')';//小题号
                            $orderWidth+=15*0.75;//题号宽度
                        }
                        //有分值
                        if($score){
                            $order.='('.$iContent['score'].'分)';//分值
                            $orderWidth+=40*0.75;//题号宽度
                        }
                        //空白长度
                        $hline=($x-40)*0.75;//整行
                        if($iContent['hline']==0.3){
                            $hline=$hline-$orderWidth;//题号宽度
                            $hline=$hline-20*3*0.75;//空间隔
                            $hline=$hline/3;
                        }
                        if($iContent['hline']==0.5){
                            $hline=$hline-$orderWidth;//题号宽度
                            $hline=$hline-20*2*0.75;//空间隔
                            $hline=$hline/2;
                        }
                        //换行
                        if(($pdf->getX()+$orderWidth+$hline*$iContent['kong']>$textStartX+($x-40)*0.75)&&($pdf->getX()!=$textStartX)){
                            $pdf->ln();
                            $pdf->setX($textStartX);
                        }
                        //换页
                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                            //完成之前黑框
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                            //A3
                            if($column){
                                //需要换页
                                if($column==3){
                                    $column=1;
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                                //换列
                                else{
                                    $column++;
                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                    $columnX=$column*$x*0.75;//列开始X
                                    $lineStartX+=$x*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //A4
                            else{
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //同行情况
                        if($pdf->getX()==$textStartX){
//                            //换页
//                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                //完成之前黑框
//                                $pdf->SetDrawColor(0,0,0);
//                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                //A3
//                                if($column){
//                                    //需要换页
//                                    if($column==3){
//                                        $column=1;
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                    //换列
//                                    else{
//                                        $column++;
//                                        $pdf->SetY(($pdf::TOP+16)*0.75);
//                                        $columnX=$column*$x*0.75;//列开始X
//                                        $lineStartX+=$x*0.75;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $chooseStartX=$textStartX;
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                        $imgStartY=$pdf->getY();//图片开始Y
//                                    }
//                                }
//                                //A4
//                                else{
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                    $imgStartY=$pdf->getY();//图片开始Y
//                                }
//                            }
                            //此题每空宽0.3
                            if($iList['content'][$k]['hline']==0.3){
                                //两题一行
                                if(($iList['content'][$k]['hline']*$iList['content'][$k]['kong']+
                                $iList['content'][$k+1]['hline']*$iList['content'][$k+1]['kong'])*10==9){
                                    //计算题号总长度
                                    $orderWidth=25*2*0.75;//题号宽度
                                    //如果有分值
                                    if($score){
                                        $orderWidth+=40*2*0.75;//题号宽度
                                    }
                                    //是否有小题号
                                    for($j=0;$j<2;$j++){
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                    }
                                    //每空长度
                                    $hline=($x-40)*0.75;//整行
                                    $hline=$hline-$orderWidth;//题号宽度
                                    $hline=$hline-20*3*0.75;//空间隔
                                    $hline=$hline/3;
//                                    //坐标信息
//                                    $coordinate[]=[
//                                        'OrderID'=>$orderIDStart++,
//                                        'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
//                                        'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
//                                        'TestOrderEnd'=>$iList['content'][$k+1]['order'], //结束试题
//                                        'TestSmallID'=>0, //试题包含（1）等数据
//                                        'IfChooseTest'=>0, //是否是选做题
//                                        'Coordinate'=>[
//                                            0=>[ //a卷
//                                                0=>[ // 默认客观题5题为一个块
//                                                    'x'=>round($lineStartX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75,
//                                                    'y'=>($lineStartX/0.75+($x-20))*$pixel).','.round($pixel*($pdf->getY()/0.75+$row),
//                                                    'sheet'=>$pdf->PageNo()
//                                                ]
//                                            ]
//                                        ]
//                                    ];
                                    //写入
                                    for($j=0;$j<2;$j++){
                                        //题号宽度
                                        $order=$iList['content'][$k+$j]['order'].'.';//题号
                                        $orderWidth=25*0.75;//题号宽度
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $order.='('.$iList['content'][$k+$j]['small'].')';//小题号
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                        //有分值
                                        if($score){
                                            $order.='('.$iList['content'][$k+$j]['score'].'分)';//分值
                                            $orderWidth+=40*0.75;//题号宽度
                                        }
                                        //下划线
                                        $uline=0;
                                        if($iContent['uline']){
                                           $uline='B';
                                        }
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->SetFontSize(12*0.75);
                                        //题号
                                        $nowX=$pdf->getX();//到此题时的X
                                        $pdf->setY($pdf->getY()+($row-14)*0.75);
                                        $pdf->setX($nowX);
                                        $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
                                        $pdf->setY($pdf->getY()-($row-14)*0.75);
                                        $pdf->setX($nowX+$orderWidth);
                                        //空
                                        for($i=0;$i<$iList['content'][$k+$j]['kong'];$i++){
                                            $pdf->Cell($hline,$row*0.75,'',$uline);
                                            $pdf->Cell(20*0.75,$row*0.75,'',0);
                                        }
                                        //坐标信息
                                        $coordinate[]=[
                                            'OrderID'=>$orderIDStart++,
                                            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                            'TestOrderStart'=>$iList['content'][$k+$j]['order'], //开始试题
                                            'TestOrderEnd'=>$iList['content'][$k+$j]['order'], //结束试题
                                            'TestSmallID'=>$iList['content'][$k+$j]['small'], //试题包含（1）等数据
                                            'IfChooseTest'=>0, //是否是选做题
                                            'Coordinate'=>[
                                                0=>[ //a卷
                                                    0=>[ // 默认客观题5题为一个块
                                                        'x'=>round($nowX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75),
                                                        'y'=>round(($pdf->getX()/0.75)*$pixel).','.round($pixel*($pdf->getY()/0.75+$row)),
                                                        'sheet'=>$pdf->PageNo()
                                                    ]
                                                ]
                                            ]
                                        ];
                                    }
                                    $pdf->ln();
                                    $pdf->setX($textStartX);
                                    $k++;
                                    continue;
                                }
                                //三题一行
                                if(($iList['content'][$k]['hline']*$iList['content'][$k]['kong']+
                                $iList['content'][$k+1]['hline']*$iList['content'][$k+1]['kong']+
                                $iList['content'][$k+2]['hline']*$iList['content'][$k+2]['kong'])*10==9){
                                    //计算题号总长度
                                    $orderWidth=25*3*0.75;//题号宽度
                                    //如果有分值
                                    if($score){
                                        $orderWidth+=40*3*0.75;//题号宽度
                                    }
                                    //是否有小题号
                                    for($j=0;$j<3;$j++){
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                    }
                                    //每空长度
                                    $hline=($x-40)*0.75;//整行
                                    $hline=$hline-$orderWidth;//题号宽度
                                    $hline=$hline-20*3*0.75;//空间隔
                                    $hline=$hline/3;
//                                    //坐标信息
//                                    $coordinate[]=[
//                                        'OrderID'=>$orderIDStart++,
//                                        'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
//                                        'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
//                                        'TestOrderEnd'=>$iList['content'][$k+2]['order'], //结束试题
//                                        'TestSmallID'=>0, //试题包含（1）等数据
//                                        'IfChooseTest'=>0, //是否是选做题
//                                        'Coordinate'=>[
//                                            0=>[ //a卷
//                                                0=>[ // 默认客观题5题为一个块
//                                                    'x'=>round($lineStartX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75,
//                                                    'y'=>round(($lineStartX/0.75+($x-20))*$pixel).','.round($pixel*($pdf->getY()/0.75+$row),
//                                                    'sheet'=>$pdf->PageNo()
//                                                ]
//                                            ]
//                                        ]
//                                    ];
                                    //写入
                                    for($j=0;$j<3;$j++){
                                        //题号宽度
                                        $order=$iList['content'][$k+$j]['order'].'.';//题号
                                        $orderWidth=25*0.75;//题号宽度
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $order.='('.$iList['content'][$k+$j]['small'].')';//小题号
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                        //有分值
                                        if($score){
                                            $order.='('.$iList['content'][$k+$j]['score'].'分)';//分值
                                            $orderWidth+=40*0.75;//题号宽度
                                        }
                                        //下划线
                                        $uline=0;
                                        if($iContent['uline']){
                                           $uline='B';
                                        }
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->SetFontSize(12*0.75);
                                        //题号
                                        $nowX=$pdf->getX();//到此题时的X
                                        $pdf->setY($pdf->getY()+($row-14)*0.75);
                                        $pdf->setX($nowX);
                                        $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
                                        $pdf->setY($pdf->getY()-($row-14)*0.75);
                                        $pdf->setX($nowX+$orderWidth);
                                        //空
                                        for($i=0;$i<$iList['content'][$k+$j]['kong'];$i++){
                                            $pdf->Cell($hline,$row*0.75,'',$uline);
                                            $pdf->Cell(20*0.75,$row*0.75,'',0);
                                        }
                                        //坐标信息
                                        $coordinate[]=[
                                            'OrderID'=>$orderIDStart++,
                                            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                            'TestOrderStart'=>$iList['content'][$k+$j]['order'], //开始试题
                                            'TestOrderEnd'=>$iList['content'][$k+$j]['order'], //结束试题
                                            'TestSmallID'=>$iList['content'][$k+$j]['small'], //试题包含（1）等数据
                                            'IfChooseTest'=>0, //是否是选做题
                                            'Coordinate'=>[
                                                0=>[ //a卷
                                                    0=>[ // 默认客观题5题为一个块
                                                        'x'=>round($nowX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75),
                                                        'y'=>round(($pdf->getX()/0.75)*$pixel).','.round($pixel*($pdf->getY()/0.75+$row)),
                                                        'sheet'=>$pdf->PageNo()
                                                    ]
                                                ]
                                            ]
                                        ];
                                    }
                                    $pdf->ln();
                                    $pdf->setX($textStartX);
                                    $k+=2;
                                    continue;
                                }
                            }
                            //此题每空宽0.5
                            if($iList['content'][$k]['hline']==0.5){
                                //两题一行
                                if(($iList['content'][$k]['hline']*$iList['content'][$k]['kong']+
                                $iList['content'][$k+1]['hline']*$iList['content'][$k+1]['kong'])==1){
                                    //计算题号总长度
                                    $orderWidth=25*2*0.75;//题号宽度
                                    //如果有分值
                                    if($score){
                                        $orderWidth+=40*2*0.75;//题号宽度
                                    }
                                    //是否有小题号
                                    for($j=0;$j<2;$j++){
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                    }
                                    //每空长度
                                    $hline=($x-40)*0.75;//整行
                                    $hline=$hline-$orderWidth;//题号宽度
                                    $hline=$hline-20*2*0.75;//空间隔
                                    $hline=$hline/2;
//                                    //坐标信息
//                                    $coordinate[]=[
//                                        'OrderID'=>$orderIDStart++,
//                                        'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
//                                        'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
//                                        'TestOrderEnd'=>$iList['content'][$k+1]['order'], //结束试题
//                                        'TestSmallID'=>0, //试题包含（1）等数据
//                                        'IfChooseTest'=>0, //是否是选做题
//                                        'Coordinate'=>[
//                                            0=>[ //a卷
//                                                0=>[ // 默认客观题5题为一个块
//                                                    'x'=>round($lineStartX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75,
//                                                    'y'=>round(($lineStartX/0.75+($x-20))*$pixel).','.round($pixel*($pdf->getY()/0.75+$row),
//                                                    'sheet'=>$pdf->PageNo()
//                                                ]
//                                            ]
//                                        ]
//                                    ];
                                    //写入
                                    for($j=0;$j<2;$j++){
                                        //题号宽度
                                        $order=$iList['content'][$k+$j]['order'].'.';//题号
                                        $orderWidth=25*0.75;//题号宽度
                                        //有小题
                                        if($iList['content'][$k+$j]['small']){
                                            $order.='('.$iList['content'][$k+$j]['small'].')';//小题号
                                            $orderWidth+=15*0.75;//题号宽度
                                        }
                                        //有分值
                                        if($score){
                                            $order.='('.$iList['content'][$k+$j]['score'].'分)';//分值
                                            $orderWidth+=40*0.75;//题号宽度
                                        }
                                        //下划线
                                        $uline=0;
                                        if($iContent['uline']){
                                           $uline='B';
                                        }
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->SetFontSize(12*0.75);
                                        //题号
                                        $nowX=$pdf->getX();//到此题时的X
                                        $pdf->setY($pdf->getY()+($row-14)*0.75);
                                        $pdf->setX($nowX);
                                        $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
                                        $pdf->setY($pdf->getY()-($row-14)*0.75);
                                        $pdf->setX($nowX+$orderWidth);
                                        //空
                                        for($i=0;$i<$iList['content'][$k+$j]['kong'];$i++){
                                            $pdf->Cell($hline,$row*0.75,'',$uline);
                                            $pdf->Cell(20*0.75,$row*0.75,'',0);
                                        }
                                        //坐标信息
                                        $coordinate[]=[
                                            'OrderID'=>$orderIDStart++,
                                            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                            'TestOrderStart'=>$iList['content'][$k+$j]['order'], //开始试题
                                            'TestOrderEnd'=>$iList['content'][$k+$j]['order'], //结束试题
                                            'TestSmallID'=>$iList['content'][$k+$j]['small'], //试题包含（1）等数据
                                            'IfChooseTest'=>0, //是否是选做题
                                            'Coordinate'=>[
                                                0=>[ //a卷
                                                    0=>[ // 默认客观题5题为一个块
                                                        'x'=>round($nowX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75),
                                                        'y'=>round(($pdf->getX()/0.75)*$pixel).','.round($pixel*($pdf->getY()/0.75+$row)),
                                                        'sheet'=>$pdf->PageNo()
                                                    ]
                                                ]
                                            ]
                                        ];
                                    }
                                    $pdf->ln();
                                    $pdf->setX($textStartX);
                                    $k++;
                                    continue;
                                }
                            }
                        }
//                    foreach($iList['content'] as $iContent){
//                        //换页
//                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                            //完成之前黑框
//                            $pdf->SetDrawColor(0,0,0);
//                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                            //A3
//                            if($column){
//                                //需要换页
//                                if($column==3){
//                                    $column=1;
//                                    $pdf->AddPage();
//                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                }
//                                //换列
//                                else{
//                                    $column++;
////                                    $lineStartX+=$x*0.75;//黑框开始X
////                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $pdf->SetY(($pdf::TOP+16)*0.75);
//                                    $columnX=$column*$x*0.75;//列开始X
//                                    $lineStartX+=$x*0.75;//黑框开始X
//                                    $textStartX=$lineStartX+10*0.75;//文字开始X
//                                    $chooseStartX=$textStartX;
//                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                    $pdf->setX($textStartX);
//                                    $imgStartY=$pdf->getY();//图片开始Y
//                                }
//                            }
//                            //A4
//                            else{
//                                $pdf->AddPage();
//                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                $textStartX=$lineStartX+10*0.75;//文字开始X
//                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                $pdf->setX($textStartX);
//                            }
//                        }
//                        //题号宽度
//                        $order=$iContent['order'];//题号
//                        $orderWidth=15*0.75;//题号宽度
//                        //有小题
//                        if($iContent['small']){
//                            $order.='('.$iContent['small'].')';//小题号
//                            $orderWidth+=15*0.75;//题号宽度
//                        }
//                        //有分值
//                        if($score){
//                            $order.='('.$iContent['score'].'分)';//分值
//                            $orderWidth+=40*0.75;//题号宽度
//                        }
//                        //空白长度
//                        $hline=($x-40)*0.75;//整行
//                        if($iContent['hline']==0.3){
////                            $hline=$hline-15*0.75;//题号宽
////                            if($iContent['small']) $hline=$hline-15*0.75;//小题号宽
////                            if($score) $hline=$hline-30*0.75;//分值宽
//                            $hline=$hline-$orderWidth;//题号宽度
//                            $hline=$hline-20*3*0.75;//空间隔
//                            $hline=$hline/3;
//                        }
//                        if($iContent['hline']==0.5){
////                            $hline=$hline-15*0.75;//题号宽
////                            if($iContent['small']) $hline=$hline-15*0.75;//小题号宽
////                            if($score) $hline=$hline-30*0.75;//分值宽
//                            $hline=$hline-$orderWidth;//题号宽度
//                            $hline=$hline-20*2*0.75;//空间隔
//                            $hline=$hline/2;
//                        }
//                        //下划线
//                        $uline=0;
//                        if($iContent['uline']){
//                           $uline='B';
//                        }
                        //其他情况填空题
                        if($iContent['kong']){
                            //换行
                            if($pdf->getX()+$orderWidth+$hline*$iContent['kong']>$textStartX+($x-40)*0.75&&$pdf->getX()!=$textStartX){
                                $pdf->ln();
                                $pdf->setX($textStartX);
                            }
                            //试题间隔线
                            if($iContent['hline']>=1){
                                $pdf->ln(10*0.75);
                                $pdf->setX($textStartX);
                                $pdf->line($lineStartX,$pdf->getY(),$lineStartX+($x-20)*0.75,$pdf->getY());
                            }
                            //换页
                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                }
                            }
//                            $order=$iContent['order'];//题号
//                            $orderWidth=15*0.75;//题号宽度
//                            //有小题
//                            if($iContent['small']){
//                                $order.='('.$iContent['small'].')';//小题号
//                                $orderWidth+=15*0.75;//题号宽度
//                            }
//                            //有分值
//                            if($score){
//                                $order.='('.$iContent['score'].'分)';//分值
//                                $orderWidth+=30*0.75;//题号宽度
//                            }
                            //试题开始页
                            $startPage=$pdf->PageNo();
                            //试题开始列
                            $startCol=$column;
                            //试题开始X,Y
                            $orderStartX=$pdf->getX();
                            if($iContent['hline']>=1){
                                $orderStartX=$pdf->getX()-10*0.75;
                            }
                            $orderStartY=$pdf->getY();

                            $pdf->SetTextColor(0,0,0);
                            $pdf->SetFontSize(12*0.75);
                            //有下划线时题号高度
                            if($uline){
                                $nowX=$pdf->getX();//到此题时的X
                                $pdf->setY($pdf->getY()+($row-14)*0.75);
                                $pdf->setX($nowX);
                                $pdf->Cell($orderWidth*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
                                $pdf->setY($pdf->getY()-($row-14)*0.75);
                                $pdf->setX($nowX+$orderWidth);
                            }
                            //没下划线时题号高度
                            else{
                                $pdf->Cell($orderWidth*0.75,$row*0.75,iconv('utf-8','gbk',$order),0,0,'L');
                            }
//                            $order=$iContent['order'];
//                            if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
//                            if($score) $order.='('.$iContent['score'].'分)';
//                            $pdf->SetTextColor(0,0,0);
//                            $pdf->SetFontSize(12*0.75);
//                            $nowX=$pdf->getX();//到此题时的X
//                            $pdf->setY($pdf->getY()+($row-14)*0.75);
//                            $pdf->setX($nowX);
//                            $pdf->Cell(60*0.75,14*0.75,iconv('utf-8','gbk',$order),0,0,'L');
//                            $pdf->setY($pdf->getY()-($row-14)*0.75);
//                            $pdf->setX($nowX+60*0.75);
                            //试题描述
                            if($iContent['desc']){
                                $pdf->ln($row*0.75);
                                $pdf->setX($textStartX);
                                //处理换行标签
                                $desc=$this->delHtml($iContent['desc']);
                                //获取描述高度
                                $descHeight=$this->getHeight($pdf,$desc,$x);
                                //是否需要换页
                                if($pdf->getY()+$descHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                    }
                                }
                                //写入试题描述
                                $this->writeDesc($pdf,$desc,$x,$textStartX,$iContent['hline']);
                            }
                            $imgStartY=$pdf->getY();//图片开始Y
                            $imgNum=0;

                            //空
                            for($i=0;$i<$iContent['kong'];$i++){
                                //换行
                                if($pdf->getX()+$hline>$lineStartX+($x-40)*0.75&&$iContent['hline']<1){
                                    $pdf->ln();
                                    $pdf->setX($textStartX+$orderWidth);
                                }
                                //换页
                                if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $pdf->setX($textStartX+$orderWidth);
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                            $pdf->setX($textStartX+$orderWidth);
                                            $imgStartY=$pdf->getY();//图片开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX+$orderWidth);
                                    }
                                }
//                                if($i>0){
//                                    if($pdf->getX()!=$textStartX+60*0.75){
//                                        $pdf->setX($pdf->getX()+60*0.75);
//                                    }
//                                }
                                //空长小于1
                                if($iContent['hline']<1){
                                    $pdf->Cell($hline,$row*0.75,'',$uline);
                                    $pdf->Cell(20*0.75,$row*0.75,'');
                                }
                                //空长度是整行
                                else{
                                    for($j=0;$j<$iContent['hline'];$j++){
                                        //换页
                                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                            //完成之前黑框
                                            $pdf->SetDrawColor(0,0,0);
                                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                            //A3
                                            if($column){
                                                //需要换页
                                                if($column==3){
                                                    $column=1;
                                                    $pdf->AddPage();
                                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                                    $chooseStartX=$textStartX;
                                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                                    $pdf->setX($textStartX);
                                                    $imgStartY=$pdf->getY();//图片开始Y
                                                }
                                                //换列
                                                else{
                                                    $column++;
                                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                                    $columnX=$column*$x*0.75;//列开始X
                                                    $lineStartX+=$x*0.75;//黑框开始X
                                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                                    $chooseStartX=$textStartX;
                                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                                    $pdf->setX($textStartX);
                                                    $imgStartY=$pdf->getY();//图片开始Y
                                                }
                                            }
                                            //A4
                                            else{
                                                $pdf->AddPage();
                                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                                $pdf->setX($textStartX);
                                            }
                                        }
                                        //第一行
                                        if($i==0&&$j==0){
                                            $pdf->Cell($hline-$orderWidth,$row*0.75,'',$uline,1);
                                        }
                                        else{
                                            $pdf->Cell($hline,$row*0.75,'',$uline,1);
                                        }
                                        $pdf->setX($textStartX);
                                    }
                                }
                            }
                            //试题结束页
                            $endPage=$pdf->PageNo();
                            //试题结束列
                            $endCol=$column;
                            //试题结束X,Y
                            $orderEndX=$pdf->getX();
                            $orderEndY=$pdf->getY();
                            //大题情况结束Y
                            if($iContent['hline']>=1){
                                $orderEndY=$pdf->getY()+10*0.75;
                            }
                            //换页情况结束Y
                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                $orderEndY=($pdf::HIGH-$pdf::TOP-16)*0.75;
                            }
                            //坐标信息分多少块
                            $orderCoorArr=[];
//                            //页
//                            for($i=$startPage;$i<=$endPage;$i++){
//                                //列
//                                for($j=$startCol;$j<=$endCol;$j++){
//                                    //第一块
//                                    if($j==$startCol){
//                                        //A3
//                                        if($j){
//                                            $orderCoorArr[]=[
//                                                'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75,
//                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                'sheet'=>$i
//                                            ];
//                                            //没跨页的时候加上最后一行
//                                            if($startCol==$endCol&&$iContent['hline']<1){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                        //A4
//                                        else{
//                                            $orderCoorArr[]=[
//                                                'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75,
//                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                'sheet'=>$i
//                                            ];
//                                            //没跨页的时候加上最后一行
//                                            if($startCol==$endCol&&$iContent['hline']<1){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                    }
//                                    //最后一块
//                                    elseif($j==$endCol){
//                                        //A3
//                                        if($j){
//                                            $orderCoorArr[]=[
//                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                'sheet'=>$i
//                                            ];
//                                            //如果空长小于1，加上最后一行
//                                            if($iContent['hline']<1){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                        //A4
//                                        else{
//                                            $orderCoorArr[]=[
//                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                'sheet'=>$i
//                                            ];
//                                            //如果空长小于1，加上最后一行
//                                            if($iContent['hline']<1){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                    }
//                                    //中间块
//                                    else{
//                                        //A3
//                                        if($j){
//                                            $orderCoorArr[]=[
//                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                'sheet'=>$i
//                                            ];
//                                        }
//                                        //A4
//                                        else{
//                                            $orderCoorArr[]=[
//                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                'sheet'=>$i
//                                            ];
//                                        }
//                                    }
//                                }
//                            }
                            //不跨页，不跨列
                            if($startPage==$endPage&&$startCol==$endCol){
                                //A3
                                if($startCol){
                                    $orderCoorArr[]=[
                                        'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75),
                                        'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                        'sheet'=>$startPage
                                    ];
                                    //如果空长小于1，加上最后一行
                                    if($iContent['hline']<1){
                                        //如果试题是从行的开端开始的
                                        if($orderStartX/0.75==($pdf::LEFT+10+($j-1)*$x)){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($startCol-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'y'=>round(($orderEndX/0.75)*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderEndY/0.75),
                                                //'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                }
                                //A4
                                else{
                                    $orderCoorArr[]=[
                                        'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75),
                                        'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                        'sheet'=>$startPage
                                    ];
                                    //如果空长小于1，加上最后一行
                                    if($iContent['hline']<1){
                                        //如果试题是从行的开端开始的
                                        if($orderStartX/0.75==$pdf::LEFT+20){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+20)*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                }
                            }
                            //不跨页，跨列，只有A3情况
                            elseif($startPage==$endPage){
                                for($j=$startCol;$j<=$endCol;$j++){
                                    //第一块
                                    if($j==$startCol){
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                    //最后一块
                                    elseif($j==$endCol){
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'sheet'=>$startPage
                                            ];
                                            //如果空长小于1，加上最后一行
                                            if($iContent['hline']<1){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75),
                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                    'sheet'=>$startPage
                                                ];
                                            }
                                        }
                                    }
                                    //中间块
                                    else{
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                }
                            }
                            //跨页
                            else{
                                //每一页
                                for($i=$startPage;$i<=$endPage;$i++){
                                    //A3
                                    if($startCol){
                                        //第一页
                                        if($i==$startPage){
                                            //每一块
                                            for($j=$startCol;$j<=3;$j++){
                                                //第一块
                                                if($j==$startCol){
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                                //其他块
                                                else{
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                            }
                                        }
                                        //最后一页
                                        elseif($i==$endPage){
                                            //每一块
                                            for($j=1;$j<=$endCol;$j++){
                                                //最后一块
                                                if($j==$endCol){
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                        'sheet'=>$i
                                                    ];
                                                    //如果空长小于1，加上最后一行
                                                    if($iContent['hline']<1){
                                                        $orderCoorArr[]=[
                                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75),
                                                            'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                            'sheet'=>$i
                                                        ];
                                                    }
                                                }
                                                //其他块
                                                else{
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                            }
                                        }
                                        //中间页
                                        else{
                                            //每一块
                                            for($j=1;$j<=3;$j++){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //A4
                                    else{
                                        //第一块
                                        if($i==$startPage){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                        //最后一块
                                        elseif($i==$endPage){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+26)),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'sheet'=>$i
                                            ];
                                            //如果空长小于1，加上最后一行
                                            if($iContent['hline']<1){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderEndY/0.75),
                                                    'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*($orderEndY/0.75+$row)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                        //中间块
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                            }
//                            //跨页
//                            else{
//                                //每一页
//                                for($i=$startPage;$i<=$endPage;$i++){
//                                    //A3
//                                    if($startCol){
//                                            //A3
//                                            if($j){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75,
//                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                            //A4
//                                            else{
//                                                $orderCoorArr[]=[
//                                                    'x'=>round($orderStartX/0.75*$pixel).','.round($pixel*$orderStartY/0.75,
//                                                    'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                        //最后一块
//                                        elseif($j==$endCol){
//                                            //A3
//                                            if($j){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'sheet'=>$i
//                                                ];
//                                                //如果空长小于1，加上最后一行
//                                                if($iContent['hline']<1){
//                                                    $orderCoorArr[]=[
//                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                        'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                        'sheet'=>$i
//                                                    ];
//                                                }
//                                            }
//                                            //A4
//                                            else{
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                    'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                    'sheet'=>$i
//                                                ];
//                                                //如果空长小于1，加上最后一行
//                                                if($iContent['hline']<1){
//                                                    $orderCoorArr[]=[
//                                                        'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderEndY/0.75,
//                                                        'y'=>round($orderEndX/0.75*$pixel).','.round($pixel*$orderEndY/0.75+$row,
//                                                        'sheet'=>$i
//                                                    ];
//                                                }
//                                            }
//                                        }
//                                        //中间块
//                                        else{
//                                            //A3
//                                            if($j){
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                            //A4
//                                            else{
//                                                $orderCoorArr[]=[
//                                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16),
//                                                    'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16),
//                                                    'sheet'=>$i
//                                                ];
//                                            }
//                                        }
//                                    }
//                                }
//                            }
                            //坐标信息
                            $coordinate[]=[
                                'OrderID'=>$orderIDStart++,
                                'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
                                'TestOrderEnd'=>$iList['content'][$k]['order'], //结束试题
                                'TestSmallID'=>$iList['content'][$k]['small'], //试题包含（1）等数据
                                'IfChooseTest'=>0, //是否是选做题
                                'Coordinate'=>[
                                    0=>$orderCoorArr //a卷
                                ]
                            ];
                        }
                        //大题
                        else{
                            if($pdf->getX()>$textStartX) $pdf->ln();//换行
                            //试题间隔
                            if($pdf->getY()!=($lineStartY+10*0.75)){
                                $pdf->ln(10*0.75);
                            }
                            //换页
                            if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                }
                            }
                            //换行
//                            if($pdf->getX()!=$textStartX){
//                                $pdf->ln();
//                            }
                            //试题间隔线
                            if($pdf->getY()!=($lineStartY+10*0.75)){
                                $pdf->line($lineStartX,$pdf->getY(),$lineStartX+($x-20)*0.75,$pdf->getY());
                            }
                            //设置文字开始X
                            $pdf->setX($textStartX);
                            //试题开始页
                            $startPage=$pdf->PageNo();
                            //试题开始列
                            $startCol=$column;
                            //试题开始Y
                            $orderStartY=$pdf->getY();

                            //题号
                            $order=$iContent['order'];
                            if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
                            if($score) $order.='('.$iContent['score'].'分)';
                            $pdf->SetTextColor(0,0,0);
                            $pdf->SetFontSize(12*0.75);
                            $pdf->Cell(60*0.75,24*0.75,iconv('utf-8','gbk',$order),0,1,'L');
                            //换页
                            if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                }
                            }
                            //试题描述
                            if($iContent['desc']){
//                                $this->getDesc($pdf,$x,$lineStartX,$lineStartY,$textStartX,$iContent['desc'],$iContent['hline']);
                                $pdf->setX($textStartX);
                                //处理换行标签
                                $desc=$this->delHtml($iContent['desc']);
                                //获取描述高度
                                $descHeight=$this->getHeight($pdf,$desc,$x);
                                //是否需要换页
                                if($pdf->getY()+$descHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                    }
                                }
                                //写入试题描述
                                $this->writeDesc($pdf,$desc,$x,$textStartX,$iContent['hline']);
//                                $pdf->setX($textStartX);
//                                $strWidth=$pdf->GetStringWidth($iContent['desc']);//描述宽度
//                                $strHeight=ceil($strWidth/(($x-40)*0.75))*24*0.75;//描述高度
//                                //短文改错行高
//                                if(!$iContent['hline']){
//                                    $strHeight=ceil($strWidth/(($x-40)*0.75))*$row*0.75;//高度
//                                }
////                                $strWidth=$pdf->GetStringWidth($iContent['desc']);//描述宽度
////                                $strHeight=ceil($strWidth/(($x-40)*0.75))*24*0.75;//说明高度
////                                $pdf->text($pdf->getX(),$pdf->getY(),ceil($strWidth/(($x-40)*0.75)));
////                                $strLineStartY=$pdf->getY();//说明开始Y
//                                //是否需要换页
//                                if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
//                                    //完成之前黑框
//                                    $pdf->SetDrawColor(0,0,0);
//                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
//                                    //A3
//                                    if($column){
//                                        //需要换页
//                                        if($column==3){
//                                            $column=1;
//                                            $pdf->AddPage();
//                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        }
//                                        //换列
//                                        else{
//                                            $column++;
//                                            $pdf->SetY(($pdf::TOP+16)*0.75);
//                                            $columnX=$column*$x*0.75;//列开始X
//                                            $lineStartX+=$x*0.75;//黑框开始X
//                                            $textStartX=$lineStartX+10*0.75;//文字开始X
//                                            $chooseStartX=$textStartX;
//                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        }
//                                    }
//                                    //A4
//                                    else{
//                                        $pdf->AddPage();
//                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
//                                        $textStartX=$lineStartX+10*0.75;//文字开始X
//                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
//                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
//                                        $pdf->setX($textStartX);
//                                    }
//                                }
//                                //试题描述部分
//                                $pdf->SetFontSize(12*0.75);
//                                $pdf->setX($textStartX);
//                                if($iContent['hline']){
//                                    $pdf->MultiCell(($x-40)*0.75,24*0.75,iconv('utf-8','gbk',$iContent['desc']));
//                                }
//                                //短文改错
//                                else{
//                                    $pdf->MultiCell(($x-40)*0.75,$row*0.75,iconv('utf-8','gbk',$iContent['desc']));
//                                }
//                                $pdf->setX($textStartX);
                            }
                            $imgStartY=$pdf->getY();//图片开始Y
                            $imgNum=0;
                            //空
                            for($i=0;$i<$iContent['hline'];$i++){
                                //换页
                                if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                    //完成之前黑框
                                    $pdf->SetDrawColor(0,0,0);
                                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                    //A3
                                    if($column){
                                        //需要换页
                                        if($column==3){
                                            $column=1;
                                            $pdf->AddPage();
                                            $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                            $pdf->setX($textStartX);
                                            $imgStartY=$pdf->getY();//图片开始Y
                                        }
                                        //换列
                                        else{
                                            $column++;
                                            $pdf->SetY(($pdf::TOP+16)*0.75);
                                            $columnX=$column*$x*0.75;//列开始X
                                            $lineStartX+=$x*0.75;//黑框开始X
                                            $textStartX=$lineStartX+10*0.75;//文字开始X
                                            $chooseStartX=$textStartX;
                                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                            $pdf->setX($textStartX);
                                            $imgStartY=$pdf->getY();//图片开始Y
                                        }
                                    }
                                    //A4
                                    else{
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                $hwidth=$hline;
                                if($i==0){
                                    if(!$iContent['desc']){
                                        $hwidth=$hline-($pdf->getX()-$textStartX);
                                    }
                                }
                                $pdf->setX($textStartX);
                                $pdf->Cell($hwidth,$row*0.75,'',$uline,1);
                                //是否有图片
                                if($iContent['img'][$imgNum]){
                                    $kongY=$pdf->getY();
                                    $imgSize=getimagesize($iContent['img'][$imgNum][0]);
                                    //能否放下图片
                                    if($pdf->getY()-$imgStartY>($imgSize[1]+18)*0.75){
                                        $pdf->SetFillColor(255,255,255);
                                        $pdf->Rect($lineStartX+($x-20-10-$imgSize[0]-10)*0.75,$imgStartY,10*0.75,($imgSize[1]+18)*0.75,'F');
                                        $pdf->Image($iContent['img'][$imgNum][0],$lineStartX+($x-20-10-$imgSize[0])*0.75,$imgStartY);
                                        $pdf->setY($imgStartY+$imgSize[1]*0.75);
                                        $pdf->setX($lineStartX+($x-20-10-$imgSize[0])*0.75);
                                        $pdf->SetFontSize(12*0.75);
                                        $pdf->SetTextColor(0,0,0);
                                        $pdf->Cell($imgSize[0]*0.75+1,18*0.75,iconv('utf-8','gbk',$iContent['img'][$imgNum][1]),0,1,'C',1);
                                        $imgStartY=$pdf->getY();
                                        $pdf->setY($kongY);
                                        $imgNum++;
                                    }
                                }
                                $pdf->setX($textStartX);
                            }
                            //试题结束页
                            $endPage=$pdf->PageNo();
                            //试题结束列
                            $endCol=$column;
                            //试题结束Y
                            $orderEndY=$pdf->getY();
                            //坐标信息分多少块
                            $orderCoorArr=[];
                            //不跨页，不跨列
                            if($startPage==$endPage&&$startCol==$endCol){
                                //A3
                                if($startCol){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10+($startCol-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                        'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                        'sheet'=>$startPage
                                    ];
                                }
                                //A4
                                else{
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                        'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                        'sheet'=>$startPage
                                    ];
                                }
                            }
                            //不跨页，跨列，只有A3情况
                            elseif($startPage==$endPage){
                                for($j=$startCol;$j<=$endCol;$j++){
                                    //第一块
                                    if($j==$startCol){
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                    //最后一块
                                    elseif($j==$endCol){
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                    //中间块
                                    else{
                                        //A3
                                        if($j){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$startPage
                                            ];
                                        }
                                    }
                                }
                            }
                            //跨页
                            else{
                                //每一页
                                for($i=$startPage;$i<=$endPage;$i++){
                                    //A3
                                    if($startCol){
                                        //第一页
                                        if($i==$startPage){
                                            //每一块
                                            for($j=$startCol;$j<=3;$j++){
                                                //第一块
                                                if($j==$startCol){
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                                //其他块
                                                else{
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                            }
                                        }
                                        //最后一页
                                        elseif($i==$endPage){
                                            //每一块
                                            for($j=1;$j<=$endCol;$j++){
                                                //最后一块
                                                if($j==$endCol){
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                                //其他块
                                                else{
                                                    $orderCoorArr[]=[
                                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                        'sheet'=>$i
                                                    ];
                                                }
                                            }
                                        }
                                        //中间页
                                        else{
                                            //每一块
                                            for($j=1;$j<=3;$j++){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //A4
                                    else{
                                        //第一块
                                        if($i==$startPage){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                        //最后一块
                                        elseif($i==$endPage){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'sheet'=>$i
                                            ];
                                        }
                                        //中间块
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                            }
                            //坐标信息
                            $coordinate[]=[
                                'OrderID'=>$orderIDStart++,
                                'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                                'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
                                'TestOrderEnd'=>$iList['content'][$k]['order'], //结束试题
                                'TestSmallID'=>$iList['content'][$k]['small'], //试题包含（1）等数据
                                'IfChooseTest'=>0, //是否是选做题
                                'Coordinate'=>[
                                    0=>$orderCoorArr //a卷
                                ]
                            ];
                        }
                    }
                    //黑框
                    if($pdf->getX()>$textStartX) $pdf->ln();//换行
                    $pdf->ln(10*0.75);
                    $pdf->SetDrawColor(0,0,0);
                    //要换页时的黑框高度
                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                    }
                    //正常情况的黑框高度
                    else{
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                    }
//                    $pdf->ln(10*0.75);
//                    //黑框
//                    $pdf->SetDrawColor(0,0,0);
//                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                }
                //选作题
                if($iList['type']==2){
                    $orderArr=$imgArr=[];//题号,图片
                    $uline='B';//下划线
                    $hline=$testScore=0;//行数,试题分数
                    foreach($iList['content'] as $iContent){
                        //题号
                        $order=$iContent['order'];
//                        if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
                        $orderArr[]=$order;
//                        $orderChoose[]='['.$order.']';
                        if($iContent['img']){
                            foreach($iContent['img'] as $iImg){
                                $imgArr[]=$iImg;
                            }
                        }
                        //是否有下划线
                        if(!$iContent['uline']){
                            $uline=0;
                        }
                        //行数
                        if($hline<$iContent['hline']){
                            $hline=$iContent['hline'];
                        }
                        //分数
                        if($testScore<$iContent['score']){
                            $testScore=' ('.$iContent['score'].'分)';
                        }
                    }
                    //是否显示分数
                    if(!$score){
                        $testScore='';
                    }
                    //选做题说明
                    $listStr='选答题,请考生在'.implode('、',$orderArr).'题中任选'.$iList['do'].'题作答。请把你所选题目的题号用2B铅笔涂黑。如果多做，则按所做的第一题计分。在作答过程中请写清每问的小标号。'.$testScore;
                    $pdf->SetFontSize(12*0.75);
                    $strWidth=$pdf->GetStringWidth($listStr);//说明宽度
                    $strHeight=(ceil($strWidth/(($x-40)*0.75))+1)*24*0.75;//说明高度
//                    $strLineStartY=$pdf->getY();//说明开始Y
                    //是否需要换页
                    if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        //完成之前黑框
                        $pdf->SetDrawColor(0,0,0);
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                        //A3
                        if($column){
                            //需要换页
                            if($column==3){
                                $column=1;
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $chooseStartX=$textStartX;
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                            //换列
                            else{
                                $column++;
                                $pdf->SetY(($pdf::TOP+16)*0.75);
                                $columnX=$column*$x*0.75;//列开始X
                                $lineStartX+=$x*0.75;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $chooseStartX=$textStartX;
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //A4
                        else{
                            $pdf->AddPage();
                            $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                            $textStartX=$lineStartX+10*0.75;//文字开始X
                            $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                            $pdf->setY($lineStartY+10*0.75);//文字开始Y
                            $pdf->setX($textStartX);
                            $imgStartY=$pdf->getY();//图片开始Y
                        }
                    }
                    //选做题说明部分
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->setX($textStartX);
                    $pdf->MultiCell(($x-40)*0.75,24*0.75,iconv('utf-8','gbk',$listStr));
                    $pdf->setX($textStartX);
                    $pdf->Write(24*0.75,iconv('utf-8','gbk','我选做的题号是： '));
                    $pdf->SetTextColor(228,3,127);
                    //选做题涂卡区域坐标
                    $coordinate[]=[
                        'OrderID'=>$orderIDStart++, //主观题 多选题 三选一  第一个为选择框
                        'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                        'TestOrderStart'=>$orderArr[0], //开始试题
                        'TestOrderEnd'=>$orderArr[(count($orderArr)-1)], //结束试题
                        'TestSmallID'=>0, //试题包含（1）等数据
                        'IfChooseTest'=>$iList['do'], //是否是选做题 三选一中的一
                        'Coordinate'=>[
                            0=>[ //a卷
                                0=>[
                                    'x'=>round($pdf->getX()/0.75*$pixel).','.round($pixel*($pdf->getY()/0.75)),
                                    'y'=>round(($pdf->getX()/0.75+count($orderArr)*40)*$pixel).','.round($pixel*($pdf->getY()/0.75+24)),
                                    'sheet'=>$pdf->PageNo(),
                                    "sub"=>[
                                        'x'=>round(($pdf->getX()/0.75+3)*$pixel).','.round($pixel*($pdf->getY()/0.75+6)),//第一个矩形的左上
                                        'y'=>round(($pdf->getX()/0.75+27)*$pixel).','.round($pixel*($pdf->getY()/0.75+18)),//第一个矩形的右下
                                        'z'=>round(16*$pixel).','.round(0*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                                        'xn'=>count($orderArr), //水平数量
                                        'yn'=>1, //垂直数量
                                        'd'=>0, //选项方向
                                    ]
                                ]
                            ]
                        ]
                    ];
                    //选做题涂卡区域
                    foreach($orderArr as $iOrder){
                        $pdf->Cell(8*0.75,24*0.75,'[',0,0,'C');
                        $pdf->Cell(14*0.75,24*0.75,iconv('utf-8','gbk',$iOrder),0,0,'C');
                        $pdf->Cell(8*0.75,24*0.75,']',0,0,'C');
                        $pdf->setX($pdf->getX()+10*0.75);
                    }
                    $pdf->ln();
                    $pdf->line($lineStartX,$pdf->getY(),$lineStartX+($x-20)*0.75,$pdf->getY());
                    $pdf->ln(1);//图片和说明间距
                    $pdf->setX($textStartX);
//                    $imgStartY=$pdf->getY();//图片开始Y
//                    $imgNum=0;
                    //有几个空
                    for($k=0;$k<$iList['do'];$k++){
                        //换页
                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                            //完成之前黑框
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                            //A3
                            if($column){
                                //需要换页
                                if($column==3){
                                    $column=1;
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                                //换列
                                else{
                                    $column++;
                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                    $columnX=$column*$x*0.75;//列开始X
                                    $lineStartX+=$x*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //A4
                            else{
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //图片开始Y
                        $imgStartY=$pdf->getY();
                        $imgNum=0;
                        //空开始页
                        $startPage=$pdf->PageNo();
                        //空开始列
                        $startCol=$column;
                        //空开始Y
                        $orderStartY=$pdf->getY();
                        //每个空几行
                        for($i=0;$i<$hline;$i++){
                            //换页
                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            $pdf->Cell(($x-40)*0.75,$row*0.75,'',$uline,1);
                            //是否有图片
                            if($imgArr[$imgNum]){
                                $kongY=$pdf->getY();
                                $imgSize=getimagesize($imgArr[$imgNum][0]);
                                //能否放下图片
                                if($pdf->getY()-$imgStartY>($imgSize[1]+18)*0.75){
                                    $pdf->SetFillColor(255,255,255);
                                    $pdf->Rect($lineStartX+($x-20-10-$imgSize[0]-10)*0.75,$imgStartY,10*0.75,($imgSize[1]+18)*0.75,'F');
                                    $pdf->Image($imgArr[$imgNum][0],$lineStartX+($x-20-10-$imgSize[0])*0.75,$imgStartY);
                                    $pdf->setY($imgStartY+$imgSize[1]*0.75);
                                    $pdf->setX($lineStartX+($x-20-10-$imgSize[0])*0.75);
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Cell($imgSize[0]*0.75+1,18*0.75,iconv('utf-8','gbk',$imgArr[$imgNum][1]),0,1,'C',1);
                                    $imgStartY=$pdf->getY();
                                    $pdf->setY($kongY);
                                    $imgNum++;
                                }
                            }
                            $pdf->setX($textStartX);
                        }
                        //试题结束页
                        $endPage=$pdf->PageNo();
                        //试题结束列
                        $endCol=$column;
                        //试题结束Y
                        $orderEndY=$pdf->getY();
                        //坐标信息分多少块
                        $orderCoorArr=[];
                        //不跨页，不跨列
                        if($startPage==$endPage&&$startCol==$endCol){
                            //A3
                            if($startCol){
                                $orderCoorArr[]=[
                                    'x'=>round(($pdf::LEFT+10+($startCol-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                    'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                    'sheet'=>$startPage
                                ];
                            }
                            //A4
                            else{
                                $orderCoorArr[]=[
                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                    'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                    'sheet'=>$startPage
                                ];
                            }
                        }
                        //不跨页，跨列，只有A3情况
                        elseif($startPage==$endPage){
                            for($j=$startCol;$j<=$endCol;$j++){
                                //第一块
                                if($j==$startCol){
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                                //最后一块
                                elseif($j==$endCol){
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                                //中间块
                                else{
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                            }
                        }
                        //跨页
                        else{
                            //每一页
                            for($i=$startPage;$i<=$endPage;$i++){
                                //A3
                                if($startCol){
                                    //第一页
                                    if($i==$startPage){
                                        //每一块
                                        for($j=$startCol;$j<=3;$j++){
                                            //第一块
                                            if($j==$startCol){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                            //其他块
                                            else{
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //最后一页
                                    elseif($i==$endPage){
                                        //每一块
                                        for($j=1;$j<=$endCol;$j++){
                                            //最后一块
                                            if($j==$endCol){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                    'sheet'=>$i
                                                ];
                                            }
                                            //其他块
                                            else{
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //中间页
                                    else{
                                        //每一块
                                        for($j=1;$j<=3;$j++){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                                //A4
                                else{
                                    //第一块
                                    if($i==$startPage){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($orderStartY/0.75)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$i
                                        ];
                                    }
                                    //最后一块
                                    elseif($i==$endPage){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($orderEndY/0.75)),
                                            'sheet'=>$i
                                        ];
                                    }
                                    //中间块
                                    else{
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$i
                                        ];
                                    }
                                }
                            }
                        }
                        //坐标信息
                        $coordinate[]=[
                            'OrderID'=>$orderIDStart++, //主观题 多选题 三选一  第一个为选择框
                            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                            'TestOrderStart'=>$orderArr[0], //开始试题
                            'TestOrderEnd'=>$orderArr[(count($orderArr)-1)], //结束试题
                            'TestSmallID'=>0, //试题包含（1）等数据
                            'IfChooseTest'=>$iList['do'], //是否是选做题 三选一中的一
                            'Coordinate'=>[
                                0=>$orderCoorArr //a卷
                            ]
                        ];
                    }
                    //黑框
                    $pdf->ln(10*0.75);
                    $pdf->SetDrawColor(0,0,0);
                    //要换页时的黑框高度
                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                    }
                    //正常情况的黑框高度
                    else{
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                    }
//                    //黑框
//                    $pdf->ln(10*0.75);
//                    $pdf->SetDrawColor(0,0,0);
//                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                }
                //语文作文
                if($iList['type']==3){
                    $pdf->setX($textStartX);
                    foreach($iList['content'] as $iContent){
                        //换页
                        if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                            //完成之前黑框
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                            //A3
                            if($column){
                                //需要换页
                                if($column==3){
                                    $column=1;
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                                //换列
                                else{
                                    $column++;
                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                    $columnX=$column*$x*0.75;//列开始X
                                    $lineStartX+=$x*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //A4
                            else{
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //试题开始页
                        $startPage=$pdf->PageNo();
                        //试题开始列
                        $startCol=$column;
                        //试题开始Y
                        $orderStartY=$pdf->getY();
                        //题号
                        $order=$iContent['order'];
                        if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
                        if($score) $order.='('.$iContent['score'].'分)';
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFontSize(12*0.75);
                        $pdf->Cell(60*0.75,24*0.75,iconv('utf-8','gbk',$order),0,1,'L');
                        //换页
                        if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                            //完成之前黑框
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                            //A3
                            if($column){
                                //需要换页
                                if($column==3){
                                    $column=1;
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                                //换列
                                else{
                                    $column++;
                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                    $columnX=$column*$x*0.75;//列开始X
                                    $lineStartX+=$x*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //A4
                            else{
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //试题描述
                        if($iContent['desc']){
                            $pdf->setX($textStartX);
                            //处理换行标签
                            $desc=$this->delHtml($iContent['desc']);
                            //获取描述高度
                            $descHeight=$this->getHeight($pdf,$desc,$x);
                            //换页
                            if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //写入试题描述
                            $this->writeDesc($pdf,$desc,$x,$textStartX,1);
                            $pdf->setX($textStartX);
                        }
                        //A3行左右间距
                        if($column){
                            $pdf->setX($textStartX+5*0.75);
                        }
                        //字数
                        for($i=0;$i<($iContent['char']*1.3);$i++){
                            //是否换行
                            if($pdf->getX()+30*0.75>$lineStartX+($x-30)*0.75){
                                $pdf->ln(38*0.75);
                                $pdf->setX($textStartX);
                                //A3行左右间距
                                if($column){
                                    $pdf->setX($textStartX+5*0.75);
                                }
                            }
                            //换页
                            if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX+5*0.75);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX+5*0.75);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            $pdf->Cell(30*0.75,30*0.75,'',1);
                            if(is_int(($i+2)/200)){
                                $pdf->SetFontSize(8*0.75);
                                $pdf->SetTextColor(228,3,127);
                                $pdf->text($pdf->getX(),$pdf->getY()+37*0.75,($i+2).iconv('utf-8','gbk','字'));
                            }
                        }
                        //最后一行
                        for($i=0;$i<30;$i++){
                            if($pdf->getX()+30*0.75>$lineStartX+($x-30)*0.75){
                                break;
                            }
                            $pdf->Cell(30*0.75,30*0.75,'',1);
                        }
                        $pdf->ln();
                    }
                    //试题结束页
                    $endPage=$pdf->PageNo();
                    //试题结束列
                    $endCol=$column;
                    //试题结束Y
                    $orderEndY=$pdf->getY();
                    //坐标信息分多少块
                    $orderCoorArr=[];
                    //不跨页，不跨列
                    if($startPage==$endPage&&$startCol==$endCol){
                        //A3
                        if($startCol){
                            $orderCoorArr[]=[
                                'x'=>round(($pdf::LEFT+10+($startCol-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                'sheet'=>$startPage
                            ];
                        }
                        //A4
                        else{
                            $orderCoorArr[]=[
                                'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                'sheet'=>$startPage
                            ];
                        }
                    }
                    //不跨页，跨列，只有A3情况
                    elseif($startPage==$endPage){
                        for($j=$startCol;$j<=$endCol;$j++){
                            //第一块
                            if($j==$startCol){
                                //A3
                                if($j){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                        'sheet'=>$startPage
                                    ];
                                }
                            }
                            //最后一块
                            elseif($j==$endCol){
                                //A3
                                if($j){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                        'sheet'=>$startPage
                                    ];
                                }
                            }
                            //中间块
                            else{
                                //A3
                                if($j){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                        'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                        'sheet'=>$startPage
                                    ];
                                }
                            }
                        }
                    }
                    //跨页
                    else{
                        //每一页
                        for($i=$startPage;$i<=$endPage;$i++){
                            //A3
                            if($startCol){
                                //第一页
                                if($i==$startPage){
                                    //每一块
                                    for($j=$startCol;$j<=3;$j++){
                                        //第一块
                                        if($j==$startCol){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                        //其他块
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                                //最后一页
                                elseif($i==$endPage){
                                    //每一块
                                    for($j=1;$j<=$endCol;$j++){
                                        //最后一块
                                        if($j==$endCol){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                'sheet'=>$i
                                            ];
                                        }
                                        //其他块
                                        else{
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                                //中间页
                                else{
                                    //每一块
                                    for($j=1;$j<=3;$j++){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$i
                                        ];
                                    }
                                }
                            }
                            //A4
                            else{
                                //第一块
                                if($i==$startPage){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($orderStartY/0.75)),
                                        'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                        'sheet'=>$i
                                    ];
                                }
                                //最后一块
                                elseif($i==$endPage){
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                        'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($orderEndY/0.75)),
                                        'sheet'=>$i
                                    ];
                                }
                                //中间块
                                else{
                                    $orderCoorArr[]=[
                                        'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                        'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                        'sheet'=>$i
                                    ];
                                }
                            }
                        }
                    }
                    //坐标信息
                    $coordinate[]=[
                        'OrderID'=>$orderIDStart++, //主观题 多选题 三选一  第一个为选择框
                        'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                        'TestOrderStart'=>$iContent['order'], //开始试题
                        'TestOrderEnd'=>$iContent['order'], //结束试题
                        'TestSmallID'=>0, //试题包含（1）等数据
                        'IfChooseTest'=>0, //是否是选做题 三选一中的一
                        'Coordinate'=>[
                            0=>$orderCoorArr //a卷
                        ]
                    ];
                    //黑框
                    $pdf->ln(10*0.75);
                    $pdf->SetDrawColor(0,0,0);
                    //要换页时的黑框高度
                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                    }
                    //正常情况的黑框高度
                    else{
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                    }
//                    //黑框
//                    $pdf->ln(10*0.75);
//                    $pdf->SetDrawColor(0,0,0);
//                    $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                }
                //英语作文
                if($iList['type']==4){
                    $pdf->setX($textStartX);
                    foreach($iList['content'] as $iContent){
                        //换页
                        if($pdf->getY()+(24+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                            //完成之前黑框
                            $pdf->SetDrawColor(0,0,0);
                            $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                            //A3
                            if($column){
                                //需要换页
                                if($column==3){
                                    $column=1;
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                                //换列
                                else{
                                    $column++;
                                    $pdf->SetY(($pdf::TOP+16)*0.75);
                                    $columnX=$column*$x*0.75;//列开始X
                                    $lineStartX+=$x*0.75;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $chooseStartX=$textStartX;
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //A4
                            else{
                                $pdf->AddPage();
                                $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                $textStartX=$lineStartX+10*0.75;//文字开始X
                                $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                $pdf->setX($textStartX);
                                $imgStartY=$pdf->getY();//图片开始Y
                            }
                        }
                        //试题开始页
                        $startPage=$pdf->PageNo();
                        //试题开始列
                        $startCol=$column;
                        //试题开始Y
                        $orderStartY=$pdf->getY();
                        //题号
                        $order=$iContent['order'];
                        if($iContent['small']) $order.='('.$iContent['small'].')';//小题号
                        if($score) $order.='('.$iContent['score'].'分)';
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetFontSize(12*0.75);
                        $pdf->Cell(60*0.75,24*0.75,iconv('utf-8','gbk',$order),0,2,'L');
                        //试题描述
                        if($iContent['desc']){
                            //处理换行标签
                            $desc=$this->delHtml($iContent['desc']);
                            //获取描述高度
                            $descHeight=$this->getHeight($pdf,$desc,$x);
                            //换页
                            if($pdf->getY()+$strHeight>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            //写入试题描述
                            $this->writeDesc($pdf,$desc,$x,$textStartX,$iContent['hline']);
                            $pdf->setX($textStartX);
                        }
                        //下划线
                        $uline=0;
                        if($iContent['uline']){
                           $uline='B';
                        }
                        //空
                        //如果空小于3行，给三行
                        if($iContent['hline']<3){
                            $iContent['hline']=3;
                        }
                        for($i=0;$i<$iContent['hline'];$i++){
                            //换页
                            if($pdf->getY()+($row+10)*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                                //完成之前黑框
                                $pdf->SetDrawColor(0,0,0);
                                $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                                //A3
                                if($column){
                                    //需要换页
                                    if($column==3){
                                        $column=1;
                                        $pdf->AddPage();
                                        $lineStartX=$pdf::LEFT*0.75+10*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                    //换列
                                    else{
                                        $column++;
                                        $pdf->SetY(($pdf::TOP+16)*0.75);
                                        $columnX=$column*$x*0.75;//列开始X
                                        $lineStartX+=$x*0.75;//黑框开始X
                                        $textStartX=$lineStartX+10*0.75;//文字开始X
                                        $chooseStartX=$textStartX;
                                        $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                        $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                        $pdf->setX($textStartX);
                                        $imgStartY=$pdf->getY();//图片开始Y
                                    }
                                }
                                //A4
                                else{
                                    $pdf->AddPage();
                                    $lineStartX=$pdf::LEFT*0.75+10*0.75+$columnX;//黑框开始X
                                    $textStartX=$lineStartX+10*0.75;//文字开始X
                                    $lineStartY=$lineEndY=$pdf->getY();//黑框开始Y
                                    $pdf->setY($lineStartY+10*0.75);//文字开始Y
                                    $pdf->setX($textStartX);
                                    $imgStartY=$pdf->getY();//图片开始Y
                                }
                            }
                            $hwidth=$hline;
                            if($i==0){
                                if(!$iContent['desc']){
                                    $hwidth=$hline-($pdf->getX()-$textStartX);
                                }
                            }
                            $pdf->Cell(($x-40)*0.75,$row*0.75,'',$uline,1);
                            //是否有图片
                            if($iContent['img'][$imgNum]){
                                $kongY=$pdf->getY();
                                $imgSize=getimagesize($iContent['img'][$imgNum][0]);
                                //能否放下图片
                                if($pdf->getY()-$imgStartY>($imgSize[1]+18)*0.75){
                                    $pdf->SetFillColor(255,255,255);
                                    $pdf->Rect($lineStartX+($x-20-10-$imgSize[0]-10)*0.75,$imgStartY,10*0.75,($imgSize[1]+18)*0.75,'F');
                                    $pdf->Image($iContent['img'][$imgNum][0],$lineStartX+($x-20-10-$imgSize[0])*0.75,$imgStartY);
                                    $pdf->setY($imgStartY+$imgSize[1]*0.75);
                                    $pdf->setX($lineStartX+($x-20-10-$imgSize[0])*0.75);
                                    $pdf->SetTextColor(0,0,0);
                                    $pdf->Cell($imgSize[0]*0.75+1,18*0.75,iconv('utf-8','gbk',$iContent['img'][$imgNum][1]),0,1,'C',1);
                                    $imgStartY=$pdf->getY();
                                    $pdf->setY($kongY);
                                    $imgNum++;
                                }
                            }
                            $pdf->setX($textStartX);
                        }
                        //试题结束页
                        $endPage=$pdf->PageNo();
                        //试题结束列
                        $endCol=$column;
                        //试题结束Y
                        $orderEndY=$pdf->getY();
                        //坐标信息分多少块
                        $orderCoorArr=[];
                        //不跨页，不跨列
                        if($startPage==$endPage&&$startCol==$endCol){
                            //A3
                            if($startCol){
                                $orderCoorArr[]=[
                                    'x'=>round(($pdf::LEFT+10+($startCol-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                    'y'=>round(($pdf::LEFT+10+($startCol-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                    'sheet'=>$startPage
                                ];
                            }
                            //A4
                            else{
                                $orderCoorArr[]=[
                                    'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*$orderStartY/0.75),
                                    'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                    'sheet'=>$startPage
                                ];
                            }
                        }
                        //不跨页，跨列，只有A3情况
                        elseif($startPage==$endPage){
                            for($j=$startCol;$j<=$endCol;$j++){
                                //第一块
                                if($j==$startCol){
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                                //最后一块
                                elseif($j==$endCol){
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                                //中间块
                                else{
                                    //A3
                                    if($j){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$startPage
                                        ];
                                    }
                                }
                            }
                        }
                        //跨页
                        else{
                            //每一页
                            for($i=$startPage;$i<=$endPage;$i++){
                                //A3
                                if($startCol){
                                    //第一页
                                    if($i==$startPage){
                                        //每一块
                                        for($j=$startCol;$j<=3;$j++){
                                            //第一块
                                            if($j==$startCol){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*$orderStartY/0.75),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                            //其他块
                                            else{
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //最后一页
                                    elseif($i==$endPage){
                                        //每一块
                                        for($j=1;$j<=$endCol;$j++){
                                            //最后一块
                                            if($j==$endCol){
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*$orderEndY/0.75),
                                                    'sheet'=>$i
                                                ];
                                            }
                                            //其他块
                                            else{
                                                $orderCoorArr[]=[
                                                    'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                    'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                    'sheet'=>$i
                                                ];
                                            }
                                        }
                                    }
                                    //中间页
                                    else{
                                        //每一块
                                        for($j=1;$j<=3;$j++){
                                            $orderCoorArr[]=[
                                                'x'=>round(($pdf::LEFT+10+($j-1)*$x)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                                'y'=>round(($pdf::LEFT+10+($j-1)*$x+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                                'sheet'=>$i
                                            ];
                                        }
                                    }
                                }
                                //A4
                                else{
                                    //第一块
                                    if($i==$startPage){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($orderStartY/0.75)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$i
                                        ];
                                    }
                                    //最后一块
                                    elseif($i==$endPage){
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($orderEndY/0.75)),
                                            'sheet'=>$i
                                        ];
                                    }
                                    //中间块
                                    else{
                                        $orderCoorArr[]=[
                                            'x'=>round(($pdf::LEFT+10)*$pixel).','.round($pixel*($pdf::TOP+16)),
                                            'y'=>round(($pdf::LEFT+10+($x-20))*$pixel).','.round($pixel*($pdf::HIGH-$pdf::TOP-16)),
                                            'sheet'=>$i
                                        ];
                                    }
                                }
                            }
                        }
                        //坐标信息
                        $coordinate[]=[
                            'OrderID'=>$orderIDStart++, //主观题 多选题 三选一  第一个为选择框
                            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
                            'TestOrderStart'=>$iContent['order'], //开始试题
                            'TestOrderEnd'=>$iContent['order'], //结束试题
                            'TestSmallID'=>0, //试题包含（1）等数据
                            'IfChooseTest'=>0, //是否是选做题 三选一中的一
                            'Coordinate'=>[
                                0=>$orderCoorArr //a卷
                            ]
                        ];
                    }
                    //黑框
                    $pdf->ln(10*0.75);
                    $pdf->SetDrawColor(0,0,0);
                    //要换页时的黑框高度
                    if($pdf->getY()+34*0.75>($pdf::HIGH-$pdf::TOP-16)*0.75){
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16)*0.75-$lineStartY);
                    }
                    //正常情况的黑框高度
                    else{
                        $pdf->Rect($lineStartX,$lineStartY,($x-20)*0.75,$pdf->getY()-$lineStartY);
                    }
                }
            }
            $pdf->setX($textStartX);
        }
        //禁答区
        $pdf->ln(10*0.75);
        $pdf->SetDrawColor(0,0,0);
        //A3
        if($column){
            $startCol=$column;
            for($i=$column;$i<4;$i++){
                if($i!=$startCol){
                    $pdf->SetY(($pdf::TOP+16)*0.75);
                }
                $pdf->setX(($pdf::LEFT+10+($column-1)*$x)*0.75);
                $pdf->SetFontSize(($pdf::HIGH*0.75-$pdf->getY())/16);
                if($pdf::HIGH*0.75-$pdf->getY()>$pdf::TOP+20){
                    $pdf->cell(($x-20)*0.75,($pdf::HIGH-$pdf::TOP-16-4)*0.75-$pdf->getY(),iconv('utf-8','gbk','此区域禁止作答'),1,0,'C');
                }
                $column++;
            }
        }
        //A4
        else{
            $pdf->setX($lineStartX);
            $pdf->SetFontSize(($pdf::HIGH*0.75-$pdf->getY())/15);
            if($pdf::HIGH*0.75-$pdf->getY()>$pdf::TOP+20){
                $pdf->cell(($x-20)*0.75,($pdf::HIGH-$pdf::TOP-20)*0.75-$pdf->getY(),iconv('utf-8','gbk','此区域禁止作答'),1,0,'C');
            }
        }
        return $coordinate;
    }

    /**
     * 获取客观题选项数量
     * @param int $style 选项类型
     * @param int $num 选项数量
     * @return int $num 选项数量
     * @author demo
     */
    public function getOptionNum($style,$num){
        //对错
        if($style==3){
            if($num==0||$num>2){
                return 2;
            }
        }
        //字母选项
        else{
            if($num==0||$num>9){
                return 4;
            }
        }
        return $num;
    }

        //填空题多题一行坐标信息
    public function getCoorTK($pdf,$orderIDStart,$testStart,$testEnd,$lineStartX,$row){
        $coordinate=[
            'OrderID'=>$orderIDStart++,
            'Style'=>3,//默认0考号 1缺考 2客观题 3主观题 4页面标示
            'TestOrderStart'=>$iList['content'][$k]['order'], //开始试题
            'TestOrderEnd'=>$iList['content'][$k+1]['order'], //结束试题
            'TestSmallID'=>0, //试题包含（1）等数据
            'IfChooseTest'=>0, //是否是选做题
            'Coordinate'=>[
                0=>[ //a卷
                    0=>[ // 默认客观题5题为一个块
                        'x'=>round($lineStartX/0.75*$pixel).','.round($pixel*$pdf->getY()/0.75),
                        'y'=>round(($lineStartX/0.75+($x-20))*$pixel).','.round($pixel*($pdf->getY()/0.75+$row)),
                        'sheet'=>$pdf->PageNo()
                    ]
                ]
            ]
        ];
        return $coordinate;
    }

    //试题描述处理换行标签
    public function delHtml($desc){
        $desc=str_ireplace(['<p>',"\r\n"],'',$desc);
        $desc=str_ireplace('</p>','<br>',$desc);
        $desc=explode('<br>',$desc);
        return $desc;
    }
    //获取描述高度
    public function getHeight($pdf,$desc,$x){
        $descHeight=0;//描述高度
        foreach($desc as $iDesc){
            $iDesc=chop($iDesc);//去除右侧空格
            if(!empty($iDesc)){
                $strWidth=$pdf->GetStringWidth($iDesc);//描述宽度
                $strHeight=ceil($strWidth/(($x-40)*0.75))*24*0.75;//描述高度
                //短文改错行高
                if(!$hline){
                    $strHeight=ceil($strWidth/(($x-40)*0.75))*$pdf::ROW*0.75;//高度
                }
                $descHeight+=$strHeight;
            }
        }
        return $descHeight;
    }
    //写入试题描述部分
    public function writeDesc($pdf,$desc,$x,$textStartX,$hline){
        $pdf->SetFontSize(12*0.75);
        foreach($desc as $iDesc){
            $iDesc=chop($iDesc);//去除右侧空格
            if(!empty($iDesc)){
                $pdf->setX($textStartX);
                if($hline){
                    $pdf->MultiCell(($x-40)*0.75,24*0.75,iconv('utf-8','gbk',$iDesc));
                }
                //短文改错
                else{
                    $pdf->MultiCell(($x-40)*0.75,$pdf::ROW*0.75,iconv('utf-8','gbk',$iDesc));
                }
                $pdf->setX($textStartX);
            }
        }
    }

    /**
     * 获取页面大小
     * @param str $layout 版式
     * @return object 绘画对象
     * @author demo
     */
    public function getlayout($layout='A4'){
        if($layout=='A4'){
            return new PDF('P','pt','A4');
        }
        return new PDF('L','pt','A3');
    }

    /**
     * 获取顶部信息
     * @param object $pdf 绘画对象
     * @param int $x 一栏的宽度
     * @param arr $top 顶部信息
     * @param arr $title 标题
     * @param arr $sub 副标题
     * @author demo
     */
    public function getTop($pdf,$x,$top,$title,$sub){
        $pdf->SetFont('GB-hw','',14*0.75);
        $pdf->setX($pdf::LEFT*0.75);
        //顶部信息
        if($top['display']){
            $pdf->MultiCell($x*0.75,24*0.75,iconv('utf-8','gbk',$top['content']),0,'C');
        }
        //标题
        if($title['display']){
            $pdf->SetFontSize(22*0.75);
            $pdf->SetTextColor(228,3,127);
            $pdf->MultiCell($x*0.75,42*0.75,iconv('UTF-8','gbk',$title['content']),0,'C');
        }
        //副标题
        if($sub['display']){
            $pdf->SetTextColor(0,0,0);
            $pdf->MultiCell($x*0.75,22*0.75,iconv('UTF-8','gbk',$sub['content']),0,'C');
        }
    }

    /**
     * 获取头部信息
     * @param object $pdf 绘画对象
     * @param int $x 一栏的宽度
     * @param arr $type 顶部类型
     * @param arr $code 条形码
     * @param arr $num 准考证号
     * @param arr $care 注意事项
     * @param arr $miss 缺考标记
     * @author demo
     */
    public function getType($pdf,$x,$type=1,$code,$num,$care,$miss,$pixel){
        //考号，条形码坐标信息
        $coordinate['code']=[
            'OrderID'=>1,
            'Style'=>0,//0考号 1缺考 2客观题 3主观题 4页面标示
            'TestOrderStart'=>0,
            'TestOrderEnd'=>0,
            'TestSmallID'=>0,
            'IfChooseTest'=>0,
        ];
        //缺考标记坐标信息
        $coordinate['miss']=[
            'OrderID'=>2,
            'Style'=>1,//0考号 1缺考 2客观题 3主观题 4页面标示
            'TestOrderStart'=>0,
            'TestOrderEnd'=>0,
            'TestSmallID'=>0,
            'IfChooseTest'=>0,
        ];
        //头部写完后的Y坐标
        $nowY=0;
        //A4
        if($x==0){
            $startY=$pdf->getY();
            if($type==1){
//                $startY=$pdf->getY();
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::WIDTH_A4-$pdf::LEFT-231)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+89*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::WIDTH_A4-$pdf::LEFT-231)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::WIDTH_A4-$pdf::LEFT)*$pixel).','.round(($nowY/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->Cell(56*0.75,32*0.75,iconv('UTF-8','gbk','准考证号'),0,0);
                    $pdf->setX($pdf->getX()+4*0.75);
                    //准考证号框
                    $pdf->SetDrawColor(228,3,127);
                    for($i=0;$i<$num['length'];$i++){
                        $pdf->setX($pdf->getX()+4*0.75);
                        $pdf->cell(27*0.75,32*0.75,'',1);
                    }
//                    //坐标信息
//                    $coordinate['code']['Coordinate'][0][]=[
//                        'x'=>round(($pdf->getX()/0.75-31*$num['length']-60)).','.round(($pdf->getY()/0.75),
//                        'y'=>round(($pdf->getX()/0.75)).','.round(($pdf->getY()/0.75+32),
//                        'sheet'=>1,
//                        'id'=>2
//                    ];
                    $pdf->Ln(42*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,17*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,17*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //缺考标记
                if($miss['display']){
                    if($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY+100*0.75,231*0.75,42*0.75);
                        if($nowY<$startY+(100+42+10)*0.75) $nowY=$startY+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY,231*0.75,42*0.75);
                        if($nowY<$startY+(42+10)*0.75) $nowY=$startY+(42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                }
            }
            if($type==2){
//                $startY=$pdf->getY();
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+89*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::WIDTH_A4-$pdf::LEFT-231)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::WIDTH_A4-$pdf::LEFT)*$pixel).','.round(($nowY/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //涂写准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->SetDrawColor(228,3,127);
//                    $pdf->setX($pdf->getX());
                    //起始坐标信息
//                    $coordinate['code']['Coordinate'][0][1]['x']=($pdf->getX()/0.75)).','.round(($pdf->getY()/0.75);
                    //准考证号标题
                    $pdf->MultiCell(34*$num['length']*0.75,37*0.75,iconv('UTF-8','gbk','准考证号'),1,'C');
//                    $pdf->setX($pdf->getX());
                    //准考证号手写框
                    $numStartY=$pdf->getY();
                    for($i=0;$i<$num['length'];$i++){
//                        $pdf->Line($pdf->getX(),$pdf->getY(),$pdf->getX(),$pdf->getY()+(32+240)*0.75);
                        $pdf->setY($numStartY);
                        $pdf->setX($pdf->getX()+(34*$i)*0.75);
                        $numStartX=$pdf->getX();
                        $pdf->Cell(34*0.75,37*0.75,'','LBR',2);
//                        $pdf->setX($pdf->getX()+5*0.75);
                        $pdf->SetFontSize(12*0.75);
                        for($j=0;$j<10;$j++){
                            $border='';
                            if($j==9) $border='B';
                            $pdf->Cell(7*0.75,22*0.75,'[','L'.$border,0,'L');
                            $pdf->Cell(20*0.75,22*0.75,iconv('utf-8','gbk',$j),$border,0,'C');
                            $pdf->Cell(7*0.75,22*0.75,']','R'.$border,0,'R');
                            $pdf->ln();
                            $pdf->setX($numStartX);
//                            $pdf->Cell(34*0.75,22*0.75,'['.$j.']',$border,2,'C');
                        }
                    }
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round($pdf::LEFT*$pixel).','.round(($numStartY/0.75+37)*$pixel),
                        'y'=>round(($pdf->getX()/0.75+34)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>2,
                        "sub"=>[
                            'x'=>round(($pdf::LEFT+3)*$pixel).','.round(($numStartY/0.75+37+3)*$pixel),//第一个矩形的左上
                            'y'=>round(($pdf::LEFT+3+31)*$pixel).','.round(($numStartY/0.75+37+3+19)*$pixel),//第一个矩形的右下
                            'z'=>round(34*$pixel).','.round(22*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                            'xn'=>$num['length'], //水平数量
                            'yn'=>10, //垂直数量
                            'd'=>1, //选项方向 0横 1纵
                        ]
                    ];
                    $pdf->Ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //注意事项
                if($care['display']){
                    if($code['display']){
                        $pdf->setY($startY+99*0.75);
                    }
                    else{
                        $pdf->setY($startY);
                    }
                    $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->setDrawColor(228,3,127);
                    $pdf->MultiCell(231*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(231*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                            $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                        }
                        else{
                            $pdf->MultiCell(231*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                            $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //缺考标记
                if($miss['display']){
                    if($care['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$pdf->getY(),231*0.75,42*0.75);
                        if($nowY<$pdf->getY()+(10+42)*0.75) $nowY=$pdf->getY()+(10+42)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    elseif($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY+100*0.75,231*0.75,42*0.75);
                        if($nowY<$startY+(100+42)*0.75) $nowY=$startY+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY,231*0.75,42*0.75);
                        if($nowY<$startY+(42+10)*0.75) $nowY=$startY+(42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                }
            }
            if($type==3){
//                $startY=$pdf->getY();
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+99*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::WIDTH_A4-$pdf::LEFT-231)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::WIDTH_A4-$pdf::LEFT)*$pixel).','.round(($pdf->getY()/0.75+89)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2-231-10)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //缺考标记
                if($miss['display']){
                    if($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY+100*0.75,231*0.75,42*0.75);
                        if($nowY<$startY+(100+42+10)*0.75) $nowY=$startY+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY,231*0.75,42*0.75);
                        if($nowY<$startY+(42+10)*0.75) $nowY=$startY+(42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                }
            }
            if($type==4){
                $startY=$pdf->getY();
//                //准考证号
//                if($num['display']){
//                    $pdf->SetFontSize(14*0.75);
//                    $pdf->SetTextColor(228,3,127);
//                    $pdf->Cell(56*0.75,32*0.75,iconv('UTF-8','gbk','准考证号'),0,0);
//                    $pdf->setX($pdf->getX()+4*0.75);
//                    //准考证号框
//                    $pdf->SetDrawColor(228,3,127);
//                    for($i=0;$i<$num['length'];$i++){
//                        $pdf->setX($pdf->getX()+4*0.75);
//                        $pdf->cell(27*0.75,32*0.75,'',1);
//                    }
////                    //坐标信息
////                    $coordinate['code']['Coordinate'][0][]=[
////                        'x'=>round(($pdf->getX()/0.75-31*$num['length']-60)).','.round(($pdf->getY()/0.75),
////                        'y'=>round(($pdf->getX()/0.75)).','.round(($pdf->getY()/0.75+32),
////                        'sheet'=>1,
////                        'id'=>2
////                    ];
//                    if($miss['display']){
//                        $pdf->Ln(52*0.75);
//                    }
//                    else{
//                        $pdf->Ln(42*0.75);
//                    }
//                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
//                }
//                //注意事项
//                if($care['display']){
//                    $contentArr=explode('\n', $care['content']);
//                    //注意事项标题
//                    $pdf->SetFontSize(14*0.75);
//                    $pdf->SetTextColor(0,0,0);
//                    $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2)*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
//                    //注意事项内容
//                    $pdf->SetFontSize(12*0.75);
//                    $pdf->SetTextColor(228,3,127);
//                    foreach($contentArr as $i=>$iContent){
//                        if($i+1==count($contentArr)){
//                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
//                        }
//                        else{
//                            $pdf->MultiCell(($pdf::WIDTH_A4-$pdf::LEFT*2)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
//                        }
//                    }
//                    $pdf->ln(10*0.75);
//                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
//                }
//                //缺考标记
//                if($miss['display']){
//                    $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY,231*0.75,42*0.75);
//                    if($nowY<$startY+(42+10)*0.75) $nowY=$startY+(42+10)*0.75;
//                    //坐标信息
//                    $coordinate['miss']['Coordinate'][0][]=[
//                        'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+17)*$pixel),
//                        'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+27)*$pixel),
//                        'sheet'=>1
//                    ];
//                }
                //涂写准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->SetDrawColor(228,3,127);
//                    $pdf->setX($pdf->getX());
                    //起始坐标信息
//                    $coordinate['code']['Coordinate'][0][1]['x']=($pdf->getX()/0.75)).','.round(($pdf->getY()/0.75);
                    //准考证号标题
                    $pdf->MultiCell(34*$num['length']*0.75,37*0.75,iconv('UTF-8','gbk','准考证号'),1,'C');
//                    $pdf->setX($pdf->getX());
                    //准考证号手写框
                    $numStartY=$pdf->getY();
                    for($i=0;$i<$num['length'];$i++){
//                        $pdf->Line($pdf->getX(),$pdf->getY(),$pdf->getX(),$pdf->getY()+(32+240)*0.75);
                        $pdf->setY($numStartY);
                        $pdf->setX($pdf->getX()+(34*$i)*0.75);
                        $numStartX=$pdf->getX();
                        $pdf->Cell(34*0.75,37*0.75,'','LBR',2);
//                        $pdf->setX($pdf->getX()+5*0.75);
                        $pdf->SetFontSize(12*0.75);
                        for($j=0;$j<10;$j++){
                            $border='';
                            if($j==9) $border='B';
                            $pdf->Cell(7*0.75,22*0.75,'[','L'.$border,0,'L');
                            $pdf->Cell(20*0.75,22*0.75,iconv('utf-8','gbk',$j),$border,0,'C');
                            $pdf->Cell(7*0.75,22*0.75,']','R'.$border,0,'R');
                            $pdf->ln();
                            $pdf->setX($numStartX);
//                            $pdf->Cell(34*0.75,22*0.75,'['.$j.']',$border,2,'C');
                        }
                    }
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round($pdf::LEFT*$pixel).','.round(($numStartY/0.75+37)*$pixel),
                        'y'=>round(($pdf->getX()/0.75+34)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>2,
                        "sub"=>[
                            'x'=>round(($pdf::LEFT+3)*$pixel).','.round(($numStartY/0.75+37+3)*$pixel),//第一个矩形的左上
                            'y'=>round(($pdf::LEFT+3+31)*$pixel).','.round(($numStartY/0.75+37+3+19)*$pixel),//第一个矩形的右下
                            'z'=>round(34*$pixel).','.round(22*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                            'xn'=>$num['length'], //水平数量
                            'yn'=>10, //垂直数量
                            'd'=>1, //选项方向 0横 1纵
                        ]
                    ];
                    $pdf->Ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //注意事项
                if($care['display']){
                    $pdf->setY($startY);
                    $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->setDrawColor(228,3,127);
                    $pdf->MultiCell(231*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(231*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                            $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                        }
                        else{
                            $pdf->MultiCell(231*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                            $pdf->setX(($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75);
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //缺考标记
                if($miss['display']){
                    if($care['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$pdf->getY(),231*0.75,42*0.75);
                        if($nowY<$pdf->getY()+(10+42)*0.75) $nowY=$pdf->getY()+(10+42)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    elseif($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY+100*0.75,231*0.75,42*0.75);
                        if($nowY<$startY+(100+42)*0.75) $nowY=$startY+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+100+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::WIDTH_A4-231-$pdf::LEFT)*0.75,$startY,231*0.75,42*0.75);
                        if($nowY<$startY+(42+10)*0.75) $nowY=$startY+(42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::WIDTH_A4-31-$pdf::LEFT)*$pixel).','.round(($startY/0.75+17)*$pixel),
                            'y'=>round(($pdf::WIDTH_A4-11-$pdf::LEFT)*$pixel).','.round(($startY/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                }
            }
        }
        //A3
        else{
            if($type==1){
                //准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->setX($pdf->getX()+5*0.75);
                    $pdf->Cell(56*0.75,32*0.75,iconv('UTF-8','gbk','准考证号'),0,0);
                    $pdf->setX($pdf->getX()+4*0.75);
                    //准考证号框
                    $pdf->SetDrawColor(228,3,127);
                    for($i=0;$i<$num['length'];$i++){
                        $pdf->setX($pdf->getX()+4*0.75);
                        $pdf->cell(27*0.75,32*0.75,'',1);
                    }
//                    //坐标信息
//                    $coordinate['code']['Coordinate'][0][]=[
//                        'x'=>round(($pdf->getX()/0.75-31*$num['length']-60)).','.round(($pdf->getY()/0.75),
//                        'y'=>round(($pdf->getX()/0.75)).','.round(($pdf->getY()/0.75+32),
//                        'sheet'=>1,
//                        'id'=>2
//                    ];
                    $pdf->Ln(42*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                $startX=$pdf->getX()+5*0.75;//注意事项开始x
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+89*0.75;
                    $startX+=(231+10)*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::LEFT+5+231)*$pixel).','.round(($pdf->getY()/0.75+89)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //缺考标记
                if($miss['display']){
                    if($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY()+100*0.75,231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+100+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(42+10)*0.75;
                        $startX+=(231+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                            'sheet'=>1
                        ];
                    }
                }
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->setX($startX);
                    $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$pdf->getX(),32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        $pdf->setX($startX);
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$pdf->getX(),16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$pdf->getX(),16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->Ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
            }
            if($type==2){
                //准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->setX($pdf->getX()+5*0.75);
                    //准考证号标题
                    $pdf->MultiCell(34*$num['length']*0.75,37*0.75,iconv('UTF-8','gbk','准考证号'),1,'C');
                    //准考证号手写框
                    $numStartY=$pdf->getY();
                    for($i=0;$i<$num['length'];$i++){
                        $pdf->setY($numStartY);
                        $pdf->setX($pdf->getX()+(34*$i+5)*0.75);
                        $numStartX=$pdf->getX();
                        $pdf->Cell(34*0.75,37*0.75,'','LBR',2);
                        $pdf->SetFontSize(12*0.75);
                        for($j=0;$j<10;$j++){
                            $border='';
                            if($j==9) $border='B';
                            $pdf->Cell(7*0.75,22*0.75,'[','L'.$border,0,'L');
                            $pdf->Cell(20*0.75,22*0.75,iconv('utf-8','gbk',$j),$border,0,'C');
                            $pdf->Cell(7*0.75,22*0.75,']','R'.$border,0,'R');
                            $pdf->ln();
                            $pdf->setX($numStartX);
                        }
                    }
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5)*$pixel).','.round(($numStartY/0.75+37)*$pixel),
                        'y'=>round(($pdf->getX()/0.75+34)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>2,
                        "sub"=>[
                            'x'=>round(($pdf::LEFT+5+3)*$pixel).','.round(($numStartY/0.75+37+3)*$pixel),//第一个矩形的左上
                            'y'=>round(($pdf::LEFT+5+3+31)*$pixel).','.round(($numStartY/0.75+37+3+19)*$pixel),//第一个矩形的右下
                            'z'=>round(34*$pixel).','.round(22*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                            'xn'=>$num['length'], //水平数量
                            'yn'=>10, //垂直数量
                            'd'=>1, //选项方向 0横 1纵
                        ]
                    ];
                    //间距
                    $pdf->Ln(10*0.75);
                }
                if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                $startX=$pdf->getX()+5*0.75;//注意事项开始x
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+89*0.75;
                    $startX+=(231+10)*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::LEFT+5+231)*$pixel).','.round(($pdf->getY()/0.75+89)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //缺考标记
                if($miss['display']){
                    if($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY()+100*0.75,231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+100+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(42+10)*0.75;
                        $startX+=(231+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                }
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->setX($startX);
                    $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        $pdf->setX($startX);
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
            }
            if($type==3){
                $startX=$pdf->getX()+5*0.75;//注意事项开始x
                //条形码区域
                if($code['display']){
                    $pdf->Image($pdf::IMG_URL.'barCode.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,89*0.75);
                    $nowY=$pdf->getY()+89*0.75;
                    $startX+=(231+10)*0.75;
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'y'=>round(($pdf::LEFT+5+231)*$pixel).','.round(($pdf->getY()/0.75+89)*$pixel),
                        'sheet'=>1,
                        'id'=>1
                    ];
                }
                //缺考标记
                if($miss['display']){
                    if($code['display']){
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY()+100*0.75,231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(100+42+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+100+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+100+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                    else{
                        $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,42*0.75);
                        $nowY=$pdf->getY()+(42+10)*0.75;
                        $startX+=(231+10)*0.75;
                        //坐标信息
                        $coordinate['miss']['Coordinate'][0][]=[
                            'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                            'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                            'sheet'=>1,
                        ];
                    }
                }
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->setX($startX);
                    $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        $pdf->setX($startX);
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($x+$pdf::LEFT-5)*0.75-$startX,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
            }
            if($type==4){
                //准考证号
                if($num['display']){
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(228,3,127);
                    $pdf->SetDrawColor(228,3,127);
                    $pdf->setX($pdf->getX()+5*0.75);
                    //准考证号标题
                    $pdf->MultiCell(34*$num['length']*0.75,37*0.75,iconv('UTF-8','gbk','准考证号'),1,'C');
                    //准考证号手写框
                    $numStartY=$pdf->getY();
                    for($i=0;$i<$num['length'];$i++){
                        $pdf->setY($numStartY);
                        $pdf->setX($pdf->getX()+(34*$i+5)*0.75);
                        $numStartX=$pdf->getX();
                        $pdf->Cell(34*0.75,37*0.75,'','LBR',2);
                        $pdf->SetFontSize(12*0.75);
                        for($j=0;$j<10;$j++){
                            $border='';
                            if($j==9) $border='B';
                            $pdf->Cell(7*0.75,22*0.75,'[','L'.$border,0,'L');
                            $pdf->Cell(20*0.75,22*0.75,iconv('utf-8','gbk',$j),$border,0,'C');
                            $pdf->Cell(7*0.75,22*0.75,']','R'.$border,0,'R');
                            $pdf->ln();
                            $pdf->setX($numStartX);
                        }
                    }
                    //坐标信息
                    $coordinate['code']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5)*$pixel).','.round(($numStartY/0.75+37)*$pixel),
                        'y'=>round(($pdf->getX()/0.75+34)*$pixel).','.round(($pdf->getY()/0.75)*$pixel),
                        'sheet'=>1,
                        'id'=>2,
                        "sub"=>[
                            'x'=>round(($pdf::LEFT+5+3)*$pixel).','.round(($numStartY/0.75+37+3)*$pixel),//第一个矩形的左上
                            'y'=>round(($pdf::LEFT+5+3+31)*$pixel).','.round(($numStartY/0.75+37+3+19)*$pixel),//第一个矩形的右下
                            'z'=>round(34*$pixel).','.round(22*$pixel), //矩形与矩形之间的间隔'水平距离,垂直距离'
                            'xn'=>$num['length'], //水平数量
                            'yn'=>10, //垂直数量
                            'd'=>1, //选项方向 0横 1纵
                        ]
                    ];
                    //间距
                    $pdf->Ln(10*0.75);
                }
                if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                $startX=$pdf->getX()+5*0.75;//注意事项开始x
                //注意事项
                if($care['display']){
                    $contentArr=explode('\n', $care['content']);
                    //注意事项标题
                    $pdf->SetFontSize(14*0.75);
                    $pdf->SetTextColor(0,0,0);
                    $pdf->setX($startX);
                    $pdf->MultiCell(($x-10)*0.75,32*0.75,iconv('UTF-8','gbk','注意事项'),'LTR');
                    //注意事项内容
                    $pdf->SetFontSize(12*0.75);
                    $pdf->SetTextColor(228,3,127);
                    foreach($contentArr as $i=>$iContent){
                        $pdf->setX($startX);
                        if($i+1==count($contentArr)){
                            $pdf->MultiCell(($x-10)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LRB');
                        }
                        else{
                            $pdf->MultiCell(($x-10)*0.75,16*0.75,iconv('UTF-8','gbk',$iContent),'LR');
                        }
                    }
                    $pdf->ln(10*0.75);
                    if($nowY<$pdf->getY()) $nowY=$pdf->getY();
                }
                //缺考标记
                if($miss['display']){
                    $pdf->Image($pdf::IMG_URL.'miss.png',($pdf::LEFT+5)*0.75,$pdf->getY(),231*0.75,42*0.75);
                    $nowY=$pdf->getY()+(42+10)*0.75;
                    //坐标信息
                    $coordinate['miss']['Coordinate'][0][]=[
                        'x'=>round(($pdf::LEFT+5+200)*$pixel).','.round(($pdf->getY()/0.75+17)*$pixel),
                        'y'=>round(($pdf::LEFT+5+220)*$pixel).','.round(($pdf->getY()/0.75+27)*$pixel),
                        'sheet'=>1,
                    ];
                }
            }
        }
        $pdf->setY($nowY);
        return $coordinate;
    }

    /**
     * 获取红色圆角矩形
     * @param Object $pdf 绘画对象
     * @param int $x 一栏宽度
     */
    public function getRedBorder($pdf,$x){
        $pdf->SetDrawColor(228,3,127);
        //作答区域红色上边框
        $width=($pdf::WIDTH_A4-$pdf::LEFT*2-14)*0.75;//A4线宽
        $startX=$pdf->getX();
        //A3
        if($x){
            $width=($x-10-14)*0.75;//A3
            $startX=$pdf->getX()+5*0.75;
        }
        //上边框
        $pdf->Image($pdf::IMG_URL.'tl.png',$startX,$pdf->getY(),7*0.75,7*0.75);
        $pdf->Line($startX+7*0.75,$pdf->getY(),$startX+7*0.75+$width,$pdf->getY());
        $pdf->Image($pdf::IMG_URL.'tr.png',$startX+7*0.75+$width,$pdf->getY(),7*0.75,7*0.75);
        //作答区域左右竖线
        $pdf->Line($startX,$pdf->getY()+7*0.75,$startX,($pdf::HIGH-$pdf::TOP)*0.75-7*0.75);
        $pdf->Line($startX+$width+14*0.75,$pdf->getY()+7*0.75,$startX+$width+14*0.75,($pdf::HIGH-$pdf::TOP)*0.75-7*0.75);
        //上边框
        $pdf->Image($pdf::IMG_URL.'bl.png',$startX,($pdf::HIGH-$pdf::TOP)*0.75-7*0.75,7*0.75,7*0.75);
        $pdf->Line($startX+7*0.75,($pdf::HIGH-$pdf::TOP)*0.75,$startX+7*0.75+$width,($pdf::HIGH-$pdf::TOP)*0.75);
        $pdf->Image($pdf::IMG_URL.'br.png',$startX+7*0.75+$width,($pdf::HIGH-$pdf::TOP)*0.75-7*0.75,7*0.75,7*0.75);
        //作答区域红色下边框
//        $pdf->Image('../www/Public/static/images/border_b.png',$x,$y+$height-7*0.75,704*0.75,7*0.75);
    }
//    public function getRedBorder($pdf,$x,$y,$height){
//        //作答区域红色上边框
//        $pdf->Image('../www/Public/static/images/border_t.png',$x,$y,704*0.75,7*0.75);
//        //作答区域左右竖线
//        $pdf->SetDrawColor(228,3,127);
//        $pdf->Line($x,$y+7*0.75,$x,$y+$height-7*0.75);
//        $pdf->Line($x+704*0.75,$y+7*0.75,$x+704*0.75,$y+$height-7*0.75);
//        //作答区域红色下边框
//        $pdf->Image('../www/Public/static/images/border_b.png',$x,$y+$height-7*0.75,704*0.75,7*0.75);
//    }

    //作答区域黑框
    public function getBlackRect($pdf,$x,$y,$w,$h){
        $pdf->SetDrawColor(0,0,0);
        $pdf->Rect($x*0.75,$y*0.75,$w*0.75,$h*0.75);
    }

    //获取试题序号位置
    public function getAnswerArea($pdf,$x,$y,$str){
        $pdf->SetFont('GB-hw','',12*0.75);
        $pdf->SetTextColor(0,0,0);
        $pdf->Text($x*0.75,($y+(12*0.75))*0.75,iconv('UTF-8','gbk',$str));
    }

    //作答区域下划线
    public function getAnswerLink($pdf,$x,$y,$l){
        $pdf->SetDrawColor(0,0,0);
        $pdf->Line($x*0.75,$y*0.75,($x+$l)*0.75,$y*0.75);
    }

    //获取填空题每行信息
    public function getlineInfo($list){

    }
}