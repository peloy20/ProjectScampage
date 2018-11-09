<?php
/**
 * @Author: 
 * @Date:   
 * @Last Modified by:   ./Peloy20
 * @Last Modified time: 2018-11-03 20:34:03
 */
require_once 'autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Sendinbox extends Config
{
	function __construct()
	{   
        $this->modules = new SendinboxModules;
        if(phpversion() < "7.0.0"){
           die("PHP ".phpversion()." TIDAK SUPPORT SILAHKAN UPDATE KE PHP 7\r\n");
        }
        echo "=========================================\r\n";
        echo "Sendinbox ".$this->modules->versi()."\r\n";
        echo "(c) 2018 ./Peloy20\r\n";
        echo "=========================================\r\n";
        $LoadEmail              = $this->modules->stuck("[ Load Email List (list.txt) ] : "); 
        $du                     = $this->modules->stuck("[ Send Duplicate Mail (0 = Yes , 1 = No)] : "); 
        $debug                  = $this->modules->stuck("[ Enable Debug (0 = Yes , 1 = No)] : "); 
        $this->listEmail        = $this->modules->load($LoadEmail , $du);
        $this->smtp_config      = null;
        $this->debug            = str_replace("1", "no", str_replace("0", "yes", $debug));
        $this->run();
    }
    function send(){
        $mail = new PHPMailer(true);
        try {

            $mail->setLanguage('id', 'modules/language/'); // jika smtp error ganti "id" jadi "en"
            
            if($this->debug == 'yes'){
                $mail->SMTPDebug    = 3;                               
            }else{
                $mail->SMTPDebug    = 0;                               
            }

            $mail->isSMTP();                                            
            $mail->Host         = $this->smtp_config['smtp_host'];       
            $mail->SMTPAuth     = true;                               
            $mail->Username     = $this->smtp_config['smtp_user'];                 
            $mail->Password     = $this->smtp_config['smtp_pass'];                             
            $mail->SMTPSecure   = $this->smtp_config['smtp_secure'];                                
            $mail->Port         = $this->smtp_config['smtp_port'];                                 
            $mail->From         = $this->alias( $this->smtp_config['recipients']['from_email'] , $this->email);
            $mail->FromName     = $this->alias( $this->smtp_config['recipients']['from_name']  , $this->email);

            foreach ($this->smtp_config[content][attachments] as $key => $attfile) {
                if($attfile != ""){
                    $flocation = 'attachments/'.$attfile;
                    if( file_exists($flocation) ){
                        $mail->addAttachment('attachments/'.$attfile);
                    }
                }
            }

            $mail->Encoding = 'base64';
            $mail->CharSet  = 'UTF-8';

            $mail->AddAddress($this->email);
            
            $mail->isHTML(true); 
            
            $content = $this->modules->arrayrandom(  $this->smtp_config['content']['format'] );
			

            if(!file_exists('letter/'.$content['value'])){
                die("[IndonesiaSendinbox] ============>> Letter Tidak ada <<============\r\n");
            }
            
            $bodyLetter         = $this->alias( file_get_contents('letter/'.$content['value']) , $this->email);
            $mail->Subject      = $this->alias( $content['key'] , $this->email );
            $mail->Body         = $this->alias( $bodyLetter , $this->email  );
            
            $mail->send();
            $this->modules->save('sendinbox-success.txt',$this->email);
            return 'Message has been sent';
        } catch (Exception $e) {
            $this->modules->save('sendinbox-failed.txt',$this->email);
            return $mail->ErrorInfo."\r\n";
        }     
    }
    function run(){
        $hit = 1;
        $num = 1;
       

        foreach ($this->listEmail['list'] as $key => $email) {
			 $this->sendinbox_config     = $this->setting();
				$this->smtp_array           = $this->smtp();
		   
            $this->smtp_config  = $this->modules->arrayrandom(  $this->smtp_array )['value'];
            $this->email        = $email; 

            if(count($this->smtp_array) == 0){
                die("[IndonesiaSendinbox] ============>> SMTP Tidak ada <<============\r\n");
            }
            
            echo "[IndonesiaSendinbox][".$hit."/".$this->listEmail['total']."|".count($this->smtp_array)."][".substr($this->smtp_config['smtp_user'], 0,8)."..] ".$email." => ";
            
            $send   = $this->send();
            echo str_replace('Message has been sent', 'success' , $send);
            
            if( $send != 'Message has been sent' ){
                unset($this->smtp_array[$this->smtp_config['key']]);
            }
            

            if($num == $this->sendinbox_config['number']){
                echo "[IndonesiaSendinbox] ============>> Delay (".$this->sendinbox_config['delay'].") Sec <<============\r\n";
                sleep($this->sendinbox_config['delay']);
                $num = 1;
            }
            echo "\r\n";
            $num++;
            $hit++;
        }
    }
}
$Sendinbox = new Sendinbox;