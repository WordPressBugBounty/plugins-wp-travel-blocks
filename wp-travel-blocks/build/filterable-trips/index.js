(()=>{"use strict";var e,t={8641:(e,t,l)=>{const o=window.wp.blocks,n=window.wp.element,r=window.wp.i18n,a=window.wp.blockEditor,i=window.wp.components,s=window.wp.data,c=window.wp.serverSideRender;var u=l.n(c);const p=JSON.parse('{"u2":"wp-travel-blocks/filterable-trips"}');(0,o.registerBlockType)(p.u2,{edit:function(e){var t,l,o,c,p,y;const v={post_type:"itineraries",per_page:100},m=(0,s.useSelect)((e=>e("core").getEntityRecords("taxonomy","itinerary_types",v)),[]),d=(0,s.useSelect)((e=>e("core").getEntityRecords("taxonomy","travel_locations",v)),[]),b=(0,s.useSelect)((e=>e("core").getEntityRecords("taxonomy","activity",v)),[]),{setAttributes:g,attributes:w}=e,{query:_,layoutType:k,filterType:f}=w,{orderBy:T,order:h,selectedTripTypes:O,selectedTripDestinations:x,selectedTripActivities:E,numberOfItems:C}=_,F=null!==(t=m?.reduce(((e,t)=>({...e,[t.name]:t})),{}))&&void 0!==t?t:{};let L=[];O&&O.length>0&&(L=O.map((e=>e.name)));const S=null!==(l=d?.reduce(((e,t)=>({...e,[t.name]:t})),{}))&&void 0!==l?l:{};let j=[];x&&x.length>0&&(j=x.map((e=>e.name)));const I=null!==(o=b?.reduce(((e,t)=>({...e,[t.name]:t})),{}))&&void 0!==o?o:{};let q=[];return E&&E.length>0&&(q=E.map((e=>e.name))),(0,n.createElement)("div",{...(0,a.useBlockProps)()},(0,n.createElement)(n.Fragment,null,(0,n.createElement)(a.InspectorControls,null,(0,n.createElement)(i.PanelBody,{title:(0,r.__)("Filters","wp-travel-blocks")},(0,n.createElement)(i.SelectControl,{label:(0,r.__)("Select Layout","wp-travel-blocks"),value:k,options:[{label:(0,r.__)("Default Layout","wp-travel-blocks"),value:"default-layout"},{label:(0,r.__)("Layout One","wp-travel-blocks"),value:"layout-one"},{label:(0,r.__)("Layout Two","wp-travel-blocks"),value:"layout-two"},{label:(0,r.__)("Layout Three","wp-travel-blocks"),value:"layout-three"},{label:(0,r.__)("Layout Four","wp-travel-blocks"),value:"layout-four"}],onChange:e=>g({layoutType:e})}),(0,n.createElement)(i.QueryControls,{maxItems:5,orderBy:T,order:h,numberOfItems:C,onNumberOfItemsChange:e=>g({query:{..._,numberOfItems:e}})}),(0,n.createElement)(i.SelectControl,{label:(0,r.__)("Filter Type","wp-travel-blocks"),value:f,options:[{label:"Trip Type",value:"itinerary_types"},{label:"Destination",value:"travel_locations"},{label:"Activity",value:"activity"}],onChange:e=>{g({filterType:e})}}),"itinerary_types"==f&&(0,n.createElement)(i.FormTokenField,{maxLength:5,label:(0,r.__)("Trip Type"),value:null!==(c=L)&&void 0!==c?c:[],suggestions:Object.keys(F).map((e=>e)),onChange:e=>{const t=e.map((e=>{const t="string"==typeof e?F[e]:e;return{count:t.count,id:t.id,description:t.description,link:t.link,taxonomy:t.taxonomy,name:t.name,slug:t.slug}}));g({query:{..._,selectedTripTypes:t}})}}),"travel_locations"==f&&(0,n.createElement)(i.FormTokenField,{maxLength:5,label:(0,r.__)("Trip Destinations"),value:null!==(p=j)&&void 0!==p?p:[],suggestions:Object.keys(S).map((e=>e)),onChange:e=>{const t=e.map((e=>{const t="string"==typeof e?S[e]:e;return{count:t.count,id:t.id,description:t.description,link:t.link,taxonomy:t.taxonomy,name:t.name,slug:t.slug}}));g({query:{..._,selectedTripDestinations:t}})}}),"activity"==f&&(0,n.createElement)(i.FormTokenField,{maxLength:5,label:(0,r.__)("Trip Activities","wp-travel-blocks"),value:null!==(y=q)&&void 0!==y?y:[],suggestions:Object.keys(I).map((e=>e)),onChange:e=>{const t=e.map((e=>{const t="string"==typeof e?I[e]:e;return{count:t.count,id:t.id,description:t.description,link:t.link,taxonomy:t.taxonomy,name:t.name,slug:t.slug}}));g({query:{..._,selectedTripActivities:t}})}})))),(0,n.createElement)(u(),{block:"wp-travel-blocks/filterable-trips",attributes:e.attributes}))},save:function(){return null}})}},l={};function o(e){var n=l[e];if(void 0!==n)return n.exports;var r=l[e]={exports:{}};return t[e](r,r.exports,o),r.exports}o.m=t,e=[],o.O=(t,l,n,r)=>{if(!l){var a=1/0;for(u=0;u<e.length;u++){for(var[l,n,r]=e[u],i=!0,s=0;s<l.length;s++)(!1&r||a>=r)&&Object.keys(o.O).every((e=>o.O[e](l[s])))?l.splice(s--,1):(i=!1,r<a&&(a=r));if(i){e.splice(u--,1);var c=n();void 0!==c&&(t=c)}}return t}r=r||0;for(var u=e.length;u>0&&e[u-1][2]>r;u--)e[u]=e[u-1];e[u]=[l,n,r]},o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var l in t)o.o(t,l)&&!o.o(e,l)&&Object.defineProperty(e,l,{enumerable:!0,get:t[l]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={7521:0,2328:0};o.O.j=t=>0===e[t];var t=(t,l)=>{var n,r,[a,i,s]=l,c=0;if(a.some((t=>0!==e[t]))){for(n in i)o.o(i,n)&&(o.m[n]=i[n]);if(s)var u=s(o)}for(t&&t(l);c<a.length;c++)r=a[c],o.o(e,r)&&e[r]&&e[r][0](),e[r]=0;return o.O(u)},l=globalThis.webpackChunkwp_travel_blocks=globalThis.webpackChunkwp_travel_blocks||[];l.forEach(t.bind(null,0)),l.push=t.bind(null,l.push.bind(l))})();var n=o.O(void 0,[2328],(()=>o(8641)));n=o.O(n)})();