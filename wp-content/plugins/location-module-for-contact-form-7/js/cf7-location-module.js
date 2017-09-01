/**
 * Location Module (LITE) for Contact Form 7
 *
 * @ver 1.0.11
 */

/**
 * Reload funcion for form style
 */
var ui_reload = function (){
    var width = jQuery('.wpcf7').outerWidth();
    var buttonwidth = jQuery('#cf7-geocode-buttons').outerWidth();
    buttonwidth = buttonwidth + jQuery('#geocode-reset').outerWidth();
    var height = jQuery('#cf7-geocode-address').outerHeight();
    jQuery('#cf7-geocode-address').css( "height", height+'px' );
    var height = jQuery('#cf7-geocode-address').outerHeight();
    jQuery('.cf7-loc-button').css( "line-height", height+'px' );
    width = width - buttonwidth -5;
    jQuery('#cf7-geocode-address').css( "width", width );
};
var map;

/**
 * On Load Functions
 */
jQuery(window).load(function(){
       ui_reload();
    }
);

/**
 * On Ready Functions
 */
jQuery(document).ready(function(e){

    if (jQuery('#cf7-location-map').length) {
        // Get Option Value
        var def_latitude = CF7LM.deflat;
        var def_longitude = CF7LM.deflng;
        var def_zoom = parseInt(CF7LM.defzoom);
        var def_err_msg = CF7LM.def_err_msg;
        var def_maps_view = CF7LM.mapsView;

        /**
         * Map Init
         */
        map = new GMaps({
            div: '#cf7-location-map',
            scrollwheel: false,
            mapTypeControl: false,
            streetViewControl: false,
            lat: def_latitude,
            lng: def_longitude,
            zoom: def_zoom,
            mapType: def_maps_view
        });

        /**
         * Geocode the input on "SET" button press
         */
        jQuery( '#geocode-link' ).click(function() {
            // Inserisco un ritardo per evitare che venga eseguita una chiamata ad ogni keypress
            var geocodeinput = GMaps.geocode({
                address: jQuery('#cf7-geocode-address').val(),
                callback: function (results, status) {
                    if (status == 'OK') {
                        var latlng = results[0].geometry.location;
                        // Centro la mappa dopo il geocode
                        map.setCenter(latlng.lat(), latlng.lng());
                        // Scrivo le coordinate nei campi nascosti
                        jQuery('#cf7-location-lat').val(latlng.lat());
                        jQuery('#cf7-location-lng').val(latlng.lng());

                        // I Build the url
                        //example: http://maps.google.com/maps?z=12&q=loc:38.9419+-78.3020
                        url = 'http://maps.google.com/maps?z=12&q=loc:';
                        url += latlng.lat();
                        url += '+';
                        url += latlng.lng();
                        jQuery('#cf7-location-url').val(url);
                        // Rimuovo eventuali marker presenti
                        map.removeMarkers();
                        // Aggiungo il nuovo marker con la nuova posizione
                        map.addMarker({
                            lat: latlng.lat(),
                            lng: latlng.lng(),

                            // Permetto all'utente di spostare il puntatore
                            draggable: true,
                            dragend: function(e) {

                                // Se l'utente sposta il marker aggiorno le coordinate
                                jQuery('#cf7-location-lat').val(e.latLng.lat());
                                jQuery('#cf7-location-lng').val(e.latLng.lng());

                                // I Build the url
                                //example: http://maps.google.com/maps?z=12&q=loc:38.9419+-78.3020
                                //url = 'http://maps.google.com/maps?z=12&q=loc:';
                                //url += e.latLng.lat();
                                //url += '+';
                                //url += e.latLng.lng();
                                jQuery('#cf7-location-url').val('http://maps.google.com/maps?z=12&q=loc:'+e.latLng.lat()+'+'+e.latLng.lng());

                                //Reverse Geocoding after marker drop
                                GMaps.geocode({
                                    lat: e.latLng.lat(),
                                    lng: e.latLng.lng(),
                                    callback: function(results, status) {
                                        if (status == 'OK') {
                                            jQuery('#cf7-geocode-address').val(results["0"].formatted_address);
                                        }
                                    }
                                });
                            }
                        });
                        // Zoommo sulla posizione
                        map.setZoom(13);
                    }else{
                        alert(def_err_msg);
                    }
                }
            });
        });
        jQuery( '#cf7-geocode-reset' ).click(function() {
            map = new GMaps({
                div: '#cf7-location-map',
                scrollwheel: false,
                mapTypeControl: false,
                streetViewControl: false,
                lat: def_latitude,
                lng: def_longitude,
                zoom: def_zoom,
                mapType: def_maps_view
            });

            jQuery('#cf7-location-lat').val('');
            jQuery('#cf7-location-lng').val('');
            jQuery('#cf7-location-url').val('');
            jQuery('#cf7-geocode-address').val('');
        });
    }
});

/**
 * Refresh the map & gui when windows is resized
 */
jQuery( window ).resize(function() {
    if (jQuery('#cf7-location-map').length) {
        map.refresh();
        ui_reload();
    }
});
