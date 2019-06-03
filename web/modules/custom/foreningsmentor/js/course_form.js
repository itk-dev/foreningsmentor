(function ($, Drupal) {
  Drupal.behaviors.courseForm = {
    attach: function (context, settings) {
      var courseType = $('#edit-field-activity-type');
      var mentorEnd = $('#edit-field-mentors-end-status');
      var coordinatorEnd = $('#edit-field-coordinators-end-status');

      function imposeLimitations() {
        var courseTypeText = courseType.find('option:selected').text();

        mentorEnd.find('option:not(:selected)').each(function () {
          if ($(this).val() !== '_none' &&
            $(this).text().indexOf(courseTypeText) !== 0) {
            $(this).hide();
          }
          else {
            $(this).show();
          }
        });

        coordinatorEnd.find('option:not(:selected)').each(function () {
          if ($(this).val() !== '_none' &&
            $(this).text().indexOf(courseTypeText) !== 0) {
            $(this).hide();
          }
          else {
            $(this).show();
          }
        });
      }

      courseType.on('change', imposeLimitations);
      mentorEnd.on('change', imposeLimitations);

      imposeLimitations();
    }
  };
})(jQuery, Drupal);
