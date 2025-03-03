<div class="col">
    <a data-bs-toggle="tooltip" data-bs-placement="bottom" title="Current Location" class="btn icon btn-primary mx-1" id="current-position" onclick="currentPosition()">
        <span class="material-symbols-outlined">my_location</span>
    </a>
    <a data-bs-toggle="tooltip" data-bs-placement="bottom" title="Set Manual Location" class="btn icon btn-primary mx-1" id="manual-position" onclick="manualPosition()">
        <span class="material-symbols-outlined">pin_drop</span>
    </a>
    <a data-bs-toggle="tooltip" data-bs-placement="bottom" title="Toggle Legend" class="btn icon btn-primary mx-1" id="legend-map" onclick="viewLegend();">
        <span class="material-symbols-outlined">visibility</span>
    </a>

</div>
<div class="col">
    <div class="input-group">
        <label class="input-group-text" for="area_geom">Area Level</label>
        <select class="form-select" id="area_geom" onchange="changeAreaGeom()">
            <option value="country">Country</option>
            <option value="province" selected>Province</option>
            <option value="city">City/Regency</option>
            <option value="subdistric">Sub District</option>
            <option value="village">Tourism Village</option>
        </select>
    </div>
</div>
<div class="col-md-auto" id="weather-info">
      <span style="margin-right: 10px;">Saribu Rumah Gadang, ID</span>
      <img src="http://openweathermap.org/img/wn/04d.png" alt="Weather Icon" style="margin-right: 10px; filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.5));">
      <span style="margin-right: 10px;" id="weatherTemp"></span>
      <span style="margin-right: 10px;" id="weatherCloud"></span>
      <span style="margin-right: 10px;" id="weatherHumidity"></span>
      <span style="margin-right: 10px;" id="weatherWind"></span>

    </div>
<script>
    function changeAreaGeom() {
        let areaLevel = $("#area_geom").val()
        if (areaLevel == 'country') {
            addAreaPolygon(indonesiaGeom, '#000000')
        } else if (areaLevel == 'province') {
            addAreaPolygon(sumbarGeom, '#000000')
        } else if (areaLevel == 'city') {
            addAreaPolygon(solokSelatanGeom, '#000000')
        } else if (areaLevel == 'subdistric') {
            addAreaPolygon(kecamatanSungaiPagu, '#000000')
        } else {
            $.ajax({
                url: baseUrl + '/api/village',
                type: 'POST',
                data: {
                    village: '1'
                },
                dataType: 'json',
                success: function (response) {
                    const data = response.data;
                    addAreaPolygon(data, '#000000')
                }
            });
           
        }
    }
</script>