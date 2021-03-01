(function(){
/*
<iframe
  width="560" height="315"
  src="YOUTUBE URL"
  frameborder="0"
  allow="encrypted-media" allowfullscreen>
</iframe>

https://img.youtube.com/vi/b0j7yA5SLH4/mqdefault.jpg
*/
var youtubeList=document.getElementsByClassName("youtube-list");
var yLen,y;
yLen=youtubeList.length;

for(y=0;y<yLen;y++){makeVideoList(youtubeList[y]);}

/* Make each video box dynamic */
function makeVideoList(box){
  var videoBox=box.getElementsByClassName("video")[0];
  if(!videoBox){return;}
  var links=box.getElementsByClassName("youtube-link");
  var linkLen=links.length;
  var link,a;
  var i;
  while(videoBox.firstChild){videoBox.removeChild(videoBox.firstChild);}
  for(i=0;i<linkLen;i++){
    link=links[i];
    a=link.getElementsByTagName("a")[0];
    if(a){makeVideoLink(link,videoBox,(i==0));}
  }
}

/* Make each link in a box load a video */
function makeVideoLink(link,videoBox,first){
  var a=link.getElementsByTagName("a")[0];
  if(!a){return;}
  var linkClass=link.className;
  a.addEventListener("click",loadVideo);
  var url=a.href;
  var id=getYoutubeID(url);
  //Image
  var imgUrl=getYoutubeImageURL(id);
  var img=document.createElement("img");
  img.src=imgUrl;
  img.addEventListener("click",playVideo);
  img.style.cursor="pointer";
  var playCircle=createPlayCircle();
  if(first){loadVideo(null);}
  //Video
  var video=getYoutubeIframe(id);
  
  function loadVideo(e){
    if(e){e.preventDefault();}
	var selected=link.parentElement.getElementsByClassName("selected")[0];
    if(selected){
      if(selected===link){return;}
      selected.className=selected.className.slice(0,selected.className.indexOf("selected")-1);
    }
    while(videoBox.firstChild){videoBox.removeChild(videoBox.firstChild);}
    videoBox.appendChild(img);
    videoBox.appendChild(playCircle);
    link.className=linkClass+" selected";
  }
  function playVideo(e){
    if(e){e.preventDefault();}
    while(videoBox.firstChild){videoBox.removeChild(videoBox.firstChild);}
    videoBox.appendChild(video);
  }
}

/* Create objects */
function createPlayCircle(){
  //Container
  var cS,pS;
  var container=document.createElement("div");
  cS=container.style;
  cS.background="#ef5350";
  cS.boxSizing="border-box";
  cS.width=cS.height="50px";
  cS.textAlign="center";
  cS.position="absolute";
  cS.top="50%";
  cS.left="50%";
  cS.transform="translateY(-50%) translateX(-50%)";
  cS.pointerEvents="none";
  cS.borderRadius="25px";
  cS.paddingTop="14px";
  //Play button
  var playBtn=document.createElement("div");
  container.appendChild(playBtn);
  pS=playBtn.style;
  pS.margin="0 auto";
  pS.width=pS.height="0";
  pS.borderTop=pS.borderBottom="solid 11px transparent";
  pS.borderLeft="solid 15px #FFF";
  return container;
}
/* Youtube ID functions */
function getYoutubeID(url){
  var vPos,ePos,qPos,hPos,nextPos,minPos;
  var vPos=url.indexOf("v=");
  if(vPos==-1){
    //Test for youtu.be/<ID>
    ePos=url.indexOf("youtu.be/");
    if(ePos==-1){return "";}
    qPos=url.indexOf("?",ePos);
    hPos=url.indexOf("#",ePos);
    minPos=Math.min(qPos,hPos);
    if(qPos==-1){
      return url.slice(ePos+9,url.length);
    } else {
      return url.slice(ePos+9,qPos);
    }
    
  } // End if not v=
  nextPos=url.indexOf("&",vPos);
  hPos=url.indexOf("#",vPos);
  minPos=Math.min(nextPos,hPos);
  if(minPos==-1){
    return url.slice(vPos+2,url.length);
  } else {
    return url.slice(vPos+2,nextPos);
  }
}
function getYoutubeIframe(id){
  var iframe=document.createElement("iframe");
  iframe.width="300";
  iframe.height="174";
  iframe.setAttribute("src","https://www.youtube.com/embed/"+id+"?autoplay=1");
  iframe.setAttribute("frameborder",0);
  iframe.setAttribute("allow","autoplay; encrypted-media");
  iframe.setAttribute("allowfullscreen","");
  return iframe;
}
function getYoutubeImageURL(id){
  return "https://img.youtube.com/vi/"+id+"/mqdefault.jpg";
}

})();