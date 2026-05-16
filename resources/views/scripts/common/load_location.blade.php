<script>


 // ============= COMMON AJAX FUNCTIONS =============

        /**
         * Load districts based on division
         */
        function loadDistricts(divisionId, targetDropdown, callback = null) {
            targetDropdown.html('<option value="">Loading...</option>');

            if (divisionId) {
                $.ajax({
                    url: "{{ route('load_districts') }}",
                    type: "GET",
                    data: {
                        division_id: divisionId
                    },
                    success: function(data) {
                        targetDropdown.html('<option value="">District</option>');
                        data.districts.forEach(district => {
                            targetDropdown.append(
                                `<option value="${district.id}">${district.name} - ${district.bn_name}</option>`
                            );
                        });
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                        if (callback) callback();
                    },
                    error: function() {
                        targetDropdown.html('<option value="">District</option>');
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                    }
                });
            } else {
                targetDropdown.html('<option value="">District</option>');
            }
        }

        /**
         * Load police stations based on district
         */
        function loadPoliceStations(districtId, targetDropdown, callback = null) {
            targetDropdown.html('<option value="">Loading...</option>');

            if (districtId) {
                $.ajax({
                    url: "{{ route('load_police_stations') }}",
                    type: "GET",
                    data: {
                        district_id: districtId
                    },
                    success: function(data) {
                        targetDropdown.html('<option value="">Police Station</option>');
                        data.police_stations.forEach(policeStation => {
                            targetDropdown.append(
                                `<option value="${policeStation.id}">${policeStation.name} - ${policeStation.bn_name}</option>`
                            );
                        });
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                        if (callback) callback();
                    },
                    error: function() {
                        targetDropdown.html('<option value="">Police Station</option>');
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                    }
                });
            } else {
                targetDropdown.html('<option value="">Police Station</option>');
            }
        }

        /**
         * Load post offices based on police station
         */
        function loadPostOffices(policeStationId, targetDropdown, callback = null) {
            targetDropdown.html('<option value="">Loading...</option>');

            if (policeStationId) {
                $.ajax({
                    url: "{{ route('load_post_offices') }}",
                    type: "GET",
                    data: {
                        police_station_id: policeStationId
                    },
                    success: function(data) {
                        targetDropdown.html('<option value="">Post Office</option>');
                        data.post_offices.forEach(postOffice => {
                            targetDropdown.append(
                                `<option value="${postOffice.id}">${postOffice.name} - ${postOffice.bn_name}</option>`
                            );
                        });
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                        if (callback) callback();
                    },
                    error: function() {
                        targetDropdown.html('<option value="">Post Office</option>');
                        if (window.refreshSelect2) refreshSelect2(targetDropdown);
                    }
                });
            } else {
                postOfficeDropdown.html('<option value="">Post Office</option>');
            }
        }
        /**
         * Reset dependent dropdowns
         */
        function resetDropdowns(...dropdowns) {
            dropdowns.forEach(dropdown => {
                const name = dropdown.attr('name');
                let placeholder = 'Select';

                if (name.includes('district')) placeholder = 'District';
                else if (name.includes('police_station')) placeholder = 'Police Station';
                else if (name.includes('post_office')) placeholder = 'Post Office';

                dropdown.html(`<option value="">${placeholder}</option>`);
            });
        }
       // ============= PRESENT ADDRESS DEPENDENT DROPDOWNS =============

        $("select[name='present_division_id']").change(function() {
            const divisionId = $(this).val();
            const districtDropdown = $("select[name='present_district_id']");
            const policeStationDropdown = $("select[name='present_police_station_id']");
            const postOfficeDropdown = $("select[name='present_post_office_id']");

            resetDropdowns(policeStationDropdown, postOfficeDropdown);
            loadDistricts(divisionId, districtDropdown);
        });

        $("select[name='present_district_id']").change(function() {
            const districtId = $(this).val();
            const policeStationDropdown = $("select[name='present_police_station_id']");
            const postOfficeDropdown = $("select[name='present_post_office_id']");

            resetDropdowns(postOfficeDropdown);
            loadPoliceStations(districtId, policeStationDropdown);
        });

        $("select[name='present_police_station_id']").change(function() {
            const policeStationId = $(this).val();
            const postOfficeDropdown = $("select[name='present_post_office_id']");

            loadPostOffices(policeStationId, postOfficeDropdown);
        });

        // ============= PERMANENT ADDRESS DEPENDENT DROPDOWNS =============

        $("select[name='permanent_division_id']").change(function() {
            const divisionId = $(this).val();
            const districtDropdown = $("select[name='permanent_district_id']");
            const policeStationDropdown = $("select[name='permanent_police_station_id']");
            const postOfficeDropdown = $("select[name='permanent_post_office_id']");

            resetDropdowns(policeStationDropdown, postOfficeDropdown);
            loadDistricts(divisionId, districtDropdown);
        });

        $("select[name='permanent_district_id']").change(function() {
            const districtId = $(this).val();
            const policeStationDropdown = $("select[name='permanent_police_station_id']");
            const postOfficeDropdown = $("select[name='permanent_post_office_id']");

            resetDropdowns(postOfficeDropdown);
            loadPoliceStations(districtId, policeStationDropdown);
        });

        $("select[name='permanent_police_station_id']").change(function() {
            const policeStationId = $(this).val();
            const postOfficeDropdown = $("select[name='permanent_post_office_id']");

            loadPostOffices(policeStationId, postOfficeDropdown);
        });

        // ============= SAME ADDRESS CHECKBOX FUNCTIONALITY =============

        $("#same_address").change(function() {
            const isChecked = $(this).is(':checked');

            if (isChecked) {
                // Get present address values
                const presentDivision = $("select[name='present_division_id']").val();
                const presentDistrict = $("select[name='present_district_id']").val();
                const presentPoliceStation = $("select[name='present_police_station_id']").val();
                const presentPostOffice = $("select[name='present_post_office_id']").val();
                const presentAddress = $("textarea[name='present_address']").val();

                // Set permanent division and trigger cascade
                $("select[name='permanent_division_id']").val(presentDivision);

                // Load districts, then set district value
                loadDistricts(presentDivision, $("select[name='permanent_district_id']"), function() {
                    $("select[name='permanent_district_id']").val(presentDistrict);

                    // Load police stations, then set police station value
                    loadPoliceStations(presentDistrict, $(
                        "select[name='permanent_police_station_id']"), function() {
                        $("select[name='permanent_police_station_id']").val(
                            presentPoliceStation);

                        // Load post offices, then set post office value
                        loadPostOffices(presentPoliceStation, $(
                                "select[name='permanent_post_office_id']"),
                            function() {
                                $("select[name='permanent_post_office_id']").val(
                                    presentPostOffice);
                            });
                    });
                });

                // Copy textarea
                $("textarea[name='permanent_address']").val(presentAddress);

                // Disable permanent address fields
                $("#permanent_address_section select, #permanent_address_section textarea")
                    .prop('disabled', true)
                    .addClass('bg-gray-100 cursor-not-allowed');

                toastr.info('Permanent address copied from present address');
            } else {
                // Enable permanent address fields
                $("#permanent_address_section select, #permanent_address_section textarea")
                    .prop('disabled', false)
                    .removeClass('bg-gray-100 cursor-not-allowed');

                toastr.warning('Permanent address fields enabled');
            }
        });

        // Real-time sync when "Same Address" is checked
        $("select[name^='present_'], textarea[name='present_address']").on('change keyup', function() {
            if ($("#same_address").is(':checked')) {
                const fieldName = $(this).attr('name');
                const permanentFieldName = fieldName.replace('present_', 'permanent_');
                const permanentField = $(`[name='${permanentFieldName}']`);

                if ($(this).is('select')) {
                    const selectedValue = $(this).val();

                    // Handle cascading updates
                    if (fieldName === 'present_division_id') {
                        permanentField.val(selectedValue);
                        loadDistricts(selectedValue, $("select[name='permanent_district_id']"));
                        resetDropdowns($("select[name='permanent_police_station_id']"), $(
                            "select[name='permanent_post_office_id']"));
                    } else if (fieldName === 'present_district_id') {
                        permanentField.val(selectedValue);
                        loadPoliceStations(selectedValue, $(
                            "select[name='permanent_police_station_id']"));
                        resetDropdowns($("select[name='permanent_post_office_id']"));
                    } else if (fieldName === 'present_police_station_id') {
                        permanentField.val(selectedValue);
                        loadPostOffices(selectedValue, $("select[name='permanent_post_office_id']"));
                    } else if (fieldName === 'present_post_office_id') {
                        permanentField.val(selectedValue);
                    }
                } else if ($(this).is('textarea')) {
                    permanentField.val($(this).val());
                }
            }
        });


</script>