<?php  defined('C5_EXECUTE') or die('Access Denied'); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Orphan Page Inspector'), t('Manage orphan pages within your site.'), false, false);?>

<?php 
$form = Loader::helper('form/page_selector');
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) { ?>

<script type="text/javascript">
    
    function setItemsPerPage(items) {
        
        <?php $qs = preg_replace('/ccm_items_per_page=[0-9]*(&)?/i', '', $_SERVER['QUERY_STRING']); ?>
        var location = '<?php echo View::url($c->getCollectionPath()); ?>?ccm_items_per_page=' + parseInt(items);
        location += '<?php if(strlen($qs) > 0) { echo '&' . $qs; } ?>';
        window.location = location;
        
    }
    
    function ccm_orphan_location_selected(cID, cName) {
        
        $('input[name=Move_cID]').val(cID);
        
        var q = confirm('Are you sure you want to move these pages underneath ' + cName + '?');
        if(q) {
            $('#ccm_do_orphan_action_form').submit();
        } else {
            resetOperationSelection();   
        }
        
    }
    
    function resetOperationSelection() {
           $('#ccm-page-list-multiple-operations').val('');
    }
    
    $(function () { 
        
        $('input[type=checkbox]').change(function () {
            
            var page_list_ops = $('#ccm-page-list-multiple-operations');
            
            if($('input[type=checkbox]:checked').length > 0) {                
                page_list_ops.removeAttr('disabled');                
            } else {                
                page_list_ops.attr('disabled', 'disabled');                
            }
            
        });
        
        $('#ccm-page-list-multiple-operations').change(function () {
            
            if($(this).val() === 'move') { 
                
                $('.ccm-sitemap-select-page').trigger('click');     
                
            } else if($(this).val() === 'trash') {
                
                var q = confirm('Are you sure you want to move these pages to Trash?');
                if(q) {
                    $('#ccm_do_orphan_action_form').submit();
                } else {
                    resetOperationSelection();   
                }
                
            }
            
        });
        
    });
    
</script>


<form id="ccm_do_orphan_action_form" method="post" action="<?php echo $this->action('do_action'); ?>" >
    
    <!-- A few bits the user doesn't need to see !-->
    <div style="display: none;">
        <?php echo $form->selectPage('Move_cID', false, 'ccm_orphan_location_selected'); ?>
    </div>
    
    <div class="ccm-pane-body">
        
        <div style="margin-bottom: 10px;">
            <select id="ccm-page-list-multiple-operations" name="ccm_orphan_operation" class="span3" disabled="disabled">
                <option value="">** With Selected</option>
                <option value="trash">Move to Trash</option>
                <option value="move">Move to Location</option>
            </select> 
            
            <div style="float: right;">
                <label for="ccm-items-per-page" style="display: inline-block; vertical-align: bottom;">Items Per Page:</label>
                <select id="ccm-items-per-page" style="width: 70px;" onchange="setItemsPerPage(this.value);">
                    <option<?php echo (isset($_GET['ccm_items_per_page']) && $_GET['ccm_items_per_page'] == '10') ?  " selected='selected'" : ''; ?>>10</option>
                    <option<?php echo (isset($_GET['ccm_items_per_page']) && $_GET['ccm_items_per_page'] == '20') ?  " selected='selected'" : ''; ?>>20</option>
                    <option<?php echo (isset($_GET['ccm_items_per_page']) && $_GET['ccm_items_per_page'] == '50') ?  " selected='selected'" : ''; ?>>50</option>
                    <option<?php echo (isset($_GET['ccm_items_per_page']) && $_GET['ccm_items_per_page'] == '100') ?  " selected='selected'" : ''; ?>>100</option>
                    <option<?php echo (isset($_GET['ccm_items_per_page']) && $_GET['ccm_items_per_page'] == '200') ?  " selected='selected'" : ''; ?>>200</option>
                </select> 
            </div>
            
        </div>
        
        <table class="ccm-results-list  ">
            
            <tr>
                <th></th>
                <th>Page Name</th>
                <th>Page Path</th>
                <th>Page ID</th>
                <th>Creation Date</th>
            </tr>
            
            <?php if(count($orphan_pages) > 0) { foreach($orphan_pages->getPage() as $page) { ?>
            
            <tr>
                <td><input type="checkbox" id="cID-<?php echo $page->getCollectionID(); ?>" value="<?php echo $page->getCollectionID(); ?>" name="cID[]" /></td>
                <td><a href="<?php echo View::Url($page->getCollectionPath()); ?>"><?php echo $page->getCollectionName(); ?></a></td>            
                <td><?php echo $page->getCollectionPath(); ?></td>
                <td><?php echo $page->getCollectionID(); ?></td>
                <td><?php echo $page->getCollectionDateAdded(); ?></td>
            </tr>
            
            <?php } } else { ?>
            
            <tr>
                <td colspan="5">This site has no orphan pages.</td>        
            </tr>
            
            <?php } ?>
            
        </table>
        
        <div style="float: left; margin-top: 26px;"><?php echo $orphan_pages->displaySummary(); ?></div>
        <div style="float: right"><?php echo $orphan_pages->displayPagingV2(); ?></div>
        <br style="clear:both;" />
    </div>	
    
    <?php  } else { ?>
    <div class="ccm-pane-body">
        <p><?php echo t("You must have access to the dashboard sitemap to search pages.")?></p>
    </div>	
    
    <?php  } ?>
    
    
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>