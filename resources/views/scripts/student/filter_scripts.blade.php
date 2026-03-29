<script>
$(document).ready(function() {
    
    // ============= FILTER DEPENDENT DROPDOWNS =============
    
    // Division -> District
    $("select[name='permanent_division_id']").change(function() {
        const divisionId = $(this).val();
        const districtDropdown = $("select[name='permanent_district_id']");
        const policeStationDropdown = $("select[name='permanent_police_station_id']");
        const postOfficeDropdown = $("select[name='permanent_post_office_id']");

        // Reset dependent dropdowns
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
                    data.districts.forEach(district => {
                        districtDropdown.append(
                            `<option value="${district.id}">${district.name} - ${district.bn_name}</option>`
                        );
                    });
                },
                error: function() {
                    districtDropdown.html('<option value="">All Districts</option>');
                }
            });
        } else {
            districtDropdown.html('<option value="">All Districts</option>');
        }
    });

    // District -> Police Station
    $("select[name='permanent_district_id']").change(function() {
        const districtId = $(this).val();
        const policeStationDropdown = $("select[name='permanent_police_station_id']");
        const postOfficeDropdown = $("select[name='permanent_post_office_id']");

        // Reset dependent dropdown
        postOfficeDropdown.html('<option value="">All Post Offices</option>');
        
        policeStationDropdown.html('<option value="">Loading...</option>');

        if (districtId) {
            $.ajax({
                url: "{{ route('load_police_stations') }}",
                type: "GET",
                data: { district_id: districtId },
                success: function(data) {
                    policeStationDropdown.html('<option value="">All Police Stations</option>');
                    data.police_stations.forEach(policeStation => {
                        policeStationDropdown.append(
                            `<option value="${policeStation.id}">${policeStation.name} - ${policeStation.bn_name}</option>`
                        );
                    });
                },
                error: function() {
                    policeStationDropdown.html('<option value="">All Police Stations</option>');
                }
            });
        } else {
            policeStationDropdown.html('<option value="">All Police Stations</option>');
        }
    });

    // Police Station -> Post Office
    $("select[name='permanent_police_station_id']").change(function() {
        const policeStationId = $(this).val();
        const postOfficeDropdown = $("select[name='permanent_post_office_id']");

        postOfficeDropdown.html('<option value="">Loading...</option>');

        if (policeStationId) {
            $.ajax({
                url: "{{ route('load_post_offices') }}",
                type: "GET",
                data: { police_station_id: policeStationId },
                success: function(data) {
                    postOfficeDropdown.html('<option value="">All Post Offices</option>');
                    data.post_offices.forEach(postOffice => {
                        postOfficeDropdown.append(
                            `<option value="${postOffice.id}">${postOffice.name} - ${postOffice.bn_name}</option>`
                        );
                    });
                },
                error: function() {
                    postOfficeDropdown.html('<option value="">All Post Offices</option>');
                }
            });
        } else {
            postOfficeDropdown.html('<option value="">All Post Offices</option>');
        }
    });



    // ============= AUTO-SUBMIT ON FILTER CHANGE (OPTIONAL) =============
    
    // Uncomment below if you want auto-submit on select change
    /*
    $('.form-control-sm').on('change', function() {
        // Don't auto-submit for text inputs
        if ($(this).is('select')) {
            $(this).closest('form').submit();
        }
    });
    */

    // ============= SHOW ACTIVE FILTER COUNT =============
    
    function updateFilterCount() {
        let count = 0;
        $('select.form-control-sm, input.form-control-sm').each(function() {
            if ($(this).val() && $(this).val() !== '') {
                count++;
            }
        });
        
        if (count > 0) {
            if ($('.filter-badge').length === 0) {
                $('h5 i.fa-filter').after(`<span class="badge badge-danger ml-2 filter-badge">${count}</span>`);
            } else {
                $('.filter-badge').text(count);
            }
        } else {
            $('.filter-badge').remove();
        }
    }
    
    // Update on page load
    updateFilterCount();
    
    // Update on input change
    $('select.form-control-sm, input.form-control-sm').on('change keyup', function() {
        updateFilterCount();
    });

    // ============= KEYBOARD SHORTCUTS =============
    
    // Press Ctrl+F to focus on search
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('input[name="search"]').focus();
        }
    });

    // ============= CLEAR INDIVIDUAL FILTERS =============
    
    // Add clear button to each filter input (optional)
    $('input.form-control-sm').each(function() {
        if ($(this).val()) {
            $(this).after('<button type="button" class="btn btn-sm btn-link clear-filter" style="margin-left: -30px; z-index: 10;">✕</button>');
        }
    });
    
    $(document).on('click', '.clear-filter', function() {
        $(this).prev('input').val('').trigger('change');
        $(this).remove();
    });

});
</script>

<style>
/* Filter collapse animation */
#filterCollapse {
    transition: all 0.3s ease;
}

/* Active filter highlight */
.form-control-sm:not([value=""]):not(:placeholder-shown) {
    border-left: 3px solid #007bff;
}

select.form-control-sm option:checked {
    background-color: #007bff;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-md-3 {
        margin-bottom: 10px;
    }
}

/* Loading state */
select.loading {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 50 50'%3E%3Cpath fill='%23007bff' d='M25 5A20 20 0 1 1 5 25 20 20 0 0 1 25 5m0-5A25 25 0 1 0 50 25 25 25 0 0 0 25 0z'/%3E%3Cpath fill='%23007bff' d='M25 0v5a20 20 0 0 1 0 40v5a25 25 0 0 0 0-50z'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.6s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}
</style>