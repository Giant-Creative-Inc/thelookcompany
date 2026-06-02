/*
Compiled file. Do not edit directly.
Edit source in /assets/js/src/
*/

!function(e){e(document).on("gform_post_render",function(t,n){var a;(a=e("#gform_"+n)).find("input, textarea, select").off(".material").on("focusin.material",function(){e(this).parent().siblings("label").addClass("focused")}).on("focusout.material",function(){this.value||e(this).parent().siblings("label").removeClass("focused")}),a.find("input, textarea, select").each(function(){this.value&&e(this).parent().siblings("label").addClass("focused")})})}(jQuery);