"use strict";
(function(){
/** LOGIN SECTION **/
var LOGIN_SCREEN_URL="/logic/extern/login-form.php";
var LOGIN_API_URL="/logic/api/login.php";
var DEFAULT_LOADING_HTML="<div class=\"white-box lightbox-loader\"><span></span><p>Carregando...</p></div>";
var UNKNOWN_ERROR="Error: Contact the administrator. Give them this information: ";
/** Opening login screen **/
var loginBtn=document.getElementById("login");
var signupBtn=document.getElementById("register");
//
loginBtn.addEventListener("click",loadLoginScreen);
//
var loginDepth=null;
var loginBox=null;
var loginForm=null;
var cancelBtn=null;
function loadLoginScreen(e){
  if(location.href.indexOf("/login")>-1){return;}
  e.preventDefault();
  if(loginBox!=null){return;}
  loginDepth=getCurrentLightboxLevel();
  loginBox=makeLightbox(DEFAULT_LOADING_HTML);
  var ajax=new XMLHttpRequest();
  ajax.addEventListener("readystatechange",loginScreenLoaded);
  ajax.open("GET",LOGIN_SCREEN_URL,true);
  ajax.send();
}
function loginScreenLoaded(e){
  var ajax=e.currentTarget;
  var redirectField;
  if(ajax.readyState==4){
    if(ajax.status==200){
      //Load items
      loginBox.innerHTML=ajax.responseText;
      //Get login items
      cancelBtn=loginBox.getElementsByClassName("window-close")[0];
      loginForm=loginBox.getElementsByTagName("form")[0];
      //Prepare form
      redirectField=document.createElement("input");
      redirectField.type="hidden";
      redirectField.name="redirect";
      redirectField.value=location.href;
      loginForm.appendChild(redirectField);
      loginForm.action="/login";
      //Facebook login button
      var fbBtns=loginForm.getElementsByClassName("facebook-login");
      var fbLen=fbBtns.length;
      var fb;
      for(fb=0;fb<fbLen;fb++){
        fbBtns[fb].addEventListener("click",fbLogin);
      }
      //Event listeners
      cancelBtn.addEventListener("click",cancelLogin);
      loginForm.addEventListener("submit",submitLogin);
      //Make dynamic labels
      makeDynamicLabels();

    } else {
      //Login form not found. Redirect to login page.
      location.href="/login?redirect="+encodeURIComponent(location.href);
    }
  }
}

/**** LOGGING IN ****/
var LOGGING_IN=false;
var SUBMIT_VALID=false;

/** Facebook Login **/
function fbLogin(e){
  e.preventDefault();
  if(LOGGING_IN) return;
  try{
    FB.login(fbLoggedIn);
  } catch(err){
    FB.getLoginStatus(fbLoggedIn);
  }
}
function fbLoggedIn(response){
  if(loginForm==null){ return; }
  if(response.status!="connected"){
    return;
  }
  //Prepare request
  var facebookId=response.authResponse.userID;
  var redirect=loginForm.elements['redirect'].value;
  var query=
   "facebookId="+encodeURIComponent(facebookId)
   +"&redirect="+encodeURIComponent(redirect);
  var input;
  if(loginForm.elements["facebookId"]){
    input=loginForm.elements["facebookId"];
  } else {
    input=document.createElement("input");
    input.name="facebookId";
    input.type="hidden";
    loginForm.appendChild(input);
  }
  input.value=facebookId;
  //Send request
  var ajax=new XMLHttpRequest();
  ajax.addEventListener("readystatechange",loginAPIReturned);
  ajax.open("POST",LOGIN_API_URL,true);
  ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  ajax.send(query);
}
function submitLogin(e){
  if(SUBMIT_VALID) return;
  e.preventDefault();
  if(LOGGING_IN) return;
  
  //Prepare items
  LOGGING_IN=true;
  var buttons=loginForm.getElementsByClassName("cta");
  var btnLen=buttons.length;
  var i;
  for(i=0;i<btnLen;i++){
    buttons[i].style.background="#CCC";
    buttons[i].style.color="#EEE";
  }
  
  //Prepare request
  var elems=loginForm.elements;
  var username=elems['username'].value;
  var password=elems['password'].value;
  var redirect=elems['redirect'].value;
  var query=
       "username="+encodeURIComponent(username)
  +"&password="+encodeURIComponent(password)
  +"&redirect="+encodeURIComponent(redirect);
  //Send request
  var ajax=new XMLHttpRequest();
  ajax.addEventListener("readystatechange",loginAPIReturned);
  ajax.open("POST",LOGIN_API_URL,true);
  ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  ajax.send(query);
}

/** API **/
function loginAPIReturned(e){
  var ajax=e.currentTarget;
  var errorItems,errorMessage;
  var fields,firstField;
  if(ajax.readyState==4){
    //Cancel everything if the window closed
    if(loginForm==null){return;}
    //
    if(ajax.status==200){
      var result;
      try{
        result=JSON.parse(ajax.responseText);
        if(result.erro==true){
          //Display error message
          errorItems=loginForm.getElementsByClassName("invalid");
          if(!errorItems.length){
            fields=loginForm.getElementsByClassName("field");
            if(fields.length){
              firstField=fields[0];
            } else {
              firstField=loginForm.firstChild;
            }
            errorMessage=document.createElement("p");
            errorMessage.setAttribute("class","invalid");
            errorMessage.appendChild(document.createTextNode(""));
            firstField.parentElement.insertBefore(errorMessage,firstField);
            try{
              FB.logout(fbLoggedout);
            } catch (err){
              console.log("Error logging out: ",err);
            }
          } else {
            errorMessage=errorItems[0];
          }
          errorMessage.firstChild.nodeValue=result.message;
          LOGGING_IN=false;
        } else {
          //Success
          loginForm.submit();
          loginBox.innerHTML=DEFAULT_LOADING_HTML;
          return;
        }
        
      } catch(err){//JSON parse error
        if(err){ alert(UNKNOWN_ERROR+err+"\n\n"+ajax.responseText); }
      }
    } else { //Server error
      alert(UNKNOWN_ERROR+" login.js loginAPIReturned "+ajax.status);
    } //End if http code...
    LOGGING_IN=false;
    var buttons=loginForm.getElementsByClassName("cta");
    var btnLen=buttons.length;
    var i;
    for(i=0;i<btnLen;i++){
      buttons[i].style.background="";
      buttons[i].style.color="";
    }
  } // End if readystate=4 
}
function fbLoggedout(response){}

/** CLOSE **/
function cancelLogin(e){closeLogin();}
function closeLogin(){
  if(loginBox && loginDepth+1==getCurrentLightboxLevel()){
    closeLightbox();
    loginBox=null;
    loginDepth=null;
    cancelBtn=loginBox=loginForm=null;
  }
}
})();
