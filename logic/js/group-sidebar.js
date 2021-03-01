"use strict";
(function(){
/** Global values **/
var INVITE_TITLE="Convite para grupo";
var DENY_TEXT="Recusar";
var ACCEPT_TEXT="Aceitar";
//var CONFIRM_DENY_STR="Are you sure you want to deny the group request?";
//var CONFIRM_ACCEPT_STR="Are you sure you want to accept the group request?";

/** Variables **/
var viewBtns=document.getElementsByClassName("view-group-request");
var btnLen,btn;
var INVITE_WINDOW=null;
var SELECTED_GROUP=null;
var LOADING=false;
/** Initialize **/
btnLen=viewBtns.length;
for(btn=0;btn<btnLen;btn++){
 viewBtns[btn].addEventListener("click",viewBtnClick);
}

/** Functions **/
function viewBtnClick(e){
  e.preventDefault();
  if(INVITE_WINDOW){return;}
  var item=e.currentTarget;
  var href=item.getAttribute("href");
  var id=href.slice(1,href.length);
  SELECTED_GROUP=id;
  var group=getGroupById(id,GROUP_REQUESTS);
  if(group){
    displayGroupRequest(group);
  }
}
function displayGroupRequest(group){
  var output="";
  //Box
  var whiteBox=document.createElement("div");
  whiteBox.setAttribute("class","white-box group-req-box");
  INVITE_WINDOW=whiteBox;
  //Close button
  var closeBtn=document.createElement("a");
  closeBtn.setAttribute("class","window-close");
  closeBtn.addEventListener("click",closeGroupRequest);
  //Title
  var title=document.createElement("h2");
  title.appendChild(document.createTextNode(INVITE_TITLE));
  
  //Group image
  var groupDiv=document.createElement("div");
  groupDiv.setAttribute("class","group");
  var imageDiv=document.createElement("div");
  imageDiv.setAttribute("class","group-image");
  var imageLimits=document.createElement("div");
  imageLimits.className="image-limits";
  var image=document.createElement("img");
  image.src=group.photo;
  
  //Group info
  var infoDiv=document.createElement("div");
  infoDiv.setAttribute("class","group-info");
  var groupName=document.createElement("span");
  groupName.setAttribute("class","group-name");
  groupName.appendChild(document.createTextNode(group.name));
  var groupText=document.createElement("span");
  groupText.setAttribute("class","group-text");
  groupText.appendChild(
    document.createTextNode(
       group.members.length
      +" pessoa"
      +((group.members.length>1)?"s":"")
    )
  );
  //Group members
  var members=group.members;
  var member;
  var memberLen=members.length;
  var m;
  var membersDiv=document.createElement("div");
  membersDiv.setAttribute("class","members");
  var memberDiv,mImageDiv,mNameDiv,memberImg,memberLevel;
  var mImageLimits;
  for(m=0;m<memberLen;m++){
     //Member: firstName,lastName,photo,level
     member=members[m];
     //
     memberDiv=document.createElement("div");
     memberDiv.setAttribute("class","member");
     //
     mImageDiv=document.createElement("div");
     mImageDiv.setAttribute("class","image");
     mImageLimits=document.createElement("div");
     mImageLimits.className="image-limits";
     //
     memberImg=document.createElement("img");
     memberImg.src=member.photo;
     //
     memberLevel=document.createElement("img");
     memberLevel.setAttribute("class","level-image");
     memberLevel.addEventListener("error",removeBrokenImage);
     memberLevel.src=getLevelImage(Number(member.level)+1);
     //
     mNameDiv=document.createElement("div");
     mNameDiv.setAttribute("class","name");
     mNameDiv.appendChild(document.createTextNode(member.firstName+" "+member.lastName));
     //
     mImageLimits.appendChild(memberImg);
     mImageLimits.appendChild(memberLevel);
     mImageDiv.appendChild(mImageLimits);
     memberDiv.appendChild(mImageDiv);
     memberDiv.appendChild(mNameDiv);
     membersDiv.appendChild(memberDiv);
  }
  //Buttons
  var btnDiv=document.createElement("div");
  btnDiv.setAttribute("class","action");
  var denyBtn=document.createElement("a");
  denyBtn.setAttribute("class","cta deny");
  denyBtn.addEventListener("click",denyRequest);
  denyBtn.appendChild(document.createTextNode(DENY_TEXT));
  var acceptBtn=document.createElement("a");
  acceptBtn.setAttribute("class","cta accept");
  acceptBtn.addEventListener("click",acceptRequest);
  acceptBtn.appendChild(document.createTextNode(ACCEPT_TEXT));
  
  //Join together
  imageLimits.appendChild(image);
  imageDiv.appendChild(imageLimits);
  infoDiv.appendChild(groupName);
  infoDiv.appendChild(groupText);
  groupDiv.appendChild(imageDiv);
  groupDiv.appendChild(infoDiv);
  btnDiv.appendChild(denyBtn);
  btnDiv.appendChild(acceptBtn);
  whiteBox.appendChild(closeBtn);
  whiteBox.appendChild(title);
  whiteBox.appendChild(groupDiv);
  whiteBox.appendChild(membersDiv);
  whiteBox.appendChild(btnDiv);
  var box=makeLightbox("");
  box.appendChild(whiteBox);
}

function closeGroupRequest(e){
  if(LOADING){return;}
  if(e){e.preventDefault();}
  if(INVITE_WINDOW){
    closeLightbox();
    INVITE_WINDOW=null;
    SELECTED_GROUP=null;
  }
}

function denyRequest(e){
  if(LOADING){return;}
  //var confirmed=confirm(CONFIRM_DENY_STR);
  var confirmed=true;
  if(confirmed){
    LOADING=true;
    
    var input,form;
    form=document.createElement("form");
    form.method="POST";
    input=document.createElement("input");
    input.value="deny";input.type="hidden";input.name="group-request";
    form.appendChild(input);
    input=document.createElement("input");
    input.value=SELECTED_GROUP;input.type="hidden";input.name="id";
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}
function acceptRequest(e){
  if(LOADING){return;}
  //var confirmed=confirm(CONFIRM_DENY_STR);
  var confirmed=true;
  if(confirmed){
    LOADING=true;
    var input,form;
    form=document.createElement("form");
    form.method="POST";
    input=document.createElement("input");
    input.value="accept";input.type="hidden";input.name="group-request";
    form.appendChild(input);
    input=document.createElement("input");
    input.value=SELECTED_GROUP;input.type="hidden";input.name="id";
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}


/** Useful functions **/
function getGroupById(id,list){
  var i,len;
  len=list.length;
  for(i=0;i<len;i++){
    if(list[i]["id"]==id){return list[i];}
  }
  return null;
}
function getLevelImage(level){
  return "/sites/default/files/images/extern/user-lv"+level+".png";
}
function removeBrokenImage(e){
  var item=e.currentTarget;
  item.parentElement.removeChild(item);
}


})();