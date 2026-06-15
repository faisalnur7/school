<script>
$(document).ready(function() {

    function loadSectionsAndGroups(classId, selectedSection = null) {

        $('#sectionSelect').html('<option value="">Loading...</option>');

        if(classId) {
            $.ajax({
                url: "{{ route('load_section_groups') }}",
                type: "GET",
                data: { school_class_id: classId },

                success: function(response) {

                    /* Sections */
                    let sectionOptions = '<option value="">All Sections</option>';

                    $.each(response.sections, function(index, section) {
                        let selected = (selectedSection == section.id) ? 'selected' : '';
                        sectionOptions += `<option value="${section.id}" ${selected}>
                                                ${section.name_en}
                                           </option>`;
                    });

                    $('#sectionSelect').html(sectionOptions);
                    if (window.refreshSelect2) refreshSelect2($('#sectionSelect'));

                    // Trigger section change to reload groups and update roll/cid
                    $('#sectionSelect').trigger('change');
                }
            });
        } else {
            $('#sectionSelect').html('<option value="">All Sections</option>');
            if (window.refreshSelect2) refreshSelect2($('#sectionSelect'));
        }
    }


    /* On class change */
    $(document).on('change', '#classSelect', function() {
        let classId = $(this).val();
        loadSectionsAndGroups(classId);
    });


    /* Load from URL parameters */
    let classId        = "{{ request('school_class_id') }}";
    let sectionId      = "{{ request('section_id') }}";

    if(classId) {
        loadSectionsAndGroups(classId, sectionId);
    }

});
</script>
