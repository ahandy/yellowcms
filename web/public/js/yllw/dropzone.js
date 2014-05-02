jQuery(".dropzone").dropzone({
  url: "<?php echo plugins_url() . '/upload' ?>",
  dictDefaultMessage: "Datei bitte hier ablegen!",
  success: function() {
    jQuery('.success-mark').show();
    jQuery('.error-mark').hide();
  },
  error: function() {
    jQuery('.success-mark').hide();
    jQuery('.error-mark').show();
  }
});