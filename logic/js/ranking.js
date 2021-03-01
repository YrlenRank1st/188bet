(function(){
var RANKING_QUERY="/logic/extern/x-ranking.php";
var LEVEL_IMG="/sites/default/files/images/extern/user-lv";
var ERROR_MSG="Desculpe, aconteceu um erro.";

var btn=document.getElementById("ranking-load-more");
var table=document.getElementById("ranking-table");
if(!(btn && table)){return;}
var body=table.getElementsByTagName("tbody")[0];
if(!body){body=table;}
var LOADING=false;
var btnText=btn.firstChild;
var btnString=btnText.nodeValue;

btn.addEventListener("click",loadMore);

/* Functions */
function loadMore(e){
  if(LOADING){return;}
  e.preventDefault();
  nextPage=RANKING_CUR_PAGE+1;
  //RANKING_CUR_TYPE
  var query=RANKING_QUERY+"?type="+RANKING_CUR_TYPE+"&page="+nextPage;
  btnText.nodeValue="Carregando...";
  
  var ajax=new XMLHttpRequest();
  ajax.open("GET",query,true);
  ajax.addEventListener("readystatechange",moreLoaded);
  ajax.send();

}
function moreLoaded(e){
  var ajax=e.currentTarget;
  if(ajax.readyState==4){
    LOADING=false;
    RANKING_CUR_PAGE++;
    btnText.nodeValue=btnString;
    try{
      var obj=JSON.parse(ajax.responseText);
      var data=obj.data;
      var len=data.length;
      var i;
      for(i=0;i<len;i++){
        addUser(data[i],body);
      }
      if(len==0){
        btn.removeEventListener("click",loadMore);
      }
    } catch(err){
      alert(ERROR_MSG);
      console.log(ajax.responseText);
      console.log(err);
    }
  }
}

function addUser(item,par){
  //photo,level,firstName,lastName,position,score,id
  var tr=document.createElement("tr");
  var tdPos=document.createElement("td");
  var tdUser=document.createElement("td");
  var tdPoints=document.createElement("td");
  var span,divImg,divName,userImg,levelImg,a;
  //
  
  //
  if(Number(item.position)==1){tr.className="winner";}
  tdPos.className="position";
  tdUser.className="user";
  tdPoints.className="points";
  tr.appendChild(tdPos);
  tr.appendChild(tdUser);
  tr.appendChild(tdPoints);
  //Position
  tdPos.className="position";
  span=document.createElement("span");
  span.appendChild(document.createTextNode(item.position));
  tdPos.appendChild(span);
  //User
  divImg=document.createElement("div");
  divImg.className="user-image";
  userImg=document.createElement("img");
  userImg.src=item.photo;
  divImg.appendChild(userImg);
  if(item.level>0){
    levelImg=document.createElement("div");
    levelImg.className="level-image";
    levelImg.addEventListener("error",imageFailed);
    levelImg.src=LEVEL_IMG+item.level+".png";
    divImg.appendChild(levelImg);
  }
  //
  divName=document.createElement("div");
  divName.className="user-name";
  a=document.createElement("a");
  a.href="/user/page/?id="+item.id;
  a.appendChild(document.createTextNode(item.firstName+" "+item.lastName));
  divName.appendChild(a);
  //
  tdUser.appendChild(divImg);
  tdUser.appendChild(divName);
  
  //Score
  var score=String(item.score);
  score=score.replace(".",",");
  span=document.createElement("span");
  span.appendChild(document.createTextNode(score));
  tdPoints.appendChild(span);
  //
  par.appendChild(tr);
} // End function addUser

function imageFailed(e){
  var img=e.currentTarget;
  img.parentElement.removeChild(img);
}
})();