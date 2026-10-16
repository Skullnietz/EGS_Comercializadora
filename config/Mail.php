<?php
require_once __DIR__.'/Hosting.php';
class EgsMail
{
    public static function configure($mail)
    {
        EgsHosting::requireWrites();
        if(getenv('MAIL_HOST')){
            $mail->isSMTP();$mail->Host=getenv('MAIL_HOST');$mail->Port=(int)(getenv('MAIL_PORT') ?: 587);
            $mail->SMTPAuth=(getenv('MAIL_USERNAME') ?: '')!=='';$mail->Username=getenv('MAIL_USERNAME') ?: '';
            $mail->Password=getenv('MAIL_PASSWORD') ?: '';$mail->SMTPSecure=getenv('MAIL_ENCRYPTION')===false?'tls':getenv('MAIL_ENCRYPTION');
        }else{$mail->isMail();}
        $from=getenv('MAIL_FROM_ADDRESS') ?: 'noreplay.ordenes@backend.comercializadoraegs.com';
        $mail->setFrom($from,getenv('MAIL_FROM_NAME') ?: 'EGS');$mail->addReplyTo(getenv('MAIL_REPLY_TO') ?: $from);
    }
}
