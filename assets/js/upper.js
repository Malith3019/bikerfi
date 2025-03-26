$(function() {
  // Convert input text to uppercase
  $('input').keyup(function() {
      this.value = this.value.toUpperCase();
  });

  // Convert textarea text to uppercase
  $('textarea').keyup(function() {
      this.value = this.value.toUpperCase();
  });

  // Convert selected option text to uppercase (on change)
  $('select').change(function() {
      this.value = this.value.toUpperCase();
  });
});
