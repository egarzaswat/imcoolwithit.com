!function(e){if("function"==typeof define&&define.amd)define(e);else if("object"==typeof exports)module.exports=e();else{var n=window.Cookies,o=window.Cookies=e();o.noConflict=function(){return window.Cookies=n,o}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var o=arguments[e];for(var t in o)n[t]=o[t]}return n}function n(o){function t(n,i,r){var c;if(arguments.length>1){if(r=e({path:"/"},t.defaults,r),"number"==typeof r.expires){var s=new Date;s.setMilliseconds(s.getMilliseconds()+864e5*r.expires),r.expires=s}try{c=JSON.stringify(i),/^[\{\[]/.test(c)&&(i=c)}catch(e){}return i=encodeURIComponent(String(i)),i=i.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)),n=n.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),n=n.replace(/[\(\)]/g,escape),document.cookie=[n,"=",i,r.expires&&"; expires="+r.expires.toUTCString(),r.path&&"; path="+r.path,r.domain&&"; domain="+r.domain,r.secure?"; secure":""].join("")}n||(c={});for(var a=document.cookie?document.cookie.split("; "):[],p=0;p<a.length;p++){var d=a[p].split("="),f=d[0].replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent),l=d.slice(1).join("=");'"'===l.charAt(0)&&(l=l.slice(1,-1));try{if(l=o&&o(l,f)||l.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent),this.json)try{l=JSON.parse(l)}catch(e){}if(n===f){c=l;break}n||(c[f]=l)}catch(e){}}return c}return t.get=t.set=t,t.getJSON=function(){return t.apply({json:!0},[].slice.call(arguments))},t.defaults={},t.remove=function(n,o){t(n,"",e(o,{expires:-1}))},t.withConverter=n,t}return n()});