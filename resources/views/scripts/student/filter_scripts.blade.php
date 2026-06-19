<script>
$(document).ready(function() {
    const filterFields = $(
        'input[name="search"], input[name="phone"], select[name="academic_session_id"], select[name="school_class_id"], ' +
        'select[name="section_id"], select[name="group_id"], select[name="gender"], select[name="status"], ' +
        'select[name="present_division_id"], select[name="present_district_id"], ' +
        'select[name="present_police_station_id"], select[name="present_post_office_id"]'
    );

    function setFilterCount() {
        let count = 0;

        filterFields.each(function() {
            const value = $(this).val();
            if (Array.isArray(value) ? value.length : value !== null && value !== '') {
                count++;
            }
        });

        $('[data-filter-count]').text(count);
    }

    function refreshFilterPanelState() {
        const panel = $('#filterCollapse');
        const activeCount = Number($('[data-filter-count]').text() || 0);

        if (activeCount > 0) {
            panel.removeClass('hidden');
        }
    }

    $("select[name='present_division_id']").change(function() {
        const divisionId = $(this).val();
        const districtDropdown = $("select[name='present_district_id']");
        const policeStationDropdown = $("select[name='present_police_station_id']");
        const postOfficeDropdown = $("select[name='present_post_office_id']");

        policeStationDropdown.html('<option value="">All Police Stations</option>');
        postOfficeDropdown.html('<option value="">All Post Offices</option>');
        districtDropdown.html('<option value="">Loading...</option>');

        if (divisionId) {
            $.ajax({
                url: "{{ route('load_districts') }}",
                type: "GET",
                data: { division_id: divisionId },
                success: function(data) {
                    districtDropdown.html('<option value="">All Districts</option>');
                    data.districts.forEach(function(district) {
                        districtDropdown.append(`<option value="${district.id}">${district.name} - ${district.bn_name}</option>`);
                    });
                    if (window.refreshSelect2) refreshSelect2(districtDropdown);
                },
                error: function() {
                    districtDropdown.html('<option value="">All Districts</option>');
                    if (window.refreshSelect2) refreshSelect2(districtDropdown);
                }
            });
        } else {
            districtDropdown.html('<option value="">All Districts</option>');
            if (window.refreshSelect2) refreshSelect2(districtDropdown);
        }
    });

    $("select[name='present_district_id']").change(function() {
        const districtId = $(this).val();
        const policeStationDropdown = $("select[name='present_police_station_id']");
        const postOfficeDropdown = $("select[name='present_post_office_id']");

        postOfficeDropdown.html('<option value="">All Post Offices</option>');
        policeStationDropdown.html('<option value="">Loading...</option>');

        if (districtId) {
            $.ajax({
                url: "{{ route('load_police_stations') }}",
                type: "GET",
                data: { district_id: districtId },
                success: function(data) {
                    policeStationDropdown.html('<option value="">All Police Stations</option>');
                    data.police_stations.forEach(function(policeStation) {
                        policeStationDropdown.append(`<option value="${policeStation.id}">${policeStation.name} - ${policeStation.bn_name}</option>`);
                    });
                    if (window.refreshSelect2) refreshSelect2(policeStationDropdown);
                },
                error: function() {
                    policeStationDropdown.html('<option value="">All Police Stations</option>');
                    if (window.refreshSelect2) refreshSelect2(policeStationDropdown);
                }
            });
        } else {
            policeStationDropdown.html('<option value="">All Police Stations</option>');
            if (window.refreshSelect2) refreshSelect2(policeStationDropdown);
        }
    });

    $("select[name='present_police_station_id']").change(function() {
        const policeStationId = $(this).val();
        const postOfficeDropdown = $("select[name='present_post_office_id']");

        postOfficeDropdown.html('<option value="">Loading...</option>');

        if (policeStationId) {
            $.ajax({
                url: "{{ route('load_post_offices') }}",
                type: "GET",
                data: { police_station_id: policeStationId },
                success: function(data) {
                    postOfficeDropdown.html('<option value="">All Post Offices</option>');
                    data.post_offices.forEach(function(postOffice) {
                        postOfficeDropdown.append(`<option value="${postOffice.id}">${postOffice.name} - ${postOffice.bn_name}</option>`);
                    });
                    if (window.refreshSelect2) refreshSelect2(postOfficeDropdown);
                },
                error: function() {
                    postOfficeDropdown.html('<option value="">All Post Offices</option>');
                    if (window.refreshSelect2) refreshSelect2(postOfficeDropdown);
                }
            });
        } else {
            postOfficeDropdown.html('<option value="">All Post Offices</option>');
            if (window.refreshSelect2) refreshSelect2(postOfficeDropdown);
        }
    });

    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('input[name="search"]').focus();
        }
    });

    filterFields.on('change keyup', setFilterCount);

    setFilterCount();
    refreshFilterPanelState();
});
</script>
