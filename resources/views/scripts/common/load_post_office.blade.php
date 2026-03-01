
<script>
$("#police_station_id").change(function() {
    let policeStationId = $(this).val();
    let postOfficeDropdown = $('#post_office_id');

    postOfficeDropdown.html('<option value="">Loading...</option>');

    if (policeStationId) {
        $.ajax({
            url: "{{ route('load_post_offices') }}",
            type: "GET",
            data: {
                police_station_id: policeStationId
            },
            success: function(data) {
                postOfficeDropdown.html('<option value="">Select Post Office</option>');
                data.post_offices.forEach(postOffice => {
                    postOfficeDropdown.append(
                        `<option value="${postOffice.id}">${postOffice.name}</option>`
                    );
                });
            },
        });
    }
});
</script>