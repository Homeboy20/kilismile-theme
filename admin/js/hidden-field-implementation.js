/**
 * Hidden Field Implementation for Gateway Toggles
 * 
 * This script adds explicit hidden fields to forms to ensure that
 * unchecked toggles are properly submitted with value "0".
 */
(function($) {
    'use strict';
    
    // Run when the document is ready
    $(document).ready(function() {
        console.log('KiliSmile Hidden Field Implementation: Active');
        
        // Create hidden fields for all toggle checkboxes
        function addHiddenFieldsToToggles() {
            // Process all toggle checkboxes
            $('.method-toggle').each(function() {
                const toggleName = $(this).attr('name');
                const toggleForm = $(this).closest('form');
                
                // Remove any existing hidden field to avoid duplicates
                toggleForm.find('input[type="hidden"][name="' + toggleName + '"]').remove();
                
                // Create a hidden field with the same name as the toggle
                const hiddenField = $('<input>', {
                    type: 'hidden',
                    name: toggleName,
                    value: '0'
                });
                
                // Add the hidden field to the form
                toggleForm.append(hiddenField);
                
                // When the checkbox changes, update the hidden field
                $(this).on('change', function() {
                    // We don't need to update the hidden field value
                    // because the checkbox's value (1) will take precedence
                    // when the form is submitted if the checkbox is checked
                    console.log(toggleName + ' changed to: ' + ($(this).is(':checked') ? '1' : '0'));
                });
                
                console.log('Added hidden field for ' + toggleName + ' (initial state: ' + ($(this).is(':checked') ? '1' : '0') + ')');
            });
        }
        
        // Run the function when the page loads
        addHiddenFieldsToToggles();
        
        // Also run when any form changes to ensure all toggles have hidden fields
        $('.settings-form').on('change', function() {
            addHiddenFieldsToToggles();
        });
    });
    
})(jQuery);


