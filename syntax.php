<?php
/**
 * Plugin Search Form: Inserts a search form in any page
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sergio Merino <sergio.merino@thecorpora.com>
 * @code based on the plugin searchform from Adolfo González Blázquez
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_confsearch extends DokuWiki_Syntax_Plugin {

    public function getType() { return 'substition'; }

    public function getSort() { return 138; }

    public function connectTo($mode) {
		$this->Lexer->addSpecialPattern('\{confsearch[^\}]*\}',$mode,'plugin_confsearch');
    }

    protected function getLastCrumb()
    {
	    $br = breadcrumbs();
	    $lastcrumb = '';
	    foreach ($br as $a=>$b) $lastcrumb=$a;
	    return $lastcrumb;
    }

    public function getBaseNs($id)
    {
	    return getNS(cleanID($id));
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
    	return array($match, $state, $pos);
    }

    protected function buttonname($data) {
        $params=trim($data[0]," \{\}");
        list($pluginname,$parameters,$button)=explode('>',$params,3);
        $replacedparams = str_replace(array(
                '@NS@',
                '@USER@',
                ),
            array(
                $this->getBaseNs($this->getLastCrumb()),
                $_SERVER['REMOTE_USER'],
                ), $button);
        if ($replacedparams=="")
        	$replacedparams="Search";
        return $replacedparams;
    }

    protected function processparameters($data) {
        $params=trim($data[0]," \{\}");
        list($pluginname,$parameters,$button)=explode('>',$params,3);
        $replacedparams = str_replace(array(
                '@NS@',
                '@USER@',
                ),
            array(
                $this->getBaseNs($this->getLastCrumb()),
                $_SERVER['REMOTE_USER'],
                ), $parameters);
        return $replacedparams;
    }

    public function render($mode, Doku_Renderer $renderer, $data) {

 		global $lang;


		if ($mode == 'xhtml') {

			$renderer->doc .= '<div id="searchform_plugin">'."\n";
			$renderer->doc .= '<form name="ns_search" action="'.wl().'" accept-charset="utf-8" class="search" id="dw__search2" method="get"><div class="no">'."\n";
			$renderer->doc .= '<input type="hidden" name="do" value="search" />'."\n";
			$renderer->doc .= '<input type="hidden" id="dw__ns" name="ns" value="'.$this->processparameters($data).'">'."\n";
//Debug line			$renderer->doc .= 'Debug--'.$this->processparameters($data);
                        $renderer->doc .= '<input type="text" id="qsearch2__in" accesskey="f" name="id" class="edit" autocomplete="off">'."\n";
                        $renderer->doc .= '<input type="submit" value="'.$this->buttonname($data).'" class="button" title="Search App" onclick= "document.ns_search.id.value= document.ns_search.id.value+\' \'+document.ns_search.ns.value" />'."\n";

                        $renderer->doc .= '<div id="qsearch2__out" class="ajax_qsearch JSpopup"></div>'."\n";
			$renderer->doc .= '</div></form>'."\n";
			$renderer->doc .= '</div>'."\n";
			return true;
		}
		return false;
	}
}
?>
