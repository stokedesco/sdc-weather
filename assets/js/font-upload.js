jQuery(function($){
    var frame;
    $('#sdc_weather_uploaded_font_button').on('click', function(e){
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select Font',
            button: { text: 'Use this font' },
            library: { type: ['font/otf','font/ttf','font/woff','font/woff2','application/x-font-ttf','application/vnd.ms-fontobject','application/font-woff','application/font-woff2'] },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#sdc_weather_uploaded_font').val(attachment.id);
            $('#sdc_weather_uploaded_font_filename').text(attachment.filename);
        });
        frame.open();
    });
});
