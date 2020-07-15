<?php
/**
 * Stats Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_settings();

if ( empty( $settings['api_status'] ) ) : ?>

    <div class="notice notice-warning inline">
        <p>
            <?php printf( __( 'Please set the valid Oh Dear API token at the <a href="%1$s" alt="%2$s">%2$s</a> tab.', 'ohdear' ),
                esc_url( add_query_arg( array( 'tab' => 'settings' ) ) ),
                esc_attr( 'Settings', 'ohdear' ) );
            ?>
        </p>
    </div>

<?php else :

    $data   = ohdear()->api->get_site_data();
    $broken = ohdear()->api->get_broken();
    $uptime = ohdear()->api->get_uptime();
    $perf   = ohdear()->api->get_perf();

    /** Broken Links **/ ?>

    <div class="stats__section">

        <h3><?php _e( 'Broken Links', 'ohdear' ); ?></h3>

        <?php if ( ! empty( $broken['data'] ) ) :

            $b_links = $broken['data']; ?>

            <p><?php echo __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][2]['latest_run_ended_at']; ?></p>

            <table class="wp-list-table widefat striped">
                <thead>
                <tr>
                    <th class="column-primary" scope="col">
                        <?php esc_html_e( 'Status Code', 'ohdear' ); ?>
                    </th>

                    <th scope="col">
                        <?php esc_html_e( 'Broken Link', 'ohdear' ); ?>
                    </th>

                    <th scope="col">
                        <?php esc_html_e( 'Found on', 'ohdear' ); ?>
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ( $b_links as $b_link ) : ?>
                    <tr>
                        <td class="column-primary"> <?php echo $b_link['status_code']; ?></td>

                        <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['crawled_url'] ); ?></td>

                        <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['found_on_url'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php else : ?>
            <div class="notice notice-warning inline">
                <p>
                    <?php _e( 'No Broken Links was found for current site', 'ohdear' ); ?>
                </p>
            </div>
        <?php endif; ?>

    </div><!-- /.stats__section -->

    <?php /** Uptime Stats **/ ?>

    <div class="stats__section">

        <h3><?php _e( 'Uptime Stats', 'ohdear' ); ?></h3>

        <?php if ( ! empty( $uptime ) ) :

            $datetime = array();
            $percentage = array();

            foreach ( $uptime as $key => $u ) {
                $percentage[] = $u['uptime_percentage'];
                $datetime[]   = $u['datetime'];
            }

            if ( ! empty( $datetime ) ) : ?>

                <p><?php echo __( 'Past 30 days', 'ohdear' ) . '. ' . __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][0]['latest_run_ended_at']; ?></p>

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
                                $count = count( $datetime );

                                for ( $i = 0; $i < $count; $i++ ) {

                                    echo '{x: "' . $datetime[$i] . ' GMT", y: ' . $percentage[$i] . '}';

                                    if ( $i < $count - 1 )
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
                    <?php _e( 'No Uptime stats was received for current site', 'ohdear' ); ?>
                </p>
            </div>
        <?php endif; ?>

    </div><!-- /.stats__section -->

    <?php /** Performance Stats **/ ?>

    <div class="stats__section">

        <h3><?php _e( 'Performance Stats', 'ohdear' ); ?></h3>

        <?php if ( ! empty( $perf['data'] ) ) :

            $p_stats = $perf['data']; ?>

            <p><?php echo __( 'Past 24 hours', 'ohdear' ) . '. ' . __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][1]['latest_run_ended_at']; ?></p>

        <?php
echo '<pre>';
var_dump( $p_stats );
echo '</pre>';

        else : ?>
            <div class="notice notice-warning inline">
                <p>
                    <?php _e( 'No Performance stats was received for current site', 'ohdear' ); ?>
                </p>
            </div>
        <?php endif; ?>

    </div><!-- /.stats__section -->

<?php endif;