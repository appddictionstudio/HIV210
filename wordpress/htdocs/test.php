<?php
try{
    $headers = "From: n.srinivasulurao@gmail.com" . "\r\n" .
    "CC: somebodyelse@example.com";
    
$sent= mail("doru.arfire.1279@gmail.com","My subject","Hello World !",$headers);
if($sent)
  echo "Mail Sent !";
else
  echo "Mail Server Not configured !"; 
  //phpinfo(); 
}
catch(Exception $e){
    echo $e->getMessage()." @ ".$e->getLine();
}
?>