<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Uptime **/

if ( empty( $data['id'] ) ||
     empty( $data['checks'][0]['latest_run_ended_at'] )
){
    debug_log( 'ERROR: ' . 'admin/views/templates/uptime.php' . ' | ' . '$data is incorrect' );
    return;
} ?>

<div class="ohdear-dashboard__section">

    <h3><?php echo __( 'Uptime', 'ohdear' ) . '&nbsp;&nbsp;';

            /* translators: 1: Oh Dear API link, 2: 'Open on Oh Dear' phrase */
            printf( '<a href="%1$s" title="%2$s" target="_blank" style="font-weight: normal; font-size: small;">%2$s</a>',
                    'https://ohdear.app/sites/' . $data['id'] . '/checks/' . $data['checks'][0]['id'] . '/report/',
                    esc_html__( 'Open on Oh Dear', 'ohdear' )
            );
        ?>
    </h3>

    <?php
        $uptime = ohdear()->api->get_uptime();

        if ( ! empty( $uptime ) ) :

            $u_datetime = array();
            $u_percent  = array();

            foreach ( $uptime as $key => $u ) {

                $u_percent[]  = $u['uptime_percentage'];
                $u_datetime[] = $u['datetime'];
            }

            if ( ! empty( $u_datetime ) ) : ?>

                <p><?php
                    /* translators: 1: Days count, 2: Date */
                    printf( __( 'Last %1$s days. Last time checked: %2$s', 'ohdear' ),
                            ( ! empty( $u_days ) ) ? $u_days : count( $u_datetime ),
                            $data['checks'][0]['latest_run_ended_at']
                    );
                ?></p>

                <div id="ohdear-uptime_chart"></div>

                <script>
                  var
                    options = {
                      chart: {
                        height: <?php echo ( ! empty( $u_height ) ) ? (string) $u_height : '300'; ?>,
                        width: "100%",
                        type: "bar",
                        animations: {
                          initialAnimation: {
                            enabled: false
                          }
                        }
                      },
                      colors: [ '#e32929' ],
                      series: [{
                        name: "Uptime",
                        data: [
                            <?php
                                if ( ! empty( $u_days ) && $u_days < count( $u_datetime ) ) {

                                    $start = count( $u_datetime ) - $u_days;

                                } else {
                                    $start = 0;
                                }

                                for ( $i = $start; $i < count( $u_datetime ); $i++ ) {

                                    echo '{x: "' . $u_datetime[$i] . ' GMT", y: ' . $u_percent[$i] . '}';

                                    if ( $i < count( $u_datetime ) - 1 )
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

                    chart = new ApexCharts(document.querySelector( "#ohdear-uptime_chart" ), options);

                  chart.render();
                </script>

            <?php endif;

        else : ?>

            <div class="notice notice-warning inline">
                <p>
                    <?php _e( 'No Uptime stats for the current site', 'ohdear' ); ?>
                </p>
            </div>

        <?php endif; ?>

</div><!-- /.ohdear-dashboard__section -->