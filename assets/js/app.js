/* FreshCart Store - small front-end helpers */
document.addEventListener('DOMContentLoaded', function () {
  // Quantity input: never below 1
  document.querySelectorAll('input[name="qty"]').forEach(function (input) {
    input.addEventListener('change', function () {
      var min = parseInt(input.min || '1', 10);
      var max = parseInt(input.max || '99', 10);
      var val = parseInt(input.value, 10);
      if (isNaN(val) || val < min) input.value = min;
      if (val > max) input.value = max;
    });
  });
});
