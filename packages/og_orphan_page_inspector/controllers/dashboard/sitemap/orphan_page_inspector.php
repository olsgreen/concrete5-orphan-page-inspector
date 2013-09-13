<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSitemapOrphanPageInspectorController extends Controller {
    
    public function view() {        
        
        $this->setItemsPerPage();
        
        $this->set('orphan_pages', $this->getOrphanPageList());
        
        if(isset($_GET['success'])) {         
            $this->set('message', 'The pages were successfully moved!');   
        } elseif($_GET['error']) {
            $this->set('message', 'Ooops! There was a problem moving the pages, please try again.');   
        }
        
    }
    
    private function setItemsPerPage() {
        
        $itemsPerPage = 10;
        
        if(isset($_GET['ccm_items_per_page']) && intval($_GET['ccm_items_per_page']) > 0) {            
            $itemsPerPage = $_GET['ccm_items_per_page'];            
        } elseif(isset($_SESSION['og_orphan_ccm_items_per_page']) && intval($_SESSION['og_orphan_ccm_items_per_page']) > 0) {            
            $itemsPerPage = $_SESSION['og_orphan_ccm_items_per_page'];            
        }
        
        $_SESSION['og_orphan_ccm_items_per_page'] = $itemsPerPage;
        
        $this->set('itemsPerPage', $itemsPerPage);
        
    }
    
    public function do_action() {
        
        $cParentID = ($this->post('ccm_orphan_operation') === 'trash') ? 'trash' : intval($this->post('Move_cID'));        
        $r = $this->movePages($this->post('cID'), $cParentID);
        
        if($r) {
            $this->redirect('/dashboard/sitemap/orphan_page_inspector?success');
        } else {
            $this->redirect('/dashboard/sitemap/orphan_page_inspector?error');
        }
        
    }
    
    public function getOrphanPageList() {
        
        $db = Loader::db();        
        $q = "SELECT Orphans.cID FROM Pages AS Orphans LEFT JOIN Pages AS Parents ON Orphans.cParentID = Parents.cID";
        $q .= " WHERE Parents.cID IS NULL AND Orphans.cID <> 1 AND Orphans.cIsSystemPage = 0 AND Orphans.cParentID <> 0;";
        $r = $db->query($q);
        
        $pages = array();
        
        while(!$r->EOF) {
            
            $pages[] = Page::getByID($r->fields['cID']);
            $r->MoveNext();
        }
        
        $pageList = new ItemList();
        $pageList->setItems($pages);
        
        $pageList->setItemsPerPage(isset($_SESSION['og_orphan_ccm_items_per_page']) ? intval($_SESSION['og_orphan_ccm_items_per_page']) : 10);
        
        return $pageList;
        
    }
    
    public function movePages($cIDs, $cParentID) {
        
        $result = true;
        if($cParentID != 'trash') {
            $cParent = Page::getByID($cParentID);
            if(!$cParent instanceof Page) return false;
        }
        
        foreach($cIDs as $cID) {
            
            $p = Page::getByID($cID);
            
            if(!$p instanceof Page) $result = false;
            
            if($cParentID === 'trash') {
                $p->moveToTrash();
            } else {
                $p->move($cParent);
            }
            
        }
        
        return $result;
        
    }
    
}