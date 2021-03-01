"use strict";
(function(){
var questions=document.getElementsByClassName("question");
var selected=-1;
var i,qLen;
qLen=questions.length;
for(i=0;i<qLen;i++){
  addQuestion(questions[i]);
}
selectQuestion(-1);

function addQuestion(item){
  var title=item.getElementsByClassName("title")[0];
  var body=item.getElementsByClassName("body")[0];
  body.style.display="none";
  title.style.paddingBottom="20px";
  title.style.cursor="pointer";
  title.addEventListener("click",clickQuestion);
}
function clickQuestion(e){
  var title=e.currentTarget;
  var item=title.parentElement;
  for(i=0;i<qLen;i++){
    if(questions[i]==item){selectQuestion(i);}
  }

}
function selectQuestion(n){
  //Close selected question
  var item,body,title;
  if(selected>-1){
    item=questions[selected];
    body=item.getElementsByClassName("body")[0];
    title=item.getElementsByClassName("title")[0];
    body.style.display="none";
    title.style.paddingBottom="20px";
  }
  if(n<0||n>=qLen||n==selected){//Deselect all questions
    selected=-1;
  } else {
    //Open new question
    selected=n;
    item=questions[selected];
    body=item.getElementsByClassName("body")[0];
    title=item.getElementsByClassName("title")[0];
    body.style.display="";
    title.style.paddingBottom="";
  }
}

})();
