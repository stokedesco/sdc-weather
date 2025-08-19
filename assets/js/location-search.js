(function(){
    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('sdc-weather-find-location');
        if (!btn) {
            return;
        }
        btn.addEventListener('click', function(){
            const apiField = document.querySelector('input[name="sdc_weather_api_key"]');
            const locField = document.querySelector('input[name="sdc_weather_location"]');
            if (!apiField || !locField) {
                return;
            }
            const apiKey = apiField.value.trim();
            if (!apiKey) {
                alert('Please enter your API key first.');
                return;
            }
            const query = prompt('Enter city name to search');
            if (!query) {
                return;
            }
            fetch('https://dataservice.accuweather.com/locations/v1/cities/search?apikey=' + encodeURIComponent(apiKey) + '&q=' + encodeURIComponent(query))
                .then(function(r){ return r.json(); })
                .then(function(list){
                    const container = document.getElementById('sdc-weather-location-results');
                    if (!container) {
                        return;
                    }
                    container.innerHTML = '';
                    if (!Array.isArray(list) || !list.length) {
                        container.textContent = 'No locations found';
                        return;
                    }
                    list.slice(0,5).forEach(function(item){
                        const div = document.createElement('div');
                        div.className = 'sdc-weather-location-result';
                        div.textContent = item.LocalizedName + ', ' + item.Country.ID + ' (' + item.Key + ')';
                        div.style.cursor = 'pointer';
                        div.addEventListener('click', function(){
                            locField.value = item.Key;
                            container.innerHTML = '';
                        });
                        container.appendChild(div);
                    });
                })
                .catch(function(){
                    alert('Location search failed');
                });
        });
    });
})();
