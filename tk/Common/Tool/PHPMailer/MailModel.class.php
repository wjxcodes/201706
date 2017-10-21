<?php
class MailModel {
    protected $autoCheckFields = false;    //不自动检测数据表字段信息
    public function getpwd($sendto_email, $user_name, $subject, $bodyurl){

        $mail_info=C('MAIL_INFO');
        $bodystr='<html><head>
                <meta http-equiv="Content-Language" content="zh-cn"/>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                </head>
                <body>
                <div style="width:60%;padding:30px 20px;background:#F9F9F9;">
                <span style="font-weight:bold;font-size:16px;">尊敬的用户' . $user_name . '，您好！</span><br/>
                感谢您注册成为题库用户，请点击这里重置您的账户信息；或者复制以下URL粘贴到浏览器地址栏访问：<br/>
                (有效期一小时)<br/>
                <a href="'.$bodyurl.'" target="_blank">' . $bodyurl . '</a><br/>
                再次感谢您选择了我们提供的在线服务，如有任何问题，请联系我们，我们真诚期望您的宝贵意见，谢谢！ <br/>
                    祝您工作顺利，身体健康！' .
                            '</font><br/>
                '.$mail_info['FromName'].'  敬上<br/>'.
                date('Y年m月d日',time()).'
                </div>
                </body>
                </html>
                ';
        return $this->sendmail($sendto_email, $user_name, $subject, $bodystr);
    }
    /**
     * 验证邮箱时发送邮箱验证码
     * @param string $sendToEmail 发送的邮件地址
     * @param string $mcode 验证码
     * @param string $userName 用户名
     * @param string $subject 邮件主题
     * @author demo
     */
    public function getEmailMcode($sendToEmail, $mcode,$userName,$subject){
        $mail_info=C('MAIL_INFO');
        $bodyStr='<html><head>
                <meta http-equiv="Content-Language" content="zh-cn"/>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                </head>
                <body>
                <div style="width:60%;padding:30px 20px;background:#F9F9F9;">
                    <span>尊敬的用户' . $userName . '，您好！</span><br/>
                    <span style="display:block;text-indent:2em;">您的验证码为：<span style="font-weight:bold;font-size:16px;">'.$mcode.'</span></span>
                    <span style="display:block;text-indent:2em;">再次感谢您选择了我们提供的在线服务，如有任何问题，请联系我们，我们真诚期望您的宝贵意见，谢谢！ </span>
                    <span style="display:inline-block;float:right;">祝您工作顺利，身体健康！</span><br/>
                    <span style="display:inline-block;float:right;">'.$mail_info['FromName'].'  敬上</span><br/>
                    <span style="display:inline-block;float:right;">'.date('Y年m月d日',time()).'</span>
                </div>
                </body>
                </html>
                ';
        return $this->sendmail($sendToEmail, $userName, $subject, $bodyStr);
    }

    /**
     * 发送用户激活邮件
     * @param $email string 邮箱号，同时也是用户名
     * @param $subject string 邮件标题
     * @param $bodyURL string 激活地址
     * @return string
     * @author demo
     */
    public function getActivationMail($email,$subject,$bodyURL){
        $mail_info=C('MAIL_INFO');
        $bodyStr='<html><head>
                <meta http-equiv="Content-Language" content="zh-cn"/>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
                </head>
                <body>
                <div style="width:60%;padding:30px 20px;background:#F9F9F9;">
                    <span>尊敬的用户' . $email . '，您好！</span><br/>
                    <span style="display:block;text-indent:2em;font-size:25px;">感谢您注册题库系统账户</span>
                    <span style="text-indent:2em;">为了您能正常使用我们的系统，请点击以下链接激活您的邮箱。</span><br />
                    <span style="text-indent:2em;font-weight:bold;font-size:16px;">
                        <a href="'.$bodyURL.'" target="_blank">'.$bodyURL.'</a>
                    </span>
                    <span style="display:block;text-indent:2em;">再次感谢您选择了我们提供的在线服务，如有任何问题，请联系我们，我们真诚期望您的宝贵意见，谢谢！ </span>
                    <span style="display:inline-block;float:right;">祝您工作顺利，身体健康！</span><br/>
                    <span style="display:inline-block;float:right;">'.$mail_info['FromName'].'  敬上</span><br/>
                    <span style="display:inline-block;float:right;">'.date('Y年m月d日',time()).'</span>
                </div>
                </body>
                </html>
                ';
        return $this->sendmail($email, $email, $subject, $bodyStr);
    }

    public function sendmail($sendto_email, $user_name, $subject, $bodystr){
        $mail_info=C('MAIL_INFO');
        //Vendor('PHPMailer.class#phpmailer');
        //$mail = new PHPMailer();
        $mail=useToolFunction('PHPMailer/PHPMailer','','');

        $mail->IsSMTP();                  // send via SMTP
        $mail->Host = $mail_info['Host'];   // SMTP servers
        $mail->SMTPAuth = true;           // turn on SMTP authentication
        $mail->Username = $mail_info['Username'];     // SMTP username  注意：普通邮件认证不需要加 @域名
        $mail->Password = $mail_info['Password']; // SMTP password
        $mail->From = $mail_info['Username'];      // 发件人邮箱
        $mail->FromName = $mail_info['FromName'];  // 发件人

        $mail->CharSet = "utf-8";   // 这里指定字符集！
        $mail->Encoding = "base64";
        $mail->AddAddress($sendto_email, $user_name);  // 收件人邮箱和姓名
        $mail->SetFrom($mail_info['Username'], $mail_info['FromName']);

        $mail->AddReplyTo($mail_info['Username'], $mail_info['FromName']);
        //$mail->WordWrap = 50; // set word wrap 换行字数
        //$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment 附件
        //$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
        $mail->IsHTML(true);  // send as HTML
        // 邮件主题
        $mail->Subject = $subject;
        // 邮件内容
        $mail->Body = $bodystr;
        $mail->AltBody = "text/html";

        if (!$mail->Send())
        {
            $mail->ClearAddresses();
            return "邮件错误信息: " . $mail->ErrorInfo;
            exit;
        }
        else
        {
           $mail->ClearAddresses();
           // $this->assign('waitSecond', 6);
//            $this->success("注册成功,系统已经向您的邮箱：{$sendto_email}发送了一封激活邮件!请您尽快激活~~<br />");
           return 'success';
        }
    }
}
?>