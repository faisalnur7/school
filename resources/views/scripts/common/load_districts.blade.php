<script>
$("#division_id").change(function() {
    let divisionId = $(this).val();
    let districtDropdown = $('#district_id');

    districtDropdown.html('<option value="">Loading...</option>');

    if (divisionId) {
        $.ajax({
            url: "{{ route('load_districts') }}",
            type: "GET",
            data: {
                division_id: divisionId
            },
            success: function(data) {
                districtDropdown.html('<option value="">Select District</option>');
                data.districts.forEach(district => {
                    districtDropdown.append(
                        `<option value="${district.id}">${district.name}</option>`
                    );
                });
            },
        });
    }
});
</script>