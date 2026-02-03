/*
Assumes a relationship between terms in activity type and end statuses

Example:

If #edit-field-activity-type has these options:
 - "Swimming"
 - "Football"
And #edit-field-mentors-end-status has these options:
 - "Swimming - Completed"
 - "Swimming - In Progress"
 - "Football - Completed"
 - "Football - Dropped Out"

 When you select "Swimming" in activity type, only options starting with
 "Swimming" will show in the other dropdowns ("Swimming - Completed", "Swimming - In Progress").
 */

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
