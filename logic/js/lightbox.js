"use strict";
var LIGHTBOX_BASE_DEPTH=400;
var LIGHTBOX_DEPTH_STEP=10;
var LIGHTBOX_DEPTH=0;
var LIGHTBOX_STACK;
if(!LIGHTBOX_STACK){ LIGHTBOX_STACK=[]; }
function makeLightbox(content){
  /**
   * Opens a popup window containing the data given in the first parameter.
   * The popup window is added to the stack, printed in front of the previous one.
   */
  var div=document.createElement("div");
  div.style.zIndex=LIGHTBOX_BASE_DEPTH+LIGHTBOX_DEPTH_STEP*LIGHTBOX_STACK.length;
  div.setAttribute("class","lightbox");
  div.innerHTML=content;
  document.body.appendChild(div);
  LIGHTBOX_STACK[LIGHTBOX_DEPTH]=div;
  LIGHTBOX_DEPTH++;
  return div;
}
function closeLightbox(){
  /**
   * Closes the topmost lightbox.
   * Reduces the lightbox stack by one.
   * Returns a boolean indicating that a lightbox was closed.
   */
  if(LIGHTBOX_DEPTH==0){ return false; }
  LIGHTBOX_DEPTH--;
  var div=LIGHTBOX_STACK[LIGHTBOX_DEPTH];
  div.parentElement.removeChild(div);
  delete LIGHTBOX_STACK[LIGHTBOX_DEPTH];
  LIGHTBOX_STACK.length=LIGHTBOX_DEPTH;
  return true;
}
function closeAllLightboxes(){
  while(LIGHTBOX_DEPTH>0){closeLightbox();}
}
function getCurrentLightboxLevel(){
  return LIGHTBOX_DEPTH;
}