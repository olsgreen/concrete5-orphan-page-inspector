<?php 
defined('C5_EXECUTE') or die("Access Denied.");

/**
 *
 * Orphan Page Inspector
 * Package adding the ability to view, move & delete orphan pages.
 *
 * @author Oliver Green <green2go@gmail.com>
 * @link http://olsgreen.com
 * @license http://www.gnu.org/licenses/gpl.html GPL
 *
 */

class OgOrphanPageInspectorPackage extends Package
{
    protected $pkgHandle = 'og_orphan_page_inspector';
    protected $appVersionRequired = '5.6';
    protected $pkgVersion = '0.10';
    
    public function getPackageDescription() {
        return t("Package adding the ability to view, move & delete orphan pages.");
    }
    
    public function getPackageName() {
        return t("Orphan Page Inspector");
    }
    
    public function install() { 
        
        $pkg = parent::install();          
        
        // Install page
        $p = SinglePage::add('/dashboard/sitemap/orphan_page_inspector', $pkg);     
        
        // Set icon
        $cak = CollectionAttributeKey::getByHandle('icon_dashboard');
        if (is_object($cak)) {
            if (is_object($p) && (!$p->isError())) {
                $p->setAttribute('icon_dashboard', 'icon-wrench');
            }
        }
        
        return $pkg;
        
    }    
    
}        