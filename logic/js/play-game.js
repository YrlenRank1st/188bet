"use strict";
(function(){
/** Global variables **/
var PLAY_MATCH_URL="/logic/user/x-play-match.php";
var BET_MATCH_URL="/logic/user/x-bet-match.php";
var DEFAULT_LOADING_HTML="<div class=\"white-box lightbox-loader\"><span></span><p>Carregando...</p></div>";
var CHANGE_MADE=false;

var OPTION_NOT_SELECTED="Por favor, escolha uma opção.";
var CONFIRM_BET_STR=
   "Tem certeza que deseja escolher esta cartela? \n"
  +"Uma vez escolhida você não poderá mudar, \n"
  +"então pense bem.";

var BET_WINDOW=null;
var CONFIRM_WINDOW=null;

/** Match cards **/
var links=document.getElementsByClassName("bet-loader");
var linkLen=links.length;
var i;
for(i=0;i<linkLen;i++){
  links[i].addEventListener("click",clickPlayURL);
}

function clickPlayURL(e){
  e.preventDefault();
  if(BET_WINDOW) return;
  BET_WINDOW=makeLightbox(DEFAULT_LOADING_HTML);
  var ajax=new XMLHttpRequest();
  var link=e.currentTarget;
  var href=link.getAttribute("href");
  var id=href.slice(href.indexOf("id=")+3,href.length);
  ajax.open("GET",PLAY_MATCH_URL+"?id="+id,true);
  ajax.addEventListener("readystatechange",playPageLoaded);
  ajax.send(); 
}
function playPageLoaded(e){
  var ajax=e.currentTarget;
  if(ajax.readyState==4){
    if(ajax.status==200){
      BET_WINDOW.innerHTML=ajax.responseText;
      initMatchBox(BET_WINDOW);
    } else {
      console.log("Error: " + ajax.status);
    }
  }
} // End function play page loaded

function initMatchBox(box){
  makeBoxSlider(box);
  /* Form submission */
  var forms=box.getElementsByTagName("form");
  var formLen=forms.length;
  var closeBtns=box.getElementsByClassName("window-close");
  var closeLen=closeBtns.length;
  var i;
  for(i=0;i<closeLen;i++){
    closeBtns[i].addEventListener("click",closeBetWindow);
  }
  for(i=0;i<formLen;i++){
    forms[i].addEventListener("submit",sendMatchBet);
    function sendMatchBet(e){
      var form=e.currentTarget;
      e.preventDefault();
      var confirmed=confirm(CONFIRM_BET_STR);
      if(!confirmed){return;}
      var elem=form.elements;
      var id=elem.id.value;
      var type=elem.type.value
      var choice=elem.choice.value;
      if(id && type && choice){
        var query=
           "bet=1"
          +"&id="+encodeURIComponent(id)
          +"&type="+encodeURIComponent(type)
          +"&choice="+encodeURIComponent(choice);
        var ajax=new XMLHttpRequest();
        ajax.open("POST",PLAY_MATCH_URL+"?id="+id,true);
        ajax.addEventListener("readystatechange",playPageLoaded);
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        BET_WINDOW.innerHTML=DEFAULT_LOADING_HTML;
        CHANGE_MADE=true;
        ajax.send(query);
      } else {
        alert(OPTION_NOT_SELECTED);
      }
      
    } // End function send MatchBet
  }// End if form

} // End function init Match box

function closeBetWindow(e){
  if(e){e.preventDefault();}
  BET_WINDOW=null;
  closeLightbox();
  if(CHANGE_MADE){window.location.reload();}
}


function makeBoxSlider(box){
  //Initialize boxes
  var baseClass="match-card-box";
  var containers=box.getElementsByClassName(baseClass);
  //Take the things outside of the box.
  var uselessBox=box.getElementsByClassName("match-card-list")[0];
  while(uselessBox.firstChild){box.appendChild(uselessBox.firstChild);}
  uselessBox.parentElement.removeChild(uselessBox);
  if(containers.length<1) return;
  var firstBox=containers[0];
  var prevBox=null;
  var curPos=0;
  var curBox=containers[0];
  var nextBox=containers[1]?containers[1]:null;
  var c,cLen;
  var position=0;
  cLen=containers.length;
  box.setAttribute("class", "lightbox match-card-lightbox");
  
  
  
  //Make arrows
  var arrowLeft=document.createElement("a");
  arrowLeft.setAttribute("class","arrow-left");
  box.appendChild(arrowLeft);
  //
  var arrowRight=document.createElement("a");
  arrowRight.setAttribute("class","arrow-right");
  box.appendChild(arrowRight);
  
  
  arrowLeft.addEventListener("click",scrollRight);
  arrowRight.addEventListener("click",scrollLeft);
  window.addEventListener("resize",updateBoxes);
  arrowLeft.style.display="none";
  if(cLen==1){ arrowRight.style.display="none"; }
  updateBoxes();
  //Functions 
  function scrollLeft(){
    if(!nextBox){return;}
    curPos++;
    prevBox=curBox;curBox=nextBox;
    nextBox=containers[curPos+1]?containers[curPos+1]:null;
    updateBoxes();
    if(curPos==cLen-1){
      arrowRight.style.display="none";
    }
    arrowLeft.style.display="";
  } // End function scroll left
  function scrollRight(){
    if(!prevBox){return;}
    curPos--;
    nextBox=curBox;curBox=prevBox;
    prevBox=containers[curPos-1]?containers[curPos-1]:null;
    updateBoxes();
    if(curPos==0){
      arrowLeft.style.display="none";
    }
    arrowRight.style.display="";
  } // End function scroll right
  function updateBoxes(){
    if(prevBox){ prevBox.setAttribute("class",baseClass+" previous"); }
    curBox.setAttribute("class",baseClass+" current");
    if(nextBox){ nextBox.setAttribute("class",baseClass+" next"); }
    var moveLeft=getLeftWidth();
    curBox.insertBefore(arrowLeft,curBox.firstChild);
    curBox.appendChild(arrowRight);
    firstBox.style.marginLeft="-"+moveLeft+"px";
  }
  function getLeftWidth(){
    if(window.innerWidth<768){return 0;}
    var i;
    var sum=0;
    for(i=0;i<curPos;i++){
      sum+=containers[i].getBoundingClientRect().width;
    }
    return sum;
  }
} // End function make box slider


})();