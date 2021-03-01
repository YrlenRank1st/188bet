(function(){
var DEFAULT_LOADING_HTML="<div class=\"white-box lightbox-loader\"><span></span><p>Carregando...</p></div>";

/** LOGIN MENU **/
var loginClickZone=document.getElementsByClassName("login-menu-btn");
var loginMenus=document.getElementsByClassName("login-menu");
if(loginClickZone.length && loginMenus.length){
  var loginMenu=loginMenus[0];
  var loginClass=loginMenu.getAttribute("class");
  loginClickZone[0].addEventListener("click",toggleLoginMenu);
  var loginMenuOpen=false;
  function toggleLoginMenu(e){
    e.stopPropagation();
    loginMenuOpen=!loginMenuOpen;
    loginMenu.setAttribute("class",loginClass+(loginMenuOpen?" open":""));
    if(notesOpen){
      notesOpen=!notesOpen;
      notesList.setAttribute("class",notesClass+(notesOpen?" open":""));
    }
  }
}

/** NOTIFICATION BELL **/
var bell=document.getElementsByClassName("notification-bell");
var notesLists=document.getElementsByClassName("notifications");
var notesOpen=false;
if(bell.length && notesLists.length){
  bell[0].addEventListener("click",toggleNotifications);
  var notesList=notesLists[0];
  var notesClass=notesList.getAttribute("class");
  function toggleNotifications(e){
    e.stopPropagation();
    notesOpen=!notesOpen;
    notesList.setAttribute("class",notesClass+(notesOpen?" open":""));
    if(loginMenuOpen){
      loginMenuOpen=!loginMenuOpen;
      loginMenu.setAttribute("class",loginClass+(loginMenuOpen?" open":""));
    }
  }
}
/** Close all menus **/
var main=document.getElementsByTagName("main")[0];
var footer=document.getElementsByClassName("main-footer")[0];
main.addEventListener("click",closeAllMenus);
footer.addEventListener("click",closeAllMenus);
function closeAllMenus(e){
  if(loginMenuOpen){
    loginMenuOpen=!loginMenuOpen;
    loginMenu.setAttribute("class",loginClass+(loginMenuOpen?" open":""));
  }
  if(notesOpen){
    notesOpen=!notesOpen;
    notesList.setAttribute("class",notesClass+(notesOpen?" open":""));
  }
}

/** LOGIN MENU ITEM: INVITE FRIEND **/
var INVITE_FRIEND_BOX=null;
var INVITE_FRIEND_API_URL="/logic/user/x-invite-friend.php";
var INVITE_FRIEND_URL="/invite-friend";
var inviteBtns=document.getElementsByClassName("friend-invite-btn");
var iFBtn,iFLen;
iFLen=inviteBtns.length;
for(iFBtn=0;iFBtn<iFLen;iFBtn++){
  inviteBtns[iFBtn].addEventListener("click",openInviteFriendBox);
}
function openInviteFriendBox(e){
  e.preventDefault();
  if(INVITE_FRIEND_BOX) return;
  if(location.href.indexOf(INVITE_FRIEND_URL)>-1){
    return;
  }
  INVITE_FRIEND_BOX=makeLightbox(DEFAULT_LOADING_HTML);
  var link=e.currentTarget;
  var ajax=new XMLHttpRequest();
  ajax.open("GET",INVITE_FRIEND_API_URL,true);
  ajax.addEventListener("readystatechange",inviteFriendLoaded);
  ajax.send();
  function inviteFriendLoaded(e){
    if(ajax.readyState==4){
      if(ajax.status==200){
        INVITE_FRIEND_BOX.innerHTML=ajax.responseText;
        initInviteFriendForm(INVITE_FRIEND_BOX);
      } else {
        link.click();//Error. Open in separate page.
        console.log("FAIL",e);
      }
    }
  }
  function initInviteFriendForm(box){
    //Close
    var closeBtn=box.getElementsByClassName("window-close")[0];
    if(closeBtn){ closeBtn.addEventListener("click",closeInviteFriendForm); }
    
    //Form
    var form=box.getElementsByTagName("form")[0];
    if(form) {form.action=INVITE_FRIEND_URL; }
    makeDynamicLabels();
    
    form.addEventListener("submit",inviteFriend);
  }
  function inviteFriend(e){
    e.preventDefault();
    var form=e.currentTarget;
    //
    query="invite-friend=1&email="+encodeURIComponent(form.elements.email.value);
    //
    ajax=new XMLHttpRequest();
    ajax.open("POST",INVITE_FRIEND_API_URL,true);
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    
    ajax.addEventListener("readystatechange",inviteFriendLoaded);
    INVITE_FRIEND_BOX.innerHTML=DEFAULT_LOADING_HTML;
    
    ajax.send(query);
    
  }
  function closeInviteFriendForm(e){
    e.preventDefault();
    if(INVITE_FRIEND_BOX){
      INVITE_FRIEND_BOX=null;
      closeLightbox();
    }
  }
}
})();