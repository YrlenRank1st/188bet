"use strict";
(function(){
var REWARD_API_URL="/logic/user/x-reward.php";
var BUY_REWARD_API_URL="/logic/user/x-reward-buy.php";
var REWARD_BACKUP_URL="/reward";
var BUY_REWARD_BACKUP_URL="/reward/buy";
var DEFAULT_LOADING_HTML="<div class=\"white-box lightbox-loader\"><span></span><p>Carregando...</p></div>";
//
var sliders=document.getElementsByClassName("rewards-slider");
var rewards=document.getElementsByClassName("reward");
var rLen=rewards.length;
var r,rewardA,aLen,a;
orderRewards(rewards);
if(sliders.length){
  initRewardSlider(sliders[0],rewards);
}
for(r=0;r<rLen;r++){
  rewardA=rewards[r].getElementsByTagName("a");
  aLen=rewardA.length;
  for(a=0;a<aLen;a++){
    rewardA[a].addEventListener("click",viewReward);
  }
}


function initRewardSlider(slider,rewards){
  //Create price arrows and fill
  var lowPrice=Number(slider.dataset.low);
  var highPrice=Number(slider.dataset.high);
  
  var lowBar,highBar,lowNumber,highNumber,fill;
  fill=document.createElement("div");
  fill.setAttribute("class","fill");
  slider.appendChild(fill);
  //
  lowBar=document.createElement("div");
  lowBar.setAttribute("class","low-price");
  lowBar.style.left="0px";
  slider.appendChild(lowBar);
  lowNumber=document.createElement("span");
  lowNumber.appendChild(document.createTextNode(lowPrice));
  lowNumber.setAttribute("class","price");
  lowBar.appendChild(lowNumber);
  //
  highBar=document.createElement("div");
  highBar.setAttribute("class","high-price");
  slider.appendChild(highBar);
  highNumber=document.createElement("span");
  highNumber.appendChild(document.createTextNode(highPrice));
  highNumber.setAttribute("class","price");
  highBar.appendChild(highNumber);
  highBar.style.left=(fill.getBoundingClientRect().width)+"px";
  //Initialize items
  var selectedSlider=null;
  var minValue=lowPrice;
  var maxValue=highPrice;
  window.addEventListener("resize",updatePos);
  /* Functions */
  function updatePos(e){
    var box=slider.getBoundingClientRect();
    var maxWidth=box.width;
    var leftPos=Math.floor(box.width * ( (minValue-lowPrice)/(highPrice-lowPrice) ) );
    var rightPos=Math.floor(box.width * ( (maxValue-lowPrice)/(highPrice-lowPrice) ) );
    
    lowBar.style.left=leftPos+"px";
    highBar.style.left=rightPos+"px";
    fill.style.marginLeft=leftPos+"px";
    fill.style.width=(rightPos-leftPos)+"px";
  }
  function updateValue(){
    var box=slider.getBoundingClientRect();
    var maxWidth=box.width;
    var leftPX=Number(lowBar.style.left.slice(0,lowBar.style.left.length-2));
    var rightPX=Number(highBar.style.left.slice(0,highBar.style.left.length-2));
    minValue=lowPrice+Math.floor((highPrice-lowPrice)*Number(leftPX)/maxWidth);
    maxValue=lowPrice+Math.floor((highPrice-lowPrice)*Number(rightPX)/maxWidth);
    
    //Select all items between these two values
    lowNumber.firstChild.nodeValue=minValue;
    highNumber.firstChild.nodeValue=maxValue;
    
    filterRewards(rewards,minValue,maxValue);
    
  }
  
  /* Events */
  lowBar.addEventListener("mousedown",dragItem);
  highBar.addEventListener("mousedown",dragItem);
  function dragItem(e){
    if(e.currentTarget!=lowBar && e.currentTarget!=highBar){
      return;
    }
    e.preventDefault();
    selectedSlider=e.currentTarget;
    document.body.addEventListener("mousemove",moveItem);
    document.body.addEventListener("mouseup",dropItem);
  }
  function moveItem(e){
    e.preventDefault();
    var box=slider.getBoundingClientRect();
    var xPos=e.clientX-box.left;
    if(xPos<0){xPos=0;}
    if(xPos>box.width){xPos=box.width;}
    
    //Correct
    var leftPX=Number(lowBar.style.left.slice(0,lowBar.style.left.length-2));
    var rightPX=Number(highBar.style.left.slice(0,highBar.style.left.length-2));
    if(selectedSlider===lowBar && xPos>rightPX){
      xPos=rightPX;
    } else if(selectedSlider===highBar && xPos<leftPX){
      xPos=leftPX;
    }
    selectedSlider.style.left=Math.floor(xPos)+"px";
    var leftPX=Number(lowBar.style.left.slice(0,lowBar.style.left.length-2));
    var rightPX=Number(highBar.style.left.slice(0,highBar.style.left.length-2));
    fill.style.marginLeft=leftPX+"px";
    fill.style.width=(rightPX-leftPX)+"px";
    updateValue();
  }
  function dropItem(e){
    e.preventDefault();
    document.body.removeEventListener("mousemove",moveItem);
    document.body.removeEventListener("mouseup",dropItem);
  }
  /* End Events */

}

function orderRewards(list){
  var len=list.length;
  var len_1=len-1;
  if(len<2){return;}
  var i,j;
  var curBox,tempBox,minBox;
  var curValue,tempValue,minValue;
  var parent=list[0].parentElement;
  for(i=0;i<len_1;i++){
    curBox=list[i];
    curValue=curBox.dataset.price;
    minValue=curValue;minBox=null;
    //
    for(j=i+1;j<len;j++){
      tempBox=list[j];
      tempValue=Number(tempBox.dataset.price);
      if(tempValue<minValue){ minBox=tempBox;minValue=tempValue; }
    }
    //
    if(minBox){ parent.insertBefore(minBox,curBox); }
  } // End for i in items

}

function filterRewards(list,minPrice,maxPrice){
  var i,len,item,price;
  len=list.length;
  for(i=0;i<len;i++){
    item=list[i];
    price=item.dataset.price;
    if(price<minPrice||price>maxPrice){
      item.style.display="none";
    } else {
      item.style.display="";
    }
  }
}

/** Reward Window **/
var REWARD_WINDOW=null;
var CITY_SCRIPT=null;
function viewReward(e){
  if(REWARD_WINDOW){return;}
  e.preventDefault();
  var link=e.currentTarget;
  var href=link.getAttribute("href");
  var id=href.slice(href.indexOf("?id=")+4,href.length);
  
  REWARD_WINDOW=makeLightbox(DEFAULT_LOADING_HTML);
  
  var ajax=new XMLHttpRequest();
  ajax.open("GET",REWARD_API_URL+"?id="+id,true);
  ajax.addEventListener("readystatechange",rewardLoaded);
  ajax.send();
  
}
function rewardLoaded(e){
  var ajax=e.currentTarget;
  if(ajax.readyState==4){
    if(ajax.status==200){
      REWARD_WINDOW.innerHTML=ajax.responseText;
      displayReward();
    } else {
      //Nothing found.
      var href=ajax.responseURL
      var id=href.slice(href.indexOf("?id=")+4,href.length);
      location.href=REWARD_BACKUP_URL+"?id="+id;
    }
  }
}

function displayReward(){
  var closeBtn=REWARD_WINDOW.getElementsByClassName("window-close")[0];
  var buyBtn=REWARD_WINDOW.getElementsByClassName("cta")[0];
  if(closeBtn){
    closeBtn.addEventListener("click",closeRewardWindow);
  }
  if(buyBtn){
    buyBtn.addEventListener("click",openBuyWindow);
  }
  
}
function closeRewardWindow(e){
  e.preventDefault();
  e.currentTarget.removeEventListener("click",closeRewardWindow);
  closeLightbox();
  REWARD_WINDOW=null;
  if(CITY_SCRIPT){CITY_SCRIPT.parentElement.removeChild(CITY_SCRIPT);}
}

/** Buy Reward Window **/
function openBuyWindow(e){
  e.preventDefault();
  var link=e.currentTarget;
  var href=link.getAttribute("href");
  var id=href.slice(href.indexOf("?id=")+4,href.length);
  
  REWARD_WINDOW.innerHTML=DEFAULT_LOADING_HTML;
  
  var ajax=new XMLHttpRequest();
  ajax.open("GET",BUY_REWARD_API_URL+"?id="+id,true);
  ajax.addEventListener("readystatechange",buyRewardLoaded);
  ajax.send();
  
}
function buyRewardLoaded(e){
  var ajax=e.currentTarget;
  if(ajax.readyState==4){
    if(ajax.status==200){
      REWARD_WINDOW.innerHTML=ajax.responseText;
      displayBuyReward();
    } else {
      //Nothing found.
      var href=ajax.responseURL
      var id=href.slice(href.indexOf("?id=")+4,href.length);
      location.href=BUY_REWARD_BACKUP_URL+"?id="+id;
    }
  }
}
function displayBuyReward(){
  var closeBtn=REWARD_WINDOW.getElementsByClassName("window-close")[0];
  var form=REWARD_WINDOW.getElementsByTagName("form")[0];
  var script=REWARD_WINDOW.getElementsByTagName("script")[0];
  if(closeBtn){
    closeBtn.addEventListener("click",closeRewardWindow);
  }
  if(form){
    form.addEventListener("submit",buyReward);
  }
  if(script){
    script.parentElement.removeChild(script);
    CITY_SCRIPT=document.createElement("script");
    CITY_SCRIPT.src=script.src+"?"+Math.floor(99999*Math.random());
    CITY_SCRIPT.type=script.type;
    document.head.appendChild(CITY_SCRIPT);
  }
  makeDynamicLabels();
  function buyReward(e){
    e.preventDefault();
    var el=form.elements;
    var query=""
      +"rewardId="+encodeURIComponent(el['rewardId'].value)
      +"&name="+encodeURIComponent(el['name'].value)
      +"&cpf="+encodeURIComponent(el['cpf'].value)
      +"&cep="+encodeURIComponent(el['cep'].value)
      +"&address="+encodeURIComponent(el['address'].value)
      +"&complement="+encodeURIComponent(el['complement'].value)
      +"&state="+encodeURIComponent(el['state'].value)
      +"&city="+encodeURIComponent(el['city'].value)
      +"&telephone="+encodeURIComponent(el['telephone'].value);

    REWARD_WINDOW.innerHTML=DEFAULT_LOADING_HTML;
    CITY_SCRIPT.parentElement.removeChild(CITY_SCRIPT);
    CITY_SCRIPT=null;
    
    var ajax=new XMLHttpRequest();
    ajax.open("POST",BUY_REWARD_API_URL+"?id="+el['rewardId'].value,true);
    ajax.addEventListener("readystatechange",buyRewardLoaded);
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    ajax.send(query);
  }
  
}


})();