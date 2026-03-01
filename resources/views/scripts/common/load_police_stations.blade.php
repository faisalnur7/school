<script>
$("#district_id").change(function() {
    let districtId = $(this).val();
    let policeStationDropdown = $('#police_station_id');

    policeStationDropdown.html('<option value="">Loading...</option>');

    if (districtId) {
        $.ajax({
            url: "{{ route('load_police_stations') }}",
            type: "GET",
            data: {
                district_id: districtId
            },
            success: function(data) {
                policeStationDropdown.html('<option value="">Select Police Station</option>');
                data.police_stations.forEach(policeStation => {
                    policeStationDropdown.append(
                        `<option value="${policeStation.id}">${policeStation.name}</option>`
                    );
                });
            },
        });
    }
});
</script>