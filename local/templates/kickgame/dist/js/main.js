"use strict";document.addEventListener("DOMContentLoaded",function(){var e=document.querySelector("body");e.classList.remove("preload");var t=document.getElementById("cookieSection"),n=document.querySelectorAll(".cookieAccept");function o(e){for(var t=document.cookie.split(";"),n=0;n<t.length;n++){var o=t[n].trim().split("=")||[];if(o!=[]&&e==o[0])return o[1]}return""}t&&(""===o("kickgame")&&(t.style.display="inherit"),n.forEach(function(e){e.addEventListener("click",function(e){var t;e.preventDefault(),t=o("kickgame"),e=document.getElementById("cookieSection"),""===t?(e.style.display="none",function(e,t){var n=new Date;n.setTime(n.getTime()+6048e5),n="expires="+n.toGMTString(),document.cookie=e+"="+t+";"+n+"; path=/",document.getElementById("cookieSection").style.display="none"}("kickgame","Accept")):e.style.display="inherit"})}));var i=document.querySelector(".inputFileScrinPubg"),a=document.querySelector(".fileUploadedScrinPubg");i&&a&&i.addEventListener("change",function(){p(i.files[0],a,i)});var r=document.querySelector(".inputFileScrinPubgNext"),l=document.querySelector(".fileUploadedScrinPubgNext");r&&l&&r.addEventListener("change",function(){p(r.files[0],l,r)});var c=document.querySelector(".inputFile"),s=document.querySelector(".fileUploaded");c&&s&&c.addEventListener("change",function(){p(c.files[0],s,c)});var d=document.querySelector(".inputFileAvatar"),u=document.querySelector(".fileAvatarUploaded");function p(e,t,n){if(!["image/jpeg","image/png","image/gif"].includes(e.type))return alert("Только изображения"),void(n.value="");20971520<e.size?alert("Файл не проходит по размеру"):((n=new FileReader).onload=function(e){console.log(e.target),t.setAttribute("style","background-image: url(".concat(e.target.result,")"))},n.onerror=function(e){alert("Err")},n.readAsDataURL(e))}d&&u&&d.addEventListener("change",function(){p(d.files[0],u,d)});var m=document.querySelector(".form-field__eyes");m&&m.addEventListener("click",function(){var e,t;e=".form-field__input_pass",t=m,"password"===(e=document.querySelector(e)).type?(e.type="text",t.className="form-field__eyes form-field__eyes_hide"):(e.type="password",t.className="form-field__eyes")});var f,v,y,g,h=document.getElementById("navbar-link"),_=document.getElementById("navbar__burger");f="navbar__burger",v="navbar-link",y=document.getElementById(f),g=document.getElementById(v),y&&g&&y.addEventListener("click",function(e){g.classList.toggle("show"),y.classList.toggle("active"),e.stopPropagation()}),h&&e.addEventListener("click",function(e){e.target.closest(".navbar-link")||(h.classList.remove("show"),_.classList.remove("active"))});e=document.querySelectorAll("[data-dropdown]");0<e.length&&e.forEach(function(e){!function(n){var e=n.querySelectorAll("option"),t=Array.prototype.slice.call(e),o=document.createElement("div");o.classList.add("dropdown"),n.insertAdjacentElement("afterend",o);var i=document.createElement("div");i.classList.add("dropdown__selected"),i.textContent=t[0].textContent,o.appendChild(i);var a=document.createElement("div");a.classList.add("dropdown__menu"),o.appendChild(a),i.addEventListener("click",function(){null!==this.offsetParent?this.style.display="none":(this.style.display="block",this.querySelector("input").focus())}.bind(a)),(e=document.createElement("input")).placeholder="Search...",e.type="text",e.classList.add("dropdown__menu_search"),a.appendChild(e);var r=document.createElement("div");r.classList.add("dropdown__menu_items"),a.appendChild(r),t.forEach(function(e){var t=document.createElement("div");t.classList.add("dropdown__menu_item"),t.dataset.value=e.value,t.textContent=e.textContent,r.appendChild(t),t.addEventListener("click",function(e,t,n){var o=this.dataset.value,i=this.textContent;e.textContent=i,t.value=o,n.style.display="none",n.querySelector("input").value="",n.querySelectorAll("div").forEach(function(e){e.classList.contains("selected")&&e.classList.remove("selected"),null===e.offsetParent&&(e.style.display="block")}),this.classList.add("selected")}.bind(t,i,n,a))}),r.querySelector("div").classList.add("selected"),e.addEventListener("input",function(t,e){var n=e.querySelectorAll(".dropdown__menu_items div"),o=this.value.toLowerCase(),i=t.filter(function(e){return e.textContent.toLowerCase().includes(o)}).map(function(e){return t.indexOf(e)});t.forEach(function(e){i.includes(t.indexOf(e))?null===n[t.indexOf(e)].offsetParent&&(n[t.indexOf(e)].style.display="block"):n[t.indexOf(e)].style.display="none"})}.bind(e,t,a)),document.addEventListener("click",function(e,t){null===t.target.closest(".dropdown")&&t.target!==this&&null!==e.offsetParent&&(e.style.display="none")}.bind(o,a)),n.style.display="none"}(e)})}),$(function(){$("#auth-phone").intlTelInput({initialCountry:"auto",geoIpLookup:function(t){jQuery.get("//ip-api.com/json/",function(){},"jsonp").always(function(e){e=e&&e.countryCode?e.countryCode:"ru";t(e)})},utilsScript:"/local/templates/kickgame/dist/js/utils.js",nationalMode:!1,formatOnDisplay:!0,hiddenInput:"full_number",preferredCountries:["ru"]});$(".core-list__delete-user").on("click",function(e){e.preventDefault(),$(this).closest(".card").remove()}),$(".core-team__user-avatar-delete-user").on("click",function(e){e.preventDefault(),$(this).closest(".card").remove()}),$(".form-team").validate({rules:{nameTeam:{required:!0},tagTeam:{required:!0},descriptionTeam:{required:!0},logoTeam:{required:!0}},messages:{nameTeam:{required:"Введи название команды"},tagTeam:{required:"Введи тег команды"},descriptionTeam:{required:"Введи описание команды"},logoTeam:{required:"Прикрепи логотип команды"}}}),$(".form-scrin-pubgid").validate({rules:{scrinPubg:{required:!0}},messages:{scrinPubg:{required:"Прикрепи скриншот"}}}),$(".form-scrin-pubgidnext").validate({rules:{scrinPubg:{required:!0},comments:{required:!0}},messages:{scrinPubg:{required:"Прикрепи скриншот"},comments:{required:"Введи текст"}}}),$("#c-all").on("change",function(){var e=$(this),t=e.closest(".flex-table").find('input[type="checkbox"]');e.is(":checked")?t.prop("checked",!0):t.prop("checked",!1)});var e={bootstrapCollapse:$(".card-custom"),customWaypoints:$("[data-custom-scroll-to]")};if(e.bootstrapCollapse.length)for(var t=0;t<e.bootstrapCollapse.length;t++)console.log("ok"),function(e){e.find("a.collapsed").length||e.addClass("active"),e.on("show.bs.collapse",function(){e.addClass("active")}),e.on("hide.bs.collapse",function(){e.removeClass("active")})}($(e.bootstrapCollapse[t]));if(e.customWaypoints.length)for(var n=0;n<e.customWaypoints.length;n++)$(e.customWaypoints[n]).on("click",function(e){e.preventDefault(),$("body, html").stop().animate({scrollTop:$("#"+$(this).attr("data-custom-scroll-to")).offset().top},1e3,function(){$(window).trigger("resize")})});0<$("body #bx-panel").length&&$(".header").css({top:"39px"}),$(function(){var t=!1;$(document).on("click","#ajax_next_page",function(e){if(e.preventDefault(),t)return!1;e=$(this).attr("href");t=!0,$.ajax({url:e,type:"POST",data:{IS_AJAX:"Y"},success:function(e){$("#ajax_next_page").after(e),$("#ajax_next_page").remove(),t=!1}})})}),$(".collapse").on("shown.bs.collapse",function(e){var t=$(this).closest(".card");$("html,body").animate({scrollTop:t.offset().top-"72"},500),e.stopPropagation()});var o=document.querySelectorAll(".inputFile");Array.prototype.forEach.call(o,function(e){var n=e.nextElementSibling,o=n.innerHTML;e.addEventListener("change",function(e){var t="";(t=this.files&&1<this.files.length?(this.getAttribute("data-multiple-caption")||"").replace("{count}",this.files.length):e.target.value.split("\\").pop())?n.querySelector("span").innerHTML=t:n.innerHTML=o})})});