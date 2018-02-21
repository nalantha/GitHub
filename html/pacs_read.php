<?php
$QueryLeval=" -k QueryRetrieveLevel=SERIES";
$center=" -k InstitutionName=\"MedTec\"";
$infoArray=array('StudyDate','InstitutionName','StationName','Modality','StudyDescription','PatientName');
foreach ($infoArray as $value){
   $value = array();
}
echo "write <br>";
$subcmd = $QueryLeval;
foreach ($infoArray as $value){
  $subcmd = $subcmd." -k ".$value;
}
$subcmd = $subcmd.$center;
$cmd = "findscu 35.192.237.197 11112  -aec DCM4CHEE  -v -O ".$subcmd." 2>&1";
$out= shell_exec($cmd);
$line=explode("I:", $out);
$No_response=0;
foreach($line as $lineout){
   if (preg_match("/\bFinal Find Response\b/", $lineout)){
        break;
   }
        if (preg_match("/\bFind Response\b/", $lineout)){
             $No_response = $No_response +1;
             $index = $No_response -1;
             echo $index."<br>";
        }
       echo $lineout."<br>";
       foreach($infoArray as $value){
           if (preg_match("/\b".$value."\b/",$lineout)){ 
              preg_match('/\[(.*)\]/',$lineout,$match);
              $$value[$index] = str_replace(array('[',']'),"",$match[0]);
              echo $$value[$index]."<br>";
           }
       }
}

echo "<table>";
for ($i=0; $i < $No_response; $i++){
    echo "<tr>";
    foreach($infoArray as $value){
        echo "<td>".$$value[$i]."</td>";
    }
    echo "<tr>";
}
echo "</table>";
?>
