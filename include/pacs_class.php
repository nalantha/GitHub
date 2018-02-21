<?php
class findpacs
{
   private $QueryLeval;
   private $Selections;
   private $Institution;
   private $studyUID;
   private $Patient;
   private $result = array();
   private $No_response = 0;

   public function __construct(string $QueryLeval, string $Institution, string $Patient, string $studyUID, array $Selections)
   {
      $this->QueryLeval=" -k QueryRetrieveLevel=".$QueryLeval;
      $this->Institution=" -k InstitutionName=".$Institution;
      $this->Patient=" -k PatientName=".$Patient;
      $this->studyUID=" -k StudyInstanceUID=".$studyUID;
      $this->PatientID=" -k PatientID=*";
      $this->Selections=$Selections;
      foreach ($this->Selections as $value){
          $this->result[$value] = array();
      }

      $subcmd = $this->QueryLeval;
      foreach ($this->Selections as $value){
          $subcmd = $subcmd." -k ".$value;
      }
      $subcmd = $subcmd.$this->Institution;
      $subcmd = $subcmd.$this->Patient;
      $subcmd = $subcmd.$this->studyUID;
      $subcmd = $subcmd.$this->PatientID;
      $cmd = "findscu 104.155.181.67 11112  -aec LUXSONIC  -S ".$subcmd." 2>&1";
      $out= shell_exec($cmd);
      $line=explode("I:", $out);
     
      foreach($line as $lineout){
        if (preg_match("/\bFinal Find Response\b/", $lineout)){
           break;
        }
        if (preg_match("/\bFind Response\b/", $lineout)){
             $this->No_response = $this->No_response +1;
             $index = $this->No_response-1;
        }
        foreach($this->Selections as $value){
           if (preg_match("/\b".$value."\b/",$lineout)){ 
              preg_match('/\[(.*)\]/',$lineout,$match);
              if ( ! isset($match[0]))  $match[0] = null;
              $this->result[$index][$value] = str_replace(array('[',']'),"",$match[0]);
           }
        }
      }
   }
   public function output()
   {
       return array($this->result,$this->No_response);
   }
}

class getdicom
{
   private $QueryLeval;
   private $studyUID;
   private $patinetID;
   public function __construct(string $QueryLeval, string $studyUID)
   {
      try {
         shell_exec("mkdir /usr/share/PVDI/html/tmp/".$studyUID);
      } finally {
         //  
      }
      $this->studyUID=" -k StudyInstanceUID=".$studyUID;
      $this->QueryLeval=" -k QueryRetrieveLevel=".$QueryLeval;
      $this->patientID=" -k PatientID";
      $subcmd = $this->QueryLeval;
      $subcmd = $subcmd.$this->studyUID;
      $subcmd = $subcmd.$this->patientID;
      $cmd = "getscu 104.155.181.67 11112  -aec LUXSONIC  -S -v -od /usr/share/PVDI/html/tmp/".$studyUID.$subcmd." 2>&1";
      echo $cmd;
      $this->out= shell_exec($cmd);
   }
   public function output()
   {  
      return $this->out;
   }
}
?>
