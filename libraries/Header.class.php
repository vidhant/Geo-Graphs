<?php
/**
 * Manages the Header on every page.
 */

if (! defined('GeoGraphs')) {
    exit;
}

/**
 * Class used to output the HTML headers
 */
class GG_Header
{
    /**
     * Whether to display anything.
     *
     * @access private
     * @var bool
     */
    private $_isEnabled;
    /**
     * Array containing names of Javascripts to be included.
     *
     * @access private
     * @var array
     */
    private $_scripts;
    /**
     * Array containing names of Stylesheets to be included.
     *
     * @access private
     * @var array
     */
    private $_stylesheets;
    /**
     * Variable containing Title of the page.
     *
     * @access private
     * @var string
     */
    private $_title;
    /**
     * The value for the id attribute for the body tag
     *
     * @access private
     * @var string
     */
    private $_bodyId;
    /**
     * Whether to display global message box or not.
     *
     * @access private
     * @var bool
     */
    private $_displayGlobalMessage;

    /**
     * Creates a new class instance.
     */
    public function __construct()
    {
        $this->_isEnabled = true;
        $this->_displayGlobalMessage = true;
        $this->_title = ' | Geo-Graphs';
        $this->_scripts = array();
        $this->_scripts = array();
        $this->_addDefaultFiles();
    }

    /**
     * Loads common scripts and stylesheets.
     *
     * @return void
     */
    private function _addDefaultFiles()
    {
        $default_scripts = array(
            'jquery-1.10.2.min.js',
            'jquery-ui.min.js',
            'jquery-uniform.js',
            'cytoscape.min.js',
            'common.js'
            );
        foreach ($default_scripts as $key => $filename) {
            $this->addFile($filename, 'js');
        }

        $default_stylesheets = array(
            'reset.css',
            'absolution.css',
            'jquery-ui.min.css',
            'slideshow.css',
            'common.css'
            );
        foreach ($default_stylesheets as $key => $filename) {
            $this->addFile($filename, 'css');
        }
    }

    /**
     * Sets Title of the page.
     *
     * @param string
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->_title = htmlspecialchars($title) . $this->_title;
    }

     /**
     * Setter for the ID attribute in the BODY tag
     *
     * @param string $id Value for the ID attribute
     *
     * @return void
     */
    public function setBodyId($id)
    {
        $this->_bodyId = htmlspecialchars($id);
    }

    /**
     * Adds a Javascript/Stylesheet file to page.
     *
     * @param string filename
     * @param string file type
     *
     * @return void
     */
    public function addFile($filename, $type)
    {
        $hash = md5($filename);
        switch ($type) {
            case 'js':
                if (! isset($this->_scripts[$hash])) {
                    $this->_scripts[$hash] = $filename;
                }
                break;
            case 'css':
                if (! isset($this->_stylesheets[$hash])) {
                    $this->_stylesheets[$hash] = $filename;
                }
                break;
        }
    }

    /**
     * Returns the DOCTYPE and the start HTML tag
     *
     * @return string DOCTYPE and HTML tags
     */
    private function _getHtmlStart()
    {
        $retval  = "<!DOCTYPE HTML>";
        $retval .= "<html lang='en' >";

        return $retval;
    }

    /**
     * Returns the META tags
     *
     * @return string the META tags
     */
    private function _getMetaTags()
    {
        $retval  = '<meta http-equiv="Content-Type" content="text/html;'
            . ' charset=UTF-8">';

        return $retval;
    }

    /**
     * Returns the LINK tags for the favicon and the stylesheets
     *
     * @return string the LINK tags
     */
    private function _getLinkTags()
    {
        $retval = '<link rel="icon" href="favicon.ico" '
            . 'type="image/x-icon" />'
            . '<link rel="shortcut icon" href="favicon.ico" '
            . 'type="image/x-icon" />';
        // Stylesheets
        $separator = '&stylesheets[]=';
        $url = 'css/get_stylesheets.php'
            . '?stylesheets[]='
            . implode($separator, $this->_stylesheets);
        $link = sprintf(
            '<link rel="stylesheet" type="text/css" href="%s" >',
            htmlspecialchars($url)
        );

        $retval .= $link;

        $sql_query = 'SELECT * FROM entity_images WHERE name = \'' . $_REQUEST['source'] . '\'';
        $result = $GLOBALS['dbi']->executeQuery($sql_query, array());
        while($row = $result->fetch()) {
            $images[] = $row['image'];
        }

        $retval .= '<style>';
        $i = 1;
        foreach ($images as $image) {
            $retval .= '.cb-slideshow li:nth-child(' . $i . ') span {'
            . 'background-image: url(\'' . $image . '\'); } ';
            $i++;
        }
        $retval .= '</style>';

        return $retval;
    }

