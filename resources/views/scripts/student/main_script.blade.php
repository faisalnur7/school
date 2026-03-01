<script>
    $(document).ready(function() {

        const presetKey = 'student_form_presets';
        const presetFields = [
            'academic_session_id',
            'school_class_id',
            'section_id',
            'group_id',
            'gender',
            'religion',
            'blood_group',
            'present_division_id',
            'present_district_id',
            'present_police_station_id',
            'present_post_office_id',
            'present_address'
        ];

        // ============= PRESETS =============

        function loadPreset() {
            const preset = JSON.parse(localStorage.getItem(presetKey));
            if (!preset) return;

            presetFields.forEach(name => {
                const $field = $(`[name="${name}"]`);
                if ($field.length && !$field.val() && preset[name]) {
                    $field.val(preset[name]).trigger('change');
                }
            });
        }

        function savePreset() {
            let preset = {};
            presetFields.forEach(name => {
                const $field = $(`[name="${name}"]`);
                if ($field.length && $field.val()) preset[name] = $field.val();
            });

            if ($.isEmptyObject(preset)) {
                toastr.warning('Nothing selected to save');
                return;
            }

            localStorage.setItem(presetKey, JSON.stringify(preset));
            toastr.success('Preset saved successfully');
        }

        function clearPreset() {
            localStorage.removeItem(presetKey);
            toastr.info('Preset cleared');
        }

        $('#savePresetBtn').on('click', savePreset);
        $('#clearPresetBtn').on('click', clearPreset);

        // Load preset only on create form
        @if (!isset($student))
            loadPreset();
        @endif

 

        // ============= GUARDIAN INFO TOGGLE =============

        function toggleGuardianInfo() {
            const val = $('input[name="guardian_type"]:checked').val();

            if (String(val) === '3') {
                $('#guardianInfo').removeClass('hidden').show();
            } else {
                $('#guardianInfo').addClass('hidden').hide();
            }
        }

        // On change
        $('input[name="guardian_type"]').on('change', toggleGuardianInfo);

        // Force toggle after load
        toggleGuardianInfo();

        // ============= EDIT FORM SAME ADDRESS CHECK =============

        @if (isset($student))
            @if ($student->present_address === $student->permanent_address)
                $('#same_address').prop('checked', true).trigger('change');
            @endif
        @endif

    });
</script>
