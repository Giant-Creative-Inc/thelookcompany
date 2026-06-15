/*
Compiled file. Do not edit directly.
Edit source in /assets/js/src/
*/
(function ($) {

  function bindMaterialLabels(form) {

    form.find('input, textarea, select')
      .off('.material')
      .on('focusin.material', function () {
        $(this).parent().siblings('label').addClass('focused');
      })
      .on('focusout.material', function () {
        if (!this.value) {
          $(this).parent().siblings('label').removeClass('focused');
        }
      });

    form.find('input, textarea, select').each(function () {
      if (this.value) {
        $(this).parent().siblings('label').addClass('focused');
      }
    });
  }

  $(document).on('gform_post_render', function (event, formId) {
    bindMaterialLabels($('#gform_' + formId));
  });

})(jQuery);