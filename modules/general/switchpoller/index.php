<?php
set_time_limit (0);

if(cfr('SWITCHPOLL')) {
    
    /**
     * Returns FDB cache lister MAC filters setup form
     * 
     * @return string
     */
    function web_FDBTableFiltersForm() {
        $currentFilters='';
        $oldFilters=  zb_StorageGet('FDBCACHEMACFILTERS');
        if (!empty($oldFilters)) {
            $currentFilters=  base64_decode($oldFilters);
        }
        
        $inputs=__('One MAC address per line').  wf_tag('br');
        $inputs.=  wf_TextArea('newmacfilters', '', $currentFilters, true, '40x10');
        $inputs.= wf_HiddenInput('setmacfilters', 'true');
        $inputs.= wf_CheckInput('deletemacfilters', __('Cleanup'), true, false);
        $inputs.= wf_Submit(__('Save'));
        $result=  wf_Form('', 'POST', $inputs, 'glamour');
        
        return ($result);    
    }
    
    
  /**
   * Shows current FDB cache list container
   * 
   * @param string $fdbSwitchFilter
   */
 function web_FDBTableShowDataTable($fdbSwitchFilter='') {
     $filter=(!empty($fdbSwitchFilter)) ? '&swfilter='.$fdbSwitchFilter : '' ;
     $filtersForm=  wf_modalAuto(web_icon_search('MAC filters setup'), __('MAC filters setup'), web_FDBTableFiltersForm(), '');
     
     $columns=array('Switch IP','Port','Location','MAC','User');
     $result=  wf_JqDtLoader($columns, '?module=switchpoller&ajax=true'.$filter, true, 'Objects',100);
      
     show_window(__('Current FDB cache').' '.$filtersForm,$result);
  }
    
    $allDevices=  sp_SnmpGetAllDevices();
    $allTemplates= sp_SnmpGetAllModelTemplates();
    $allTemplatesAssoc=  sp_SnmpGetModelTemplatesAssoc();
    $allusermacs=zb_UserGetAllMACs();
    $alladdress= zb_AddressGetFullCityaddresslist();
    $alldeadswitches=  zb_SwitchesGetAllDead();
    $deathTime=  zb_SwitchesGetAllDeathTime();
    
    //poll single device
    if (wf_CheckGet(array('switchid'))) {
        $switchId=vf($_GET['switchid'],3);
        if (!empty($allDevices)) {
            foreach ($allDevices as $ia=>$eachDevice) {
                if ($eachDevice['id']==$switchId){
                    //detecting device template
                    if (!empty($allTemplatesAssoc)) {
                        if (isset($allTemplatesAssoc[$eachDevice['modelid']])) {
                            if (!isset($alldeadswitches[$eachDevice['ip']])) {
                              //cache cleanup
                                if (wf_CheckGet(array('forcecache'))) {
                                    $deviceRawSnmpCache=  rcms_scandir('./exports/', $eachDevice['ip'].'_*');
                                    if (!empty($deviceRawSnmpCache)) {
                                        foreach ($deviceRawSnmpCache as $ir=>$fileToDelete) {
                                            unlink('./exports/'.$fileToDelete);
                                        }
                                    }
                                    rcms_redirect('?module=switchpoller&switchid='.$eachDevice['id']);
                                }
                            $deviceTemplate=$allTemplatesAssoc[$eachDevice['modelid']];
                            $modActions=  wf_Link('?module=switches', __('Back'), false, 'ubButton');
                            $modActions.= wf_Link('?module=switchpoller&switchid='.$eachDevice['id'].'&forcecache=true', __('Force query'), false, 'ubButton');
                            show_window($eachDevice['ip'].' - '.$eachDevice['location'],  $modActions);
                            sp_SnmpPollDevice($eachDevice['ip'], $eachDevice['snmp'], $allTemplates, $deviceTemplate,$allusermacs,$alladdress,false);
                            } else {
                               show_window(__('Error'),__('Switch dead since').' '.@$deathTime[$eachDevice['ip']].  wf_delimiter().  wf_Link('?module=switches', __('Back'), false, 'ubButton'));
                            }
                        } else {
                            show_error(__('No').' '.__('SNMP template'));
                        }
                    }
                    
                }
            }
        }
        
    } else {
     
        
    //display all of available fdb tables
      $fdbData_raw=  rcms_scandir('./exports/', '*_fdb');
      if (!empty($fdbData_raw)) {
             //// mac filters setup
             if (wf_CheckPost(array('setmacfilters'))) {
              //setting new MAC filters
              if (!empty($_POST['newmacfilters'])) {
              $newFilters=  base64_encode($_POST['newmacfilters']);
              zb_StorageSet('FDBCACHEMACFILTERS', $newFilters);
              }
              //deleting old filters
              if (isset($_POST['deletemacfilters'])) {
                  zb_StorageDelete('FDBCACHEMACFILTERS');
              }
          }
          
          
         //push ajax data
         if (wf_CheckGet(array('ajax'))) {
            if (wf_CheckGet(array('swfilter'))) {
                $fdbData_raw=array($_GET['swfilter'].'_fdb');
            }
            die(sn_SnmpParseFdbCacheJson($fdbData_raw));
         } else {
             if (wf_CheckGet(array('fdbfor'))) {
                 $fdbSwitchFilter=$_GET['fdbfor'];
             } else {
                 $fdbSwitchFilter='';
             }
             web_FDBTableShowDataTable($fdbSwitchFilter);
         }
       
         
      } else {
          show_warning(__('Nothing found'));
      }

    }
    
    
} else {
    show_error(__('Access denied'));
}

?>
