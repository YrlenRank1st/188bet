(function(){
var fbBtns=document.getElementsByClassName("facebook-login");
var fbLen=fbBtns.length;
var fb;
for(fb=0;fb<fbLen;fb++){
  fbBtns[fb].addEventListener("click",bet188_fbLogin);
}
})();
function bet188_fbLogin(e){
  e.preventDefault();
  var fbBtn=e.currentTarget;
  try{
    FB.login(fbLoggedIn);
  } catch(err){
    FB.checkLoginStatus(fbLoggedIn);
  }
  
  function fbLoggedIn(response){
    if(response.status=="connected"){
      var formBoxes=document.getElementsByClassName("login-box");
      var item,form;
      var input;
      item=fbBtn;
      do{
        form=null;
        while(item.parentElement){
          item=item.parentElement;
          if(item.nodeName.toLowerCase()=="form"){
            form=item;
            break;
          }
        }
        if(!form){
          console.error("Form not found.");
        }
        if(!response.authResponse || !response.authResponse.userID){
          console.error("Missing user ID.");
          break;
        }
        if(form.elements["facebookId"]){
          input=form.elements["facebookId"];
        } else {
          input=document.createElement("input");
          form.appendChild(input);
          input.name="facebookId";
          input.type="hidden";
        }
        input.value=response.authResponse.userID;
        form.submit();
      }while(0);
      //
    } // End if connected
  }// End fbLoggedIn
  
} // End fbLogin