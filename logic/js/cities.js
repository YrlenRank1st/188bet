"use strict";
loadCitiesByState();
function loadCitiesByState(){
var form,item;
var cityBox,cityLabel,cityInput,citySelect,citySelectBox;
var stateSelect;
var oldState="";
var selectState="";
var cityLists=[];
var citySelections=[];
var AJAX_CODE=0;
var cityValue="";
var cityIsRequred=true;
cityBox=document.getElementById("field-city");
if(cityBox){
  item=cityBox;
  form=null;
  while(item.parentElement){
    item=item.parentElement;
    if(item.nodeName.toLowerCase()=="form"){
      form=item;
      break;
    }
  }
  if(form){
    stateSelect=form.elements["state"];
    cityLabel=cityBox.getElementsByTagName("label")[0];
    cityInput=cityBox.getElementsByTagName("input")[0];
    if(cityInput.required){cityIsRequred=true;}
    cityValue=cityInput.value;
    //
    citySelectBox=document.createElement("div");
    citySelectBox.style.display="none";
    citySelect=document.createElement("select");
    citySelectBox.appendChild(citySelect);
    cityBox.appendChild(citySelectBox);
    citySelect.addEventListener("change",citySelected);
    citySelect.required=false;
    citySelect.name="";
    
    stateChanged(null);
    stateSelect.addEventListener("change",stateChanged);
  }
}// End if city box exists
function stateChanged(e){
  var state=form.elements["state"].value;
  AJAX_CODE=Math.random();
  var ajaxCode=AJAX_CODE;
  if(oldState!=state){
    if(state==""){
      useCitySelect(false);
    } else if(cityLists[state]){
      //Load this list
      useCitySelect(true);
      fillCitySelect(citySelect,cityLists[state],state);
    } else {
      //Load from API
      var ajax=new XMLHttpRequest();
      ajax.addEventListener("readystatechange",changeCityList);
      ajax.open("GET","/logic/api/cities.php?stateId="+state,true);
      ajax.send();
      function changeCityList(e){
        if(ajax.readyState==4){
          if(ajaxCode!=AJAX_CODE){
            //Outdated. Ignore.
          } else {
            var jsonCode=ajax.responseText;
            try{
              var cityData=JSON.parse(jsonCode);
              if(cityData.erro==true){
                //Error. Show text field
                useCitySelect(false);
              } else {
                cityLists[state]=cityData["data"];
                useCitySelect(true);
                fillCitySelect(citySelect,cityData["data"],state);
              }
            }catch(err){
              console.log("JSON ERROR",err);
            }
          } //End of ajax code up to date
        } //End if ready state OK
      } //End function changeCityList
      
    } // End load from API
  } // End if state changed
  
  oldState=state;
} // End function state changed

function fillCitySelect(select,data,state){
  //Insert the list of cities into the <select> element
  //Data: [{name,id},...]
  var i,len,name,option;
  len=data.length;
  citySelections[selectState]=select.value;
  while(select.firstChild){select.removeChild(select.firstChild);}
  //First item
  option=document.createElement("option");
  option.value="";
  option.appendChild(document.createTextNode("Cidade *"));  
  select.appendChild(option);
  len=data.length;
  for(i=0;i<len;i++){
    option=document.createElement("option");
    if(!i){option.selected=true;}
    option.value=data[i]["name"];
    option.appendChild(document.createTextNode(data[i]["name"]));
    select.appendChild(option);
    if(citySelections[state]){
      if(citySelections[state]===option.value){
        option.selected=true;
      }
    } else if(cityValue===option.value){
      option.selected=true;
    }
  }
  citySelected(null);
  selectState=state;
}
function citySelected(e){
  if(citySelect.value){
    cityInput.value=cityValue=citySelect.value;
  }
}
function useCitySelect(b){
  if(b){
    cityLabel.style.display="none";
    cityInput.style.display="none";
    citySelectBox.style.display="";
    cityInput.name="";
    citySelect.name="city";
    citySelect.required=cityIsRequred;
    cityInput.required=false;
  } else {
    cityLabel.style.display="";
    cityInput.style.display="";
    citySelectBox.style.display="none";
    cityInput.name="city";
    citySelect.name="";
    citySelect.required=false;
    cityInput.required=cityIsRequred;
  }
}

}