    /**
     * Returns the TITLE tag
     *
     * @return string the TITLE tag
     */
    private function _getTitleTag()
    {
        $retval  = "<title>";
        $retval .= $this->_getPageTitle();
        $retval .= "</title>";
        return $retval;
    }

    /**
     * If the page is missing the title, this function
     * will set it to something reasonable
     *
     * @return string
     */
    private function _getPageTitle()
    {
        if (empty($this->_title)) {
            $this->_title = 'Geo-Graphs';
        }

        return $this->_title;
    }

    /**
     * Returns the SCRIPT tag.
     *
     * @return string SCRIPT tag.
     */
    private function _getScriptTag()
    {
        $script_tag = '';
        $separator = '&scripts[]=';
        $url = 'js/get_scripts.php'
            . '?scripts[]='
            . implode($separator, $this->_scripts);

        $script_tag .= sprintf(
            '<script type="text/javascript" src="%s"></script>',
            htmlspecialchars($url)
        );

        return $script_tag;
    }

    /**
     * Returns the close tag to the HEAD
     * and the start tag for the BODY
     *
     * @return string HEAD and BODY tags
     */
    private function _getBodyStart()
    {
        $retval = "</head><body";
        if (! empty($this->_bodyId)) {
            $retval .= " id='" . $this->_bodyId . "'";
        }
        $retval .= ">";

        return $retval;
    }

    /**
     * Returns body header part. (i.e. Heading etc.)
     *
     * @return string Body Header.
     */
    private function _getBodyHeader()
    {
        $retval = '';

        $retval = '<header class="">'
            . '<a href="index.php"><div class="app_title">Geo-Graphs</div></a>'
            . '</header>';
        $retval .= $this->getSlideshowImages();
        $retval .= '<div class="body_area">';
        $retval .= '<div class="body_content">';
        return $retval;
    }

    /**
     * Returns Login details box, if available
     *
     * @return string Login box
     */
    private function _getLoginDetails()
    {
        $retval = '';
        if (isset($_SESSION['login_id'])) {
            $retval .= '<div class="login_details">'
                . 'welcome '
                . '<strong class="username"> ' . $_SESSION['login_id'] . ' </strong>'
                . '<a href="logout.php" class="logout_link"> [Logout]</a>'
                . '</div>';
        }

        return $retval;
    }

    /**
     * Returns global message box, if any message to display
     *
     * @return string Html
     */
    private function _getGlobalMessage()
    {
        $retval = '';

        return $retval;
    }

    /**
     * Disables the rendering of the header.
     *
     * @return void
     */
    public function disable()
    {
        $this->_isEnabled = false;
    }

    /**
     * Disables global message box.
     *
     * @return void
     */
    public function disableGlobalMessage()
    {
        $this->_displayGlobalMessage = false;
    }

    /**
     * Generates the header
     *
     * @return string The header
     */
    public function getHeader()
    {
        $retval = '';
        if ($this->_isEnabled) {
            $retval .= $this->_getHtmlStart();
            $retval .= $this->_getMetaTags();
            $retval .= $this->_getLinkTags();
            $retval .= $this->_getTitleTag();
            $retval .= $this->_getScriptTag();
            $retval .= $this->_getBodyStart();
            $retval .= $this->_getBodyHeader();
        }

        return $retval;
    }

    public function getSlideshowImages() {
        $retval = '<ul class="cb-slideshow">
            <li><span></span><div><h3>Lorem ipsum</h3></div></li>
            <li><span></span><div><h3>Lorem ipsum</h3></div></li>
            <li><span></span><div><h3>Lorem ipsum</h3></div></li>
        </ul>';

        return $retval;
    }
}

?>