<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Uptime Stats **/ ?>

<div class="stats__section">

    <h3><?php _e( 'Uptime Stats', 'ohdear' ); ?></h3>

    <?php
        $uptime = ohdear()->api->get_uptime();

        if ( ! empty( $uptime ) ) :

        $u_datetime = array();
        $u_percent  = array();

        foreach ( $uptime as $key => $u ) {

            $u_percent[]  = $u['uptime_percentage'];
            $u_datetime[] = $u['datetime'];
        }

    if ( ! empty( $u_datetime ) ) :

        $u_count = count( $u_datetime ); ?>

        <p><?php echo __( 'Last 30 days', 'ohdear' ) . '. ' . __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][0]['latest_run_ended_at']; ?></p>

        <div id="uptime_chart"></div>

        <style>
            #uptime_chart {
                margin: 35px 10px;
            }
        </style>

        <script>
          var
            options = {
              chart: {
                height: 300,
                width: "100%",
                type: "bar",
                animations: {
                  initialAnimation: {
                    enabled: false
                  }
                }
              },
              series: [{
                name: "Uptime Stats",
                data: [
                    <?php
                    for ( $i = 0; $i < $u_count; $i++ ) {

                        echo '{x: "' . $u_datetime[$i] . ' GMT", y: ' . $u_percent[$i] . '}';

                        if ( $i < $u_count - 1 )
                            echo ',';
                    }

                    echo PHP_EOL;
                    ?>
                ]
              }],
              xaxis: {
                type: "datetime"
              },
              yaxis: {
                labels: {
                  formatter: (val) => { return val.toFixed(0) + '%' }
                },
                max: 100
              }
            },

            chart = new ApexCharts(document.querySelector( "#uptime_chart" ), options);

          chart.render();
        </script>

    <?php endif;

    else : ?>
        <div class="notice notice-warning inline">
            <p>
                <?php _e( 'No Uptime stats were received for current site', 'ohdear' ); ?>
            </p>
        </div>
    <?php endif; ?>

</div><!-- /.stats__section -->

<?php