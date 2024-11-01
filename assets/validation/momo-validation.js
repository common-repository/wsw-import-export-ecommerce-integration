(function($){
    var MomoFormValidator = {
        init: function(selector, options) {
            var $form = $(selector);
            var settings = $.extend({
                validateOnBlur: true,
                validateOnSubmit: true
            }, options);

            // Add an asterisk (*) to required field labels
            $form.find('.momo-required').each(function() {
                var $label = $form.find('label[for="' + $(this).attr('name') + '"]');
                if ($label.length && !$label.hasClass('momo-asterisk-added')) {
                    $label.append('<span class="momo-required-asterisk">*</span>');
                    $label.addClass('momo-asterisk-added'); // Avoid adding the asterisk multiple times
                }
            });

            if (settings.validateOnBlur) {
                // Validate fields on blur (when they lose focus)
                $form.find('.momo-required').on('blur', function() {
                    MomoFormValidator.validateField($(this));
                });

                // Optional: Clear error on focus (when the field gains focus)
                $form.find('.momo-required').on('focus', function() {
                    MomoFormValidator.clearError($(this));
                });
            }

            if (settings.validateOnSubmit) {
                // Validate all fields on form submit
                $form.on('submit', function(e) {
                    var isValid = true;
                    $form.find('.momo-required').each(function() {
                        if (!MomoFormValidator.validateField($(this))) {
                            isValid = false;
                        }
                    });

                    if (!isValid) {
                        e.preventDefault(); // Prevent form submission if validation fails
                    }
                });
            }
        },
        validateField: function($element) {
            if ($element.val().trim() === '') {
                var errorMessage = $element.data('error-message') || 'This field is required.';
                MomoFormValidator.showError($element, errorMessage);
                return false;
            } else {
                MomoFormValidator.clearError($element);
                return true;
            }
        },
        showError: function($element, message) {
            var $error = $('<div class="momo-error-message"></div>').text(message);
            $element.addClass('momo-error').after($error);
        },
        clearError: function($element) {
            $element.removeClass('momo-error').next('.momo-error-message').remove();
        }
    };
    
    // Attach to global scope for reuse
    window.MomoFormValidator = MomoFormValidator;
})(jQuery);
