<?php
/**
 * @author demo
 * @date 2014年12月11日
 */
/**
 * 加密狗操作类
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SoftKeyModel extends BaseModel{
    /**
     * 验证数据；
     * @param string $keyID 加密狗ID号 字符串
     * @param string $encData 加密后的字符串 做比对用
     * @param int $rnd 随机数
     * @param string $userName 用户名
     * @return string
     * @author demo
     */
    public function checkData($keyID,$encData,$rnd,$userName='',$ifAdmin=0){
        $buffer=$this->selectData(
            '*',
            'KeyNum="'.$keyID.'"'
        );
        if(!$buffer){
            return '30601'; //无效加密狗！
        }
        if($buffer[0]['IfAdmin']!=$ifAdmin){
            return '30728'; //加密锁对应用户不存在！
        }
        if($userName=='') $userName=$this->getCookieUserName();
        if($buffer[0]['UserName']!=$userName){
            return '30603'; //加密狗绑定用户有误！
        }
        $encData2=$this->StrEnc($rnd,$buffer[0]['Key']); //服务器加密数据
        //exit($buffer[0]['Key'].'%%%%'.$rnd.'%%%'.$encData2.'%%%%%'.$encData);
        if(strcasecmp(trim($encData2),trim($encData))!=0){
            return '30602'; //加密狗数据验证失败！
        }
        return '1';
    }
    /**
     * 生成随机数；
     * @return int
     * @author demo
     */
    protected function rnd(){
        return rand(0,99999999) . rand(0,99999999);
    }
    /**
     * 加密狗验证数据写入cookie；
     * @param string $keyID 加密狗ID号 字符串
     * @return none
     * @author demo
     */
    public function setCookieStr($keyID){
        $time=C('WLN_COOKIE_TIMEOUT');
        $rnd=$this->rnd();
        $buffer=$this->selectData(
            '*',
            'KeyNum="'.$keyID.'"');
        $encData=$this->StrEnc($rnd,$buffer[0]['Key']);
        cookie('encData',$rnd.'#$#'.$keyID.'#$#'.$encData,$time);
    }
    /*
     * 以下数据为操作加密锁方法
     * */
    protected function AddLong($lX , $lY )
    {
        $lX4=0;
        $lY4=0;
        $lX8=0;
        $lY8=0;
        $lResult=0;

        $lX8 = $lX & 0x80000000;
        $lY8 = $lY & 0x80000000;
        $lX4 = $lX & 0x40000000;
        $lY4 = $lY & 0x40000000;

        $lResult = ($lX & 0x3FFFFFFF) + ($lY & 0x3FFFFFFF);

        if( $lX4 & $lY4)
        {
            $lResult = $lResult ^ 0x80000000 ^ $lX8 ^ $lY8 ;
        }
        elseif( $lX4 | $lY4 )
        {
            $temp=$lResult & 0x40000000 ;
            if( $lResult & 0x40000000 )
                $lResult = $lResult ^ 0xC0000000 ^ $lX8 ^ $lY8;
            else
                $lResult = $lResult ^ 0x40000000 ^ $lX8 ^ $lY8;
        }
        else
        {
            $lResult = $lResult ^ $lX8 ^ $lY8;
        }

        return  $lResult;
    }

    protected function SubtractLong($lX , $lY ) //长整数减法函数
    {
        $lX8=0;
        $lY8=0;
        $mX=0.00;
        $mY=0.00;
        $mResult=0.00;
        $lResult=0;

        $lX8 = $lX & 0x80000000;
        $lY8 = $lY & 0x80000000;

        $mX = $lX & 0x7FFFFFFF;
        $mY = $lY & 0x7FFFFFFF;

        If( $lX8 )
        {
            If ($lY8)
                $mResult = $mX - $mY;
            else
            {
                $mX = $mX + 2147483648;
                $mResult = $mX - $mY;
            }
        }
        Else
        {
            If ($lY8)
            {
                $mY = $lY;
                $mResult = $mX - $mY;
            }
            Else
                $mResult = $mX - $mY;
        }


        If ($mResult < 0 )
            $lResult = ((2147483648 + $mResult) | 0x80000000) & 0xFFFFFFFF;
        ElseIf ($mResult > 0x7fffffff)
            $lResult = (($mResult - 2147483648) | 0x80000000) & 0xFFFFFFFF;
        Else
            $lResult = $mResult & 0xFFFFFFFF;

        return  $lResult;

    }

    protected function LeftRotateLong($lValue, $lBits ) //按位左移函数
    {
        $lngSign=0; $intI=0;
        $mValue=0;

        $lBits = $lBits % 32;
        $mValue = $lValue;
        if($lBits == 0) return  $mValue;

        For ($intI = 1 ;$intI<= $lBits;$intI++)
        {
            $lngSign = $mValue & 0x40000000;
            $mValue = ($mValue & 0x3FFFFFFF) * 2;

            if($lngSign & 0x40000000)
                $mValue = $mValue | 0x80000000;
        }

        return  $mValue;
    }


    protected function RightRotateLong($lValue , $lBits) //按位右移函数
    {
        $lngSign=0;$intI=0;
        $mValue =0;

        $mValue = $lValue;
        $lBits = $lBits % 32;

        if( $lBits == 0 ) return $mValue ;

        For ($intI = 1 ;$intI<= $lBits;$intI++)
        {
            $lngSign = $mValue & 0x80000000;
            $mValue = ($mValue & 0x7FFFFFFF) / 2;
            if ($lngSign)
                $mValue = $mValue | 0x40000000;
        }
        return  $mValue;
    }

    protected function sub_Decode($v, $k) //增强算法--解密
    {
        $Y=0; $Z=0;
        $K1=0;$K2=0;$K3=0;$K4=0;
        $L1=0;$L2=0;$L3=0;$L4=0;
        $Sum=0;
        $i=0;$Rounds=0;
        $mResult[]=array(0,0);

        $Y = $v[0];
        $Z = $v[1];
        $K1 = $k[0];
        $K2 = $k[1];
        $K3 = $k[2];
        $K4 = $k[3];


        $Rounds = 32;
        $Sum = $this->LeftRotateLong(-1640531527, 5);

        for( $i = 1;$i<= $Rounds;$i++)
        {

            $L1 = $this->LeftRotateLong($Y, 4);
            $L1 = $this->AddLong($L1, $K3);
            $L2 = $this->AddLong($Y, $Sum);
            $L3 = $this->RightRotateLong($Y, 5);
            $L3 = $this->AddLong($L3, $K4);
            $L4 = $L1 ^ $L2 ^ $L3;
            $Z = $this->SubtractLong($Z, $L4);

            $L1 = $this->LeftRotateLong($Z, 4);
            $L1 = $this->AddLong($L1, $K1);
            $L2 = $this->AddLong($Z, $Sum);
            $L3 = $this->RightRotateLong($Z, 5);
            $L3 = $this->AddLong($L3, $K2);
            $L4 = $L1 ^ $L2 ^ $L3;
            $Y = $this->SubtractLong($Y, $L4);

            $Sum = $this->SubtractLong($Sum, -1640531527);

        }

        $v[0] = $Y;
        $v[1] = $Z;
        return $v;
    }

    protected function  sub_Encode($v, $k)
    {
        $Y=0; $Z=0;
        $K1=0; $K2=0; $K3=0; $K4=0;
        $L1=0; $L2=0; $L3=0; $L4=0;

        $Sum=0;
        $i=0; $Rounds=0;
        $mResult[]=array(0,0);

        $Y = $v[0];
        $Z = $v[1];
        $K1 = $k[0];
        $K2 = $k[1];
        $K3 = $k[2];
        $K4 = $k[3];


        $Rounds = 32;

        for( $i = 1;$i<= $Rounds;$i++)
        {
            //sum += delta ;
            $Sum = $this->AddLong($Sum, -1640531527);
            //y += (z<<4)+k[0] ^ z+sum ^ (z>>5)+k[1]
            $L1 = $this->LeftRotateLong($Z, 4);
            $L1 = $this->AddLong($L1, $K1);
            $L2 = $this->AddLong($Z, $Sum);

            // $L2 = ($Z +  $Sum) & 0xFFFFFFFF;

            $L3 = $this->RightRotateLong($Z, 5);
            $L3 = $this->AddLong($L3, $K2);
            $L4 = $L1 ^ $L2;
            $L4 = $L4 ^ $L3;
            $Y = $this->AddLong($Y, $L4);
            //z += (y<<4)+k[2] ^ y+sum ^ (y>>5)+k[3]
            $L1 = $this->LeftRotateLong($Y, 4);
            $L1 = $this->AddLong($L1, $K3);
            $L2 = $this->AddLong($Y, $Sum);
            $L3 = $this->RightRotateLong($Y, 5);
            $L3 = $this->AddLong($L3, $K4);
            $L4 = $L1 ^ $L2 ^ $L3;
            $Z = $this->AddLong($Z, $L4);
        }

        $v[0] = $Y;
        $v[1] = $Z;
        return $v;
    }

    protected function HexStringToByteArray($InString,$b)
    {
        $g_len=0;
        $nlen=0;
        $n=0;
        $i = 0;
        $temp="";
        $nlen = strlen($InString);
        If ($nlen < 16 ) $g_len = 16;
        $g_len = $nlen / 2;

        For( $n = 0;$n<$nlen;$n=$n+2)
        {
            $temp = substr($InString, $n, 2);
            $temp = "0x" . $temp;
            $b[$i] = $temp + 0 ;
            $i = $i + 1;
        }
        return array($g_len,$b);
    }

    protected function  ByteToLong($v, $b)
    {
        $n=0;
        $v[0] = 0;
        $v[1] = 0;
        for ($n = 0;$n<=3;$n++)
        {
            $v[0] = ($b[$n]<<($n * 8)) | $v[0];
            $v[1] = ($b[$n + 4]<<($n * 8)) | $v[1];
        }
        return $v;
    }

    protected function LongToByte($v, $b)
    {
        $n=0;
        $temp=0;
        for ($n = 0;$n<=3;$n++)
        {
            $b[$n] = ($v[0]>> ($n * 8)) & 255;
            $b[$n + 4] = ($v[1]>> ($n * 8)) & 255;
        }
        return $b;
    }

    protected function  HexStringToLongArray($Key, $k)
    {
        $nlen=0;
        $n=0;
        $temp="";
        $buf[]=0;
        $nlen = strlen($Key);
        $i = 0;
        for($n=0;$n<$nlen;$n=$n+2)
        {
            $temp = substr($Key, $n, 2);
            $buf[$i] = ("0x" . $temp)+0;
            $i = $i + 1;
        }
        for($n=0;$n<=3;$n++)
        {
            $k[$n] = 0;
        }
        for($n=0;$n<=3;$n++)
        {
            $k[0] = ($buf[$n]<<($n * 8)) | $k[0];
            $k[1] = ($buf[$n + 4]<< ($n * 8)) | $k[1];
            $k[2] = ($buf[$n + 4 + 4]<< ($n * 8)) | $k[2];
            $k[3] = ($buf[$n + 4 + 4 + 4]<< ($n * 8)) | $k[3];
        }
        return $k;
    }

    protected function Encode($b, $outb, $Key) //增强算法--加密
    {
        $keybuf[]=0;
        $v[]=0;
        $k[]=0;
        $k=$this->HexStringToLongArray( $Key, $k);
        $v=$this->ByteToLong ($v, $b);
        $v=$this->sub_Encode ($v, $k);
        $outb=$this->LongToByte ($v, $outb);
        return $outb;
    }

    protected function Decode($b, $outb, $Key) //增强算法--解密
    {
        $keybuf[]=0;
        $v[]=0;
        $k[]=0;
        $k=$this->HexStringToLongArray( $Key, $k);
        $v=$this->ByteToLong ($v, $b);
        $v=$this->sub_Decode ($v, $k);
        $outb=$this->LongToByte ($v, $outb);
        return $outb;
    }

    protected function StrDec($InString, $Key) //使用增强算法，解密字符串
    {
        $b[]=0;
        $outb[] =0;
        $temp[] =0;
        $outtemp[] =0;
        $n=0;
        $nlen=0;
        $buffer = $this->HexStringToByteArray($InString, $b);
        $nlen=$buffer[0];
        $b=$buffer[1];

        for( $n = 0 ;$n< $nlen;$n++)
        {
            $outb[$n] = $b[$n];
        }

        for ($n = 0;$n<= ($nlen - 8);$n=$n+8)
        {
            for($m=0;$m<8;$m++)
            {
                $temp[$m]= $b[$n+$m];
            }
            $outtemp=$this->Decode ($temp, $outtemp, $Key);
            //MoveByte_2 outb, outtemp, n
            for($m=0;$m<8;$m++)
            {
                $outb[$n+$m]= $outtemp[$m];
            }
        }
        $outstring = "";
        for ($n = 0 ;$n<= $nlen - 1;$n=$n+1)
        {
            if(($outb[$n]<=127) && ($outb[$n]>=0))
            {
                $outstring = $outstring . chr($outb[$n]);
            }
            else
            {
                $temp=($outb[$n]<<8) | $outb[$n+1];
                $outstring = $outstring . chr($temp);
                $n=$n+1;
            }
        }
        return  $outstring;
    }

    protected function myhex($indata)
    {
        $temp_1=$indata/16;
        if($temp_1<10)
            $temp_1=$temp_1+0x30;
        else
            $temp_1=$temp_1+0x41-10;

        $temp_2=$indata% 16;
        if($temp_2<10)
            $temp_2=$temp_2+0x30;
        else
            $temp_2=$temp_2+0x41-10;

        return chr($temp_1) . chr($temp_2);

    }

    protected function ByteArrayToHexString($b, $nlen)
    {
        $outstring = "";
        $n=0;
        $temp=0;
        for($n=0;$n<$nlen;$n++)
        {
            $outstring = $outstring . $this->myhex($b[$n]);
        }
        return  $outstring;
    }

    protected function StrEnc($InString , $Key ) //使用增强算法，加密字符串
    {
        $b[]=0;
        $outb[] =0;
        $temp[] =0;
        $outtemp[] =0;
        $n=0;
        $nlen=0;
        $temp_len=strlen($InString) + 1 ;

        if( $temp_len < 8 )$temp_len = 8;

        for($n=0;$n<$temp_len;$n++)
        {
            $temp_string=substr($InString,$n,1);
            $b[$nlen]=ord($temp_string);
            /*if (($b[$nlen]>128) or ($b[$nlen] < -127))
            {
               $temp_data_1=$b[$nlen] & 255;
               $temp_data_2=($b[$nlen]>>8 )& 0xff;
               $b[$nlen]=$temp_data_2;
               $nlen++;
               $b[$nlen]=$temp_data_1;
            }*/
            $nlen++;
        }

        for( $n = 0 ;$n< $temp_len;$n++)
        {
            $outb[$n] =$b[$n];
        }

        for ($n = 0;$n<= ($temp_len - 8);$n=$n+8)
        {
            for($m=0;$m<8;$m++)
            {
                $temp[$m]= $b[$n+$m];
            }
            $outtemp=$this->Encode ($temp, $outtemp, $Key);
            //MoveByte_2 outb, outtemp, n
            for($m=0;$m<8;$m++)
            {
                $outb[$n+$m]= $outtemp[$m];
            }
        }
        return  $this->ByteArrayToHexString($outb, $temp_len);
    }
}