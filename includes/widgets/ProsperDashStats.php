<?php 


class Widget_ProsperDashStats extends WP_Widget {

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {
        //Register the widget...
        wp_add_dashboard_widget(
            'prosper_dash_stats',                                  //A unique slug/ID
            'Prosperent Stats',                             //Visible name for the widget
            array('Widget_ProsperDashStats', 'prosperGetStats')
        );
    }

    public static function prosperGetStats( )
    {          
        $options = get_option('prosperSuite');

        if ($accessKey = $options['prosperAccess'])
        {           
            require_once(PROSPER_MODEL . '/Search.php');
            $modelSearch = new Model_Search();
            $fetch = 'fetchClicks';
            $fetch2 = 'fetchCommissions';
            
            switch ($_GET['prosperDate'])
            {
                case 'yesterday':
                    $startDate = date('Ymd', strtotime('-1 days'));
                    $endDate = date('Ymd');
                    $timeFrame = 'yesterday';
                    break;
                case 'week':
                    $startDate = date('Ymd', strtotime('-7 days'));
                    $endDate = date('Ymd');
                    $timeFrame = 'the last 7 days';
                    break;
                default:
                    $startDate = date('Ymd', strtotime('-30 days'));
                    $endDate = date('Ymd');
                    $timeFrame = 'the last 30 days';
                    break;
            }

            $settings = array(                
                'accessKey'      => $accessKey,
                'filterHttpHost' => $_SERVER['HTTP_HOST'],
                'limit'          => 1000
            );

            $clickGroup = ($timeFrame == 'yesterday' ? 'clickDate' : 'clickDateYmd');
            $curlUrls['clickData'] = $modelSearch->apiCall(array_merge($settings, array(
                'groupBy'         => $clickGroup,
                'filterClickDate' => $startDate . ',' . $endDate
            )), $fetch);

            $commissionGroup = ($timeFrame == 'yesterday' ? 'commissionDate' : 'commissionDateYmd');
            $curlUrls['commissionData'] = $modelSearch->apiCall(array_merge($settings, array(
                'filterCommissionDate' => $startDate . ',' . $endDate,
                'filterCommissionType' => 'mine',
                'groupBy'              => $commissionGroup
            )), $fetch2);

            $everything = $modelSearch->multiCurlCall($curlUrls, PROSPER_CACHE_PRODS, array_merge($settings, array('date' => $startDate . ',' . $endDate)));
            
            $range = new DatePeriod(
                DateTime::createFromFormat('Ymd', $startDate),
                new DateInterval('P1D'),
                DateTime::createFromFormat('Ymd', $endDate));
            
            $dateRange = array();
            foreach($range as $i => $date)
            {
                $dateRange[(int) $date->format('Ymd')] = array(
                    'x' => strtotime($date->format('Y-m-d')) * 1000,
                    'y' => 0
                );
            }
            
            $initialClicks = array();            
            foreach ($everything['clickData']['data'] as $clicks)
            {
                $initialClicks[(string) $clicks[$clickGroup]] = array(
                    'x' => strtotime(DateTime::createFromFormat('Ymd', $clicks[$clickGroup])->format('Y-m-d')) * 1000,
                    'y' => $clicks['groupCount']
                );
            }
            
            $initialClicks += $dateRange;
            sort($initialClicks);          

            $initialCommissions = array();
            foreach ($everything['commissionData']['data'] as $commissions)
            {
                $initialClicks[(string) $commissions[$commissionGroup]] = array(
                    'x' => strtotime(DateTime::createFromFormat('Ymd', $commissions[$commissionGroup])->format('Y-m-d')) * 1000,
                    'y' => $commissions['totalPaymentAmount']
                );
            }         
            
            $initialCommissions += $dateRange;
            sort($initialCommissions);
            
            
            if (($initialCommissions || $initialClicks) || $_GET['prosperDate']) :
            ?>
                <table style="width:100%;">
                    <tr style="float:right;">
                        <td><a style="vertical-align:baseline;" class="button-secondary" href="<?php echo admin_url( 'index.php?prosperDate=yesterday'); ?>">Yesterday</a></td>
                        <td><a style="vertical-align:baseline;" class="button-secondary" href="<?php echo admin_url( 'index.php?prosperDate=week'); ?>">Last 7 Days</a></td>
                        <td><a style="margin-left:2px; vertical-align:baseline;" class="button-secondary" href="<?php echo admin_url( 'index.php?prosperDate=month'); ?>">Last 30 Days</a></td>
                    </tr>
                </table>
            <?php 
            if (!$initialCommissions && !$initialClicks)
            {       
                echo '<div><span style="font-size:16px;font-weight:bold;display:block;padding:8px 0;">No stats for ' . $timeFrame . '.</span><span style="font-size:14px;">Please select a different range.</span></div>';
            }
            ?>
            
            <script type="text/javascript" src="<?php echo PROSPER_JS;?>/canvasjs.min.js"></script>
            <script type="text/javascript">
            
            window.onload = function(){ 
                document.getElementById("prosperClickContainer").style.display = "inline-block";       
                var prosperClicks = new CanvasJS.Chart("prosperClickContainer",
                {      
                  title:{
                    text: "Clicks"
                  },
                  animationEnabled: true,
                  axisX:{      
                      valueFormatString: "MMM DD, YY" ,
                      labelAngle: -50
                  },
                  axisY :{
                    includeZero: true,
                    valueFormatString: "#,###"
                  },
                  toolTip: {
                    shared: "true"
                  },
                  data: [
                  {        
                    type: "area",   
                    xValueType: "dateTime",   
                    dataPoints: <?php echo json_encode($initialClicks); ?>
                  }]
                });

                prosperClicks.render();   

                document.getElementById("prosperCommissionContainer").style.display = "inline-block";
                var prosperCommissions = new CanvasJS.Chart("prosperCommissionContainer",
                {      
                  title:{
                    text: "Commissions"
                  },
                  animationEnabled: true,
                  axisX:{      
                      valueFormatString: "MMM DD, YY" ,
                      labelAngle: -50
                  },
                  axisY :{
                    includeZero: true,
                    valueFormatString: "$#,###"
                  },
                  toolTip: {
                    shared: "true"
                  },
                  data: [
                  {        
                    type: "area",   
                    xValueType: "dateTime",   
                    dataPoints: <?php echo json_encode($initialCommissions); ?>
                  }]
                });

                prosperCommissions.render();  
            }
              </script>
              
              <div id="prosperClickContainer" style="display:none;height: 300px; width: 100%;"></div>
              <div id="prosperCommissionContainer" style="display:none;height: 300px; width: 100%;"></div>
            <?php 
            else:
            echo '<div><span style="font-size:16px;font-weight:bold;display:block;padding-bottom:6px">No stats to report.</span><span style="font-size:14px;">If you need any help, let us know.</span></div>';
            endif;
        }
        else
        {
            echo '<div><span style="font-size:16px;font-weight:bold;display:block;padding-bottom:6px">Opps.</span><span style="font-size:14px;">You don\'t have your Prosperent Access Key set. Go to the <strong><a href="' . admin_url( 'admin.php?page=prosper_general' ) . '">General Settings</a></strong> to set it and then you will be able to see your Clicks and Commissions right here.</span></div>';
        }
        
    }
}