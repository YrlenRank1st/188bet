"use strict";
var DYNAMIC_LABEL_CONFIGURED=[];
var DYNAMIC_LABEL_LENGTH=0;
function makeDynamicLabels(){
  var inputs=document.getElementsByTagName("input");
  var textAreas=document.getElementsByTagName("textarea");
  var labels=document.getElementsByTagName("label");
  var iLen=inputs.length,tLen=textAreas.length;
  var i;
  for(i=0;i<iLen;i++){configureInput(inputs[i],labels);}
  for(i=0;i<tLen;i++){configureInput(textAreas[i],labels);}

  function configureInput(tag,list){
    var label=null;
    var lLen=list.length;
    var tagID=tag.id;
    var labelClass="";
    var temp_tag;
    var x;
    for(x=0;x<DYNAMIC_LABEL_LENGTH;x++){
      //Remove elements that no longer have parents
      if(!document.body.contains(DYNAMIC_LABEL_CONFIGURED[x].parentElement)){
        temp_tag=DYNAMIC_LABEL_CONFIGURED[x];
        temp_tag.removeEventListener("click",updateLabel);
        temp_tag.removeEventListener("focus",updateLabel);
        temp_tag.removeEventListener("blur",updateLabel);
        temp_tag.removeEventListener("change",updateLabel);
        x--;DYNAMIC_LABEL_LENGTH--;
        DYNAMIC_LABEL_CONFIGURED.splice(x,1);
      }
      //Ignore element if exists
      if(DYNAMIC_LABEL_CONFIGURED[x]===tag){
        return;
      }
    }
    for(x=0;x<lLen;x++){
      if(list[x].htmlFor==tagID){label=list[x];break;}
    }
    if(label){
      DYNAMIC_LABEL_CONFIGURED[DYNAMIC_LABEL_LENGTH]=tag;
      DYNAMIC_LABEL_LENGTH++;
      labelClass=tag.getAttribute("class");
      if(labelClass===null){labelClass="";}
      tag.addEventListener("click",updateLabel);
      tag.addEventListener("focus",updateLabel);
      tag.addEventListener("blur",updateLabel);
      tag.addEventListener("change",updateLabel);
      updateLabel({currentTarget:tag});
    }
    function updateLabel(e){
      var tag=e.currentTarget;
      if(document.activeElement===tag || tag.value.length){
        label.setAttribute("class",labelClass+" active");
      } else {
        label.setAttribute("class",labelClass);
      }
    } // End updateLabel
  } // End configureInput
}
makeDynamicLabels();
