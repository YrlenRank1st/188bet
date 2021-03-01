(function(){
/* Open menu with menu button */
var btn=document.getElementsByClassName("mobile-button")[0];
var menu=document.getElementsByClassName("dyna-menu")[0];
var menuOpen=false;
var menuClass=menu.getAttribute("class");
btn.addEventListener("click",toggleMenu);
function toggleMenu(e){
  menuOpen=!menuOpen;
  menu.setAttribute("class",menuClass+(menuOpen?" open":""));
}
/* Open submenus */
var topMenu=menu.getElementsByClassName("menu")[0];
var submenuLinks=topMenu.getElementsByClassName("expanded");
var linkLen=submenuLinks.length;
var topMenuClass=topMenu.getAttribute("class");
var i;

//Initialize submenus
for(i=0;i<linkLen;i++){createExpandedMobile(submenuLinks[i]); }
function createExpandedMobile(tag){
  //Assume the menu only has one level.
  var submenu=tag.getElementsByClassName("menu")[0];
  var link=tag.getElementsByTagName("a")[0];
  var submenuClass=submenu.getAttribute("class");
  var submenuOpen=false;
  //Create back button
  var back=document.createElement("li");
  var backLink=document.createElement("a");
  backLink.href="#";
  submenu.insertBefore(back,submenu.firstChild);
  back.appendChild(backLink);
  backLink.appendChild(document.createTextNode("Voltar"));
  back.setAttribute("class","back-menu-link");
  //Track events
  link.addEventListener("click",toggleSubmenu);
  backLink.addEventListener("click",toggleSubmenu);
  function toggleSubmenu(e){
    e.preventDefault();
    submenuOpen=!submenuOpen;
    if(submenuOpen){
      topMenu.setAttribute("class",topMenuClass+" scrolled1");
      submenu.setAttribute("class",submenuClass+" open");
    } else {
      topMenu.setAttribute("class",topMenuClass);
      submenu.setAttribute("class",submenuClass);
    }
  }
}

})();
