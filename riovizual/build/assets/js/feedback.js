(()=>{var e={415:()=>{document.addEventListener("DOMContentLoaded",(function(){var t=document.querySelector("[id^='deactivate-riovizual']"),n=document.getElementById("rv-feedback-form");t&&t.addEventListener("click",(function(e){e.preventDefault(),document.getElementById("rv-feedback-modal").style.display="block";var t=0,n=setInterval((function(){t+=.05,document.getElementById("rv-feedback-modal").style.opacity=t,t>=1&&clearInterval(n)}),10)})),n&&n.addEventListener("submit",(function(e){e.preventDefault();const t=e.submitter;"rv-submit-feedback-btn"===t.id&&a(),"rv-cancel-feedback-btn"===t.id&&d()}));const d=()=>{document.getElementById("rv-submit-feedback-btn").textContent="Skip & Deactive",document.getElementById("other_feedback_form").classList.add("rv-d-none"),document.getElementById("better_plugin_form").classList.add("rv-d-none"),document.getElementById("rv-submit-feedback-btn").classList.remove("disabled"),n.reset();let e=1;var t=setInterval((function(){e-=.05,document.getElementById("rv-feedback-modal").style.opacity=e,e<.1&&(document.getElementById("rv-feedback-modal").style.display="none",document.getElementById("rv-feedback-modal").style.removeProperty("opacity"),clearInterval(t))}),10)},a=()=>{document.querySelector(".rv-feedback-popup").classList.add("opacity-1");let t="";const n=document.querySelector('input[name="rv-feedback-op"]:checked'),d=document.getElementById("rv-other-feedback"),a=document.getElementById("better_plugin_input"),o=document.getElementById("rv-current-version").value,r=(new Date).toLocaleString();n&&(t=n.value,"Other"===t&&d&&(t=d.value),"I found a better plugin"===t&&a&&(t=a.value)),fetch("https://survey.riovizual.com/wp-json/riovizual/v2/rio_feedback_survey/",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({feedback_entry:r,feedback_data:t,current_version:o})}).then((e=>e.json())).catch((t=>e())).finally((()=>{e()}))}}));const e=()=>{const e=new FormData;e.append("action","deactivate_plugin"),e.append("plugin","riovizual/riovizual.php"),fetch("/wp-admin/admin-ajax.php",{method:"POST",body:e,credentials:"same-origin"}).then((e=>e.json())).then((e=>{e.success&&location.reload()})).catch((e=>{}))}},272:()=>{const e=document.querySelectorAll('.rv-feedback-options input[type="radio"]'),t=document.getElementById("other_feedback_form"),n=document.getElementById("rv-other-feedback"),d=document.getElementById("better_plugin_input"),a=document.getElementById("better_plugin_form"),o=document.getElementById("rv-submit-feedback-btn");e.forEach((e=>{e.addEventListener("change",(function(){this.checked&&(o.textContent="Submit & Deactivate","other"===this.id?(t.classList.remove("rv-d-none"),a.classList.add("rv-d-none"),n&&""==n.value&&o.classList.add("disabled")):"found_better_plugin"===this.id?(a.classList.remove("rv-d-none"),t.classList.add("rv-d-none"),d&&""==d.value&&o.classList.add("disabled")):(t.classList.add("rv-d-none"),a.classList.add("rv-d-none"),o.classList.remove("disabled")))}))})),n&&n.addEventListener("keyup",(function(){""==n.value?o.classList.add("disabled"):o.classList.remove("disabled")})),d&&d.addEventListener("keyup",(function(){""==d.value?o.classList.add("disabled"):o.classList.remove("disabled")}))}},t={};function n(d){var a=t[d];if(void 0!==a)return a.exports;var o=t[d]={exports:{}};return e[d](o,o.exports,n),o.exports}n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var d in t)n.o(t,d)&&!n.o(e,d)&&Object.defineProperty(e,d,{enumerable:!0,get:t[d]})},n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";n(415),n(272)})()})();