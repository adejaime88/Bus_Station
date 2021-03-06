<?php
// Log
abstract class LogLevel
{
    const Error = 0;
    const Warning = 1;
    const Info = 2;
}

class Log
{    
    private static $logLevelStr = ["Error", "Warning", "Info"];

    public static $logLevel = LogLevel::Error;
    public static $phpFile = "";

    function debug($logLevel, $message) {
        try {
            if($logLevel <= self::$logLevel){   
                $date = new DateTime();
                $log = date_format($date, "Y-m-d H:i:s.u")." - ".self::$phpFile." - ".self::$logLevelStr[$logLevel]." - ".$message."\r\n";
                if(!file_exists('logs')){
                    mkdir("logs");
                }
                error_log($log, 3, "logs/log.txt");				
            }
        } catch(Exception $e){
        }
    }    
}


/* https://secure.php.net/manual/pt_BR/function.crypt.php
* Generate a secure hash for a given password. The cost is passed
* to the blowfish algorithm. Check the PHP manual page for crypt to
* find more information about this setting.
*/
function generate_hash($password, $cost=11){
        /* To generate the salt, first generate enough random bytes. Because
         * base64 returns one character for each 6 bits, the we should generate
         * at least 22*6/8=16.5 bytes, so we generate 17. Then we get the first
         * 22 base64 characters
         */
        $salt=substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
        /* As blowfish takes a salt with the alphabet ./A-Za-z0-9 we have to
         * replace any '+' in the base64 string with '.'. We don't have to do
         * anything about the '=', as this only occurs when the b64 string is
         * padded, which is always after the first 22 characters.
         */
        $salt=str_replace("+",".",$salt);
        /* Next, create a string that will be passed to crypt, containing all
         * of the settings, separated by dollar signs
         */
        $param='$'.implode('$',array(
                "2y", //select the most secure version of blowfish (>=PHP 5.3.7)
                str_pad($cost,2,"0",STR_PAD_LEFT), //add the cost in two digits
                $salt //add the salt
        ));
       
        //now do the actual hashing
        return crypt($password,$param);
}

/* https://secure.php.net/manual/pt_BR/function.crypt.php
* Check the password against a hash generated by the generate_hash
* function.
*/
function validate_pw($password, $hash){
        /* Regenerating the with an available hash as the options parameter should
         * produce the same hash if the same password is passed.
         */
        return crypt($password, $hash)==$hash;
}

/**
 * https://secure.php.net/manual/pt_BR/function.random-bytes.php
 */
function RandomToken($length = 32){
    if(!isset($length) || intval($length) <= 8 ){
      $length = 32;
    }
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }
    if (function_exists('mcrypt_create_iv')) {
        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    } 
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}

function Salt(){
    return substr(strtr(base64_encode(hex2bin(RandomToken(32))), '+', '.'), 0, 44);
}

function getImagemAvatar($cpf){
    // prepara o avatar com o cpf (nome do arquivo)
    $imageFile = "images/avatar/".$cpf;
    $imagemAvatar = "images/avatar/padrao.png";
    if($cpf != ""){
        if(file_exists($imageFile.".png")){
            $imagemAvatar = $imageFile.'.png';
        } else if(file_exists($imageFile.".jpg")){
            $imagemAvatar = $imageFile.'.jpg';
        }
    }     
    return $imagemAvatar;
}

function formatPhone($phone) // retorna o formato 99-999999999
{
    $formatedPhone = preg_replace('/[^0-9]/', '', $phone);
    $matches = [];
    preg_match('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', $formatedPhone, $matches);
    if ($matches) {
        return $matches[1].'-'.$matches[2].$matches[3];
    }
    return $phone; // return number without format
}

function formatCPF($cpf) // retorna o formato 999.999.999-99
{
    $formatedCPF = preg_replace('/[^0-9]/', '', $cpf);
    $matches = [];
    preg_match('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})$/', $formatedCPF, $matches);
    if ($matches) {
        return $matches[1].'.'.$matches[2].'.'.$matches[3].'-'.$matches[4];
    }
    return $cpf; // return number without format
}


function formatCNPJ($cnpj) // retorna o formato 99.999.999/9999-99
{
    $formatedCNPJ = preg_replace('/[^0-9]/', '', $cnpj);
    $matches = [];
    preg_match('/^([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})$/', $formatedCNPJ, $matches);
    if ($matches) {
        return $matches[1].'.'.$matches[2].'.'.$matches[3].'/'.$matches[4].'-'.$matches[5];
    }
    return $cnpj; // return number without format
}

function setDisplayHTMLElement($html, $name, $visible){
    if($visible){
        $text = "";
    } else {
        $text = "hidden";
    }
    return str_replace($name, $text, $html);
}

?>