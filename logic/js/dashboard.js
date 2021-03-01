"use strict";
(function(){
var IMAGE_NOT_UPLOADED_STR="Imagem não enviada.";
var FILE_IS_NOT_IMAGE="O arquivo enviado não é uma imagem.";

/** CHANGE IMAGE **/
var editImageBtn=document.getElementsByClassName("edit-user-image")[0];
var userImage,imageBox,imageForm,imageInput,imageSubmitBtn;


if(editImageBtn){
/* Initialize */
imageBox=editImageBtn.parentElement;
editImageBtn.addEventListener("click",selectNewImage);
userImage=imageBox.getElementsByTagName("img")[0];

//Form
imageForm=imageBox.getElementsByTagName("form")[0];
imageInput=imageBox.getElementsByTagName("input")[0];
imageSubmitBtn=imageBox.getElementsByTagName("button")[0];
imageInput.addEventListener("change",imageSelected);

/** Select new image **/
function selectNewImage(e){
  e.preventDefault();
  imageInput.click();
}
function imageSelected(e){
  var files=imageInput.files;
  if(files.length){
    var file=files[0];
    
    if(file && file.type.indexOf("image")==-1){
      imageInput.value=null;
      alert(FILE_IS_NOT_IMAGE);
      return;
    }
    
    var reader= new FileReader();
    var imageData=null;
    reader.addEventListener("load",readerLoaded);
    reader.addEventListener("error",readerError);
    reader.readAsDataURL(file);
    function readerLoaded(e){
      imageData=reader.result;
      userImage.src=imageData;
      imageSubmitBtn.style.display="inline-block";
    }
    function readerError(e){
      alert(IMAGE_NOT_UPLOADED_STR);
    }
  } // End if files
}
function submitImage(e){
  if(!imageInput.value){e.preventDefault();}
}

} // End if edit button

/** FACEBOOK CONNECT **/
var fbBtns=document.getElementsByClassName("fb-connect");
var fbLen,fb;
fbLen=fbBtns.length;
for(fb=0;fb<fbLen;fb++){
  initFBBtn(fbBtns[fb]);
}

function initFBBtn(fbBtn){
  fbBtn.addEventListener("click",openFB);
}

function openFB(e){
  try{
    FB.login(fbConnected);
  } catch(err){
    FB.getLoginStatus(fbConnected);
  }
}
function fbConnected(response){
  if(response.status=="connected"
  && response.authResponse
  && response.authResponse.userID){
    //User ID connected
    var form=document.createElement("form");
    form.method="POST";
    var input=document.createElement("input");
    input.type="hidden";
    input.name="facebookId";
    input.value=response.authResponse.userID;
    form.appendChild(input);
    input=document.createElement("input");
    input.type="hidden";
    input.name="fb-connect";
    input.value=1;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}

/** FACEBOOK DISCONNECT **/
var fbDisconnectForm=document.getElementById("fb-disconnect-form");
if(fbDisconnectForm){
  fbDisconnectForm.addEventListener("submit",fbLogout);
  
  function fbLogout(e){
    e.preventDefault();
    FB.getLoginStatus(fbLoggingOut);
    
  }
  function fbLoggingOut(response){
    if(response.status==="connected"){
      //Revoke permissions
      if(response.authResponse
      && response.authResponse.userID){
        FB.api(
          "/"+response.authResponse.userID+"/permissions",
          "delete",
          permissionsRevoked
        );
      } else {
        FB.logout(fbLoggedOutComplete);
      }
    } else {
      fbDisconnectForm.submit();
    }
  }
}
function permissionsRevoked(response){
  fbDisconnectForm.submit();
}
function fbLoggedOutComplete(response){
  fbDisconnectForm.submit();
}


})();