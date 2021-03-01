"use strict";
(function(){
/** PARAMETERS **/
var USER_API_URL="/logic/extern/x-search-username.php";
var USER_REMOVE_API_URL="/logic/extern/x-group-remove-member.php";
var USER_INVITE_API_URL="/logic/extern/x-group-invite-member.php";
//
var REMOVE_IMG="/sites/default/files/images/icons/remove.png";
//
var USER_NOT_FOUND_STR="Usuario não encontrado!";
var USER_WAITING_STR="Aguardando confirmação";
var INVITE_USER_STR="Convidar";
var ADD_USER_STR="Adicionar";
var REMOVE_STR="Remover";
var IMAGE_NOT_UPLOADED_STR="Imagem não enviada.";
var FILE_IS_NOT_IMAGE="O arquivo enviado não é uma imagem.";
var USER_IN_GROUP_STR="O usuário já está no grupo.";
var CONFIRM_DELETE_GROUP1_STR="Tem certeza que deseja excluir o grupo '";
var CONFIRM_DELETE_GROUP2_STR="'?";
var CONFIRM_REMOVE_USER_STR="Você tem certeza que quer remover este membro?";

/** END PARAMETERS **/


/***/
/*** SECTION 1: MANAGING MEMBERS ***/
/***/

/** Variables **/
var LOADING=false;
var ACTIVE_MEMBER=null;
var inviteBox,groupsBox,nameInput,findBtn,newMemberBox;
inviteBox=document.getElementsByClassName("member-invite");
nameInput=document.getElementById("search-username");
findBtn=document.getElementById("search-user-btn");
newMemberBox=document.getElementById("added-member");
var groupForm=document.getElementById("edit-group-form");

/** Initialize **/
var startingMembers=document.getElementsByClassName("member");
var numStartingMembers=startingMembers.length;
var mPos,sMember,sMemberLink;

for(mPos=0;mPos<numStartingMembers;mPos++){
  sMember=startingMembers[mPos];
  sMemberLink=sMember.getElementsByClassName("remove-member");
  if(sMemberLink.length){
    sMemberLink[0].addEventListener("click",removeFromGroup);
  }
}

/** Actions **/
if(inviteBox.length && nameInput && findBtn && newMemberBox){

inviteBox=inviteBox[0];
groupsBox=inviteBox.parentElement;
findBtn.addEventListener("click",findBtnClicked)
nameInput.addEventListener("keydown",nameInputInserted);

function findBtnClicked(e){
  e.preventDefault();
  if(LOADING){ return; }
  getUserByName(nameInput.value);
}
function nameInputInserted(e){
  if(LOADING){ return; }
  var code=(e.charCode?e.charCode:e.keyCode);
  if(code==13){
    e.preventDefault();
    getUserByName(nameInput.value);
  }
}
function getUserByName(name){
  if(!(name && name.length)){ return; }
  var ajax=new XMLHttpRequest();
  ajax.open("GET",USER_API_URL+"?name="+encodeURIComponent(name),true);
  ajax.addEventListener("readystatechange",userReturned);
  //Loading enabled
  LOADING=true;
  nameInput.style.color="#444";
  while(newMemberBox.firstChild){newMemberBox.removeChild(newMemberBox.firstChild);}
  //
  
  ajax.send();
  function userReturned(e){
    if(ajax.readyState==4){
      LOADING=false;
      nameInput.style.color="";
      if(ajax.status==200){
        //
        var errBox=document.createElement("p");
        errBox.setAttribute("class","invalid");
        try{
          var obj=JSON.parse(ajax.responseText);
          //Display user information
          while(newMemberBox.firstChild){newMemberBox.removeChild(newMemberBox.firstChild);}
          if(obj.erro){
            errBox.appendChild(document.createTextNode(obj.message));
            newMemberBox.appendChild(errBox);
          } else {
            ACTIVE_MEMBER=obj;
            displayMember(newMemberBox,obj.data,true);
          }
        }catch(err){
          //Consider it a failure: 
          console.log("FATAL ERROR: ",err);
          errBox.appendChild(document.createTextNode(USER_NOT_FOUND_STR));
          newMemberBox.appendChild(errBox);
        }
      } else {
        //Error
      }
    }
  }
 
}

} // End if all items exist

function displayMember(box,member,isTemporary){
  /**
   * Displays a user's information in the box.
   * Member has the fields: level,firstName,lastName,photo,id
   */
  var icon,name,mStatus,action;
  var iconDiv,userImg,levelImg,inviteBtn;
  var levelSrc,inviteTxt;
  //Icon
  icon=document.createElement("div");
  icon.setAttribute("class","member-icon user-image");
  iconDiv=document.createElement("div");
  icon.appendChild(iconDiv);
  //
  userImg=document.createElement("img");
  userImg.src=member.photo;
  iconDiv.appendChild(userImg);
  //
  levelSrc=getLevelImage(Number(member.level)+1);
  if(levelSrc && levelSrc.length){
    levelImg=document.createElement("img");
    levelImg.setAttribute("class","level-image");
    levelImg.addEventListener("error",removeBrokenImage);
    levelImg.src=levelSrc;
    iconDiv.appendChild(levelImg);
  }
  box.appendChild(icon);

  //Name
  name=document.createElement("div");
  name.setAttribute("class","member-name");
  name.appendChild(document.createTextNode(member.firstName+" "+member.lastName));
  box.appendChild(name);

  //Status
  mStatus=document.createElement("div");
  mStatus.setAttribute("class","member-status");
  box.appendChild(mStatus);

  //Action
  action=document.createElement("div");
  action.setAttribute("class","member-action");
  box.appendChild(action)

  if(isTemporary){
    inviteBtn=document.createElement("a");
    inviteBtn.setAttribute("class","white-btn invite-btn");
    inviteBtn.href="#"+member.id;
    inviteTxt=(GROUP_EDIT_MODE?INVITE_USER_STR:ADD_USER_STR);
    inviteBtn.appendChild(document.createTextNode(inviteTxt));
    action.appendChild(inviteBtn);;
    if(GROUP_EDIT_MODE){
      action.addEventListener("click",inviteMember);
    } else {
      action.addEventListener("click",addUserToNewGroup);
    }
  } else {
    //Remove member
      var removeBtn=document.createElement("a");
      removeBtn.setAttribute("class","remove-member");
      removeBtn.href="#"+member.id;

      var removeTxt=document.createElement("span");
      removeTxt.setAttribute("class","action");
      removeTxt.appendChild(document.createTextNode(REMOVE_STR));
      removeBtn.appendChild(removeTxt);
      if(GROUP_EDIT_MODE){
        removeBtn.addEventListener("click",removeFromGroup);
      } else {
        removeBtn.addEventListener("click",removeMember);
      }
      var removeImg=document.createElement("img");
      removeImg.src=REMOVE_IMG;
      removeBtn.appendChild(removeImg);

      action.appendChild(removeBtn);

    var hiddenInput=document.createElement("input");
    hiddenInput.type="hidden";
    hiddenInput.name="member[]";
    hiddenInput.value=member.id;
    box.appendChild(hiddenInput);
  } // End is not temporary
}

function addUserToNewGroup(e){
  /** Copy member to member list **/
  e.preventDefault();
  if(ACTIVE_MEMBER){
    while(newMemberBox.firstChild){newMemberBox.removeChild(newMemberBox.firstChild);}
    var moveMemberBox=document.createElement("div");
    moveMemberBox.setAttribute("class","member member-box");
    moveMemberBox.setAttribute("id","user-"+ACTIVE_MEMBER.data.id);
    displayMember(moveMemberBox,ACTIVE_MEMBER.data,false);
    groupsBox.insertBefore(moveMemberBox,inviteBox);
    ACTIVE_MEMBER=null;
  }
}

function inviteMember(e){
  /** Create this one later **/
  e.preventDefault();
  if(ACTIVE_MEMBER){
    var memberId=ACTIVE_MEMBER.data.id;
    var groupId=groupForm.elements["id"].value;
    var query="id="+groupId+"&member_id="+memberId;
    
    //Check if member already exists
    var memberBox=document.getElementById("user-"+memberId);
    if(memberBox){
      alert(USER_IN_GROUP_STR);
      return;
    }
    
    //
    var ajax=new XMLHttpRequest();
    ajax.open("POST",USER_INVITE_API_URL);
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    ajax.addEventListener("readystatechange",memberInvited);
    ajax.send(query);
    
  }
}
function memberInvited(e){
  var ajax=e.currentTarget;
  var obj;
  if(ajax.readyState==4){
    //{"data":null,"erro":false,"errors":null,"message":"Sucesso","messageEx":null}
    try{
      obj=JSON.parse(ajax.responseText);
      if(obj.erro){
        alert(obj.message);
      } else {
        //Add member to list
        
        while(newMemberBox.firstChild){newMemberBox.removeChild(newMemberBox.firstChild);}
        var moveMemberBox=document.createElement("div");
        moveMemberBox.setAttribute("class","member member-box");
        moveMemberBox.setAttribute("id","user-"+ACTIVE_MEMBER.data.id);
        displayMember(moveMemberBox,ACTIVE_MEMBER.data,false);
        groupsBox.insertBefore(moveMemberBox,inviteBox);
        ACTIVE_MEMBER=null;
        //Waiting
        var statusDiv=moveMemberBox.getElementsByClassName("member-status")[0];
        var statusText=document.createElement("span");
        statusText.setAttribute("class","status");
        statusText.appendChild(document.createTextNode(USER_WAITING_STR));
        statusDiv.appendChild(statusText);
        
        //Remove delete button
        var removeBtn=moveMemberBox.getElementsByClassName("remove-member")[0];
        if(removeBtn){
          removeBtn.parentElement.removeChild(removeBtn);
        }
        
      }
    }catch(err){
      console.log(err,ajax.responseText);
    }
  }
}

function removeMember(e){
  e.preventDefault();
  var item=e.currentTarget;
  var itemClass;
  var box=null;
  //Find the nearest parent with the class "member-box"
  while(item.parentElement){
    item=item.parentElement;
    itemClass=" "+item.getAttribute("class")+" ";
    if(itemClass.indexOf(" member-box ")>-1){
      box=item;
      break;
    }
  }
  if(box){box.parentElement.removeChild(box);}

}

function removeFromGroup(e){
  // Removes the selected member from the group
  e.preventDefault();
  if(!(
     groupForm
  && groupForm.elements['id']
  )){
    return;
  }
  var confirmed=confirm(CONFIRM_REMOVE_USER_STR);
  if(!confirmed) return;
  //
  
  var link=e.currentTarget;
  var href=link.getAttribute("href");
  var memberId=href.slice(1,href.length);
  var groupId=groupForm.elements['id'].value;

  var ajax=new XMLHttpRequest();
  ajax.open("POST",USER_REMOVE_API_URL,true);
  ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8");
  ajax.addEventListener("readystatechange",removedFromGroup);
  ajax.send(
     "id="+encodeURIComponent(groupId)
    +"&member_id="+encodeURIComponent(memberId)
  );
  function removedFromGroup(e){
    if(ajax.readyState==4){
      try{
        var obj=JSON.parse(ajax.responseText);
        if(obj.erro){
          alert(obj.message);
        } else {
          var memberBox=document.getElementById("user-"+memberId);
          if(memberBox){
            memberBox.parentElement.removeChild(memberBox);
          }
          
        }
      }catch(err){
        console.log(err,ajax.responseText);
      }
    }
  }
}

/***/
/*** SECTION 2: IMAGE UPLOAD ***/
/***/
var imageUploadBtn=document.getElementById("group-image-edit");
var imageUploadPreview=document.getElementById("group-image-preview");
var imageUploadInput=document.getElementById("group-image-input");
imageUploadBtn.addEventListener("click",uploadImage);
function uploadImage(e){
  e.preventDefault();
  imageUploadInput.addEventListener("change",filesChanged);
  imageUploadInput.click();
  function filesChanged(e){
    var files=imageUploadInput.files;
    imageUploadInput.removeEventListener("change",filesChanged);
    if(files.length){
      var file=files[0];
      if(file.type.indexOf("image")==-1){
        imageUploadInput.value=null;
        alert(FILE_IS_NOT_IMAGE);
        return;
      }
      var imageData=null;
      var reader= new FileReader();
      reader.addEventListener("load",readerLoaded);
      reader.addEventListener("error",readerError);
      reader.readAsDataURL(file);
      function readerLoaded(e){
        imageData=reader.result;
        imageUploadPreview.src=imageData;
      }
      function readerError(e){
        alert(IMAGE_NOT_UPLOADED_STR);
      }
    } // End if files
  } // End file input changed
} // End function uploadImage

/***/
/*** SECTION 3: DELETE THE GROUP ***/
/***/
var groupDeleteBtn=document.getElementById("group-delete-btn");
if(groupDeleteBtn){
  groupDeleteBtn.addEventListener("click",deleteGroup);
  function deleteGroup(e){
    e.preventDefault();
    var item=e.currentTarget;
    var href=item.getAttribute("href");
    href=href.slice(1,href.length);
    var barPos=href.indexOf("|");
    if(barPos==-1){
      alert("Error: Could not identify group!");
    } else {
      var id=href.slice(0,barPos);
      var name=href.slice(barPos+1,href.length);
      var confirmed=confirm(
         CONFIRM_DELETE_GROUP1_STR
        +name
        +CONFIRM_DELETE_GROUP2_STR
      );
      if(!confirmed){return;}
      //Submit
      var input,form;
      form=document.createElement("form");
      form.method="POST";
      input=document.createElement("input");
      input.name="group-delete";input.value="1";input.type="hidden";
      form.appendChild(input);
      input=document.createElement("input");
      input.name="id";input.value=id;input.type="hidden";
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    }
  }
}


/***/
/*** SECTION 4: SUBMIT THE FORM ***/
/***/
var saveBtn=document.getElementById("submit-group-btn");
if(saveBtn){
  saveBtn.addEventListener("click",submitGroup);
}
function submitGroup(e){
  e.preventDefault();
  groupForm.submit();
}

/***/
/*** RANDOM EVENTS ***/
/***/

function removeBrokenImage(e){
  var item=e.currentTarget;
  item.parentElement.removeChild(item);
}
/***/
/*** RANDOM FUNCTIONS ***/
/***/

function getLevelImage(level){
  return "/sites/default/files/images/extern/user-lv"+level+".png";
}

})();