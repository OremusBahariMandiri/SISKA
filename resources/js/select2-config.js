// resources/js/select2-config.js
import $ from 'jquery';
import 'select2';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

// Configure Select2 globally
const initializeSelect2 = () => {
    console.log('Initializing Select2 components');

    // Global configuration for all Select2 instances
    $.fn.select2.defaults.set('theme', 'bootstrap-5');
    $.fn.select2.defaults.set('width', '100%');

    // Initialize Select2 for employee dropdown
    if ($('#IdKodeA04').length) {
        try {
            $('#IdKodeA04').select2({
                placeholder: 'Pilih Karyawan',
                allowClear: true,
                dropdownParent: $('#IdKodeA04').parent(),
                language: {
                    searching: function() {
                        return "Mencari...";
                    },
                    noResults: function() {
                        return "Tidak ada hasil";
                    },
                    inputTooShort: function(args) {
                        var remainingChars = args.minimum - args.input.length;
                        return "Masukkan " + remainingChars + " karakter lagi";
                    }
                }
            });

            // Add event handler for form validation
            $('#IdKodeA04').on('change', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });

            console.log('Select2 initialized successfully for #IdKodeA04');
        } catch (e) {
            console.error('Error initializing Select2:', e);
        }
    } else {
        console.log('IdKodeA04 element not found in the DOM');
    }

    // Initialize any other Select2 elements if needed
    $('.select2-element').each(function() {
        try {
            $(this).select2({
                theme: 'bootstrap-5',
                placeholder: $(this).data('placeholder') || 'Pilih opsi'
            });
        } catch (e) {
            console.error('Error initializing other Select2 elements:', e);
        }
    });
};

export default initializeSelect2;