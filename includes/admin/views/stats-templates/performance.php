<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Performance Stats **/ ?>

<div class="stats__section">

    <h3><?php _e( 'Performance Stats', 'ohdear' ); ?></h3>

    <?php if ( ! empty( $perf['data'] ) ) :

        $p_created  = array();
        $p_dns      = array();
        $p_tcp      = array();
        $p_tlc      = array();
        $p_server   = array();
        $p_download = array();

        foreach ( $perf['data'] as $key => $p ) {

            $p_created[]  = $p['created_at'];
            $p_dns[]      = $p['time_namelookup'];
            $p_tcp[]      = $p['time_connect'];
            $p_tlc[]      = $p['time_appconnect'];
            $p_server[]   = $p['time_remoteserver'];
            $p_download[] = $p['time_download'];
        }

    if ( ! empty( $p_created ) ) :

        $p_count = count( $p_created ); ?>

        <p><?php echo __( 'Last 7 days', 'ohdear' ) . '. ' . __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][1]['latest_run_ended_at']; ?></p>

        <div id="perf_chart"></div>

        <style>
            #perf_chart {
                margin: 35px 10px;
            }
        </style>

        <script>
          var
            options = {
              chart: {
                height: 300,
                type: 'area'
              },
              dataLabels: {
                enabled: false
              },
              stroke: {
                curve: 'smooth',
                width: 2
              },
              tooltip: {
                x: {
                  format: 'dd MMM, HH:mm'
                },
              },
              series: [
                {
                  name: 'DNS Lookup time',
                  data: [
                      <?php
                      for ( $i = 0; $i < $p_count; $i++ ) {

                          echo $p_dns[$i];

                          if ( $i < $p_count - 1 )
                              echo ', ';
                      }
                      ?>
                  ]
                },
                {
                  name: 'TCP connection time',
                  data: [
                      <?php
                      for ( $i = 0; $i < $p_count; $i++ ) {

                          echo $p_tcp[$i];

                          if ( $i < $p_count - 1 )
                              echo ', ';
                      }
                      ?>
                  ]
                },
                {
                  name: 'TLS connection time',
                  data: [
                      <?php
                      for ( $i = 0; $i < $p_count; $i++ ) {

                          echo $p_tlc[$i];

                          if ( $i < $p_count - 1 )
                              echo ', ';
                      }
                      ?>
                  ]
                },
                {
                  name: 'Remote server processing',
                  data: [
                      <?php
                      for ( $i = 0; $i < $p_count; $i++ ) {

                          echo $p_server[$i];

                          if ( $i < $p_count - 1 )
                              echo ', ';
                      }
                      ?>
                  ]
                },
                {
                  name: 'Content download',
                  data: [
                      <?php
                      for ( $i = 0; $i < $p_count; $i++ ) {

                          echo $p_download[$i];

                          if ( $i < $p_count - 1 )
                              echo ', ';
                      }
                      ?>
                  ]
                },
              ],
              xaxis: {
                type: 'datetime',
                categories: [
                    <?php
                    for ( $i = 0; $i < $p_count; $i++ ) {

                        echo '"' . $p_created[$i] . ' GMT"';

                        if ( $i < $p_count - 1 )
                            echo ', ';
                    }
                    ?>
                ]
              },
              yaxis: {
                labels: {
                  formatter: (val) => { return 1000 * val.toFixed(3) + 'ms' }
                }
              }
            },

            chart = new ApexCharts(document.querySelector( "#perf_chart" ), options);

          chart.render();
        </script>

    <?php endif;

    else : ?>
        <div class="notice notice-warning inline">
            <p>
                <?php _e( 'No Performance stats was received for current site', 'ohdear' ); ?>
            </p>
        </div>
    <?php endif; ?>

</div><!-- /.stats__section -->

<?php