'use strict';

jQuery(document).ready(function($) {

  /**
   * Ajax Dashboard Widgets
   */

  if ( $('#dashboard_ohdear_uptime').length ) {

    $.ajax({
      type: "GET",
      data: {
        action: 'ohdear_load_uptime_widget'
      },
      url: ajaxurl,
      success: function (response) {
        $('#dashboard_ohdear_uptime .inside').html( response );
      }
    });
  }

  if ( $('#dashboard_ohdear_performance').length ) {

    $.ajax({
      type: "GET",
      data: {
        action: 'ohdear_load_perf_widget'
      },
      url: ajaxurl,
      success: function (response) {
        $('#dashboard_ohdear_performance .inside').html( response );
      }
    });
  }

  if ( $('#dashboard_ohdear_broken').length ) {

    $.ajax({
      type: "GET",
      data: {
        action: 'ohdear_load_broken_widget'
      },
      url: ajaxurl,
      success: function (response) {
        $('#dashboard_ohdear_broken .inside').html( response );
      }
    });
  }

});