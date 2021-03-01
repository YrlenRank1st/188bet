"user strict";
(function(){
var CONFIRM_EXIT_STR="Você tem certeza que quer sair do grupo?";

/** EXIT GROUP **/
var exitGroupBtn=document.getElementsByClassName("exit-group-btn")[0];
if(exitGroupBtn){
  exitGroupBtn.addEventListener("click",confirmExit);
  function confirmExit(e){
    var confirmed=confirm(CONFIRM_EXIT_STR);
    if(!confirmed){e.preventDefault();}
  }
}

})();