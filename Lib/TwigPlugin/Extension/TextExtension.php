<?php

namespace TwigPlugin\Extension;

class TextExtension extends \Twig_Extension
{
    /**
     * @var \TextHelper $textHelper
     */
    protected $textHelper;

    public function __construct($view)
    {
        \App::import('Helper', 'Text');
        $this->textHelper = new \TextHelper($view);
        $this->request = $this->textHelper->request;
        $this->response = $this->textHelper->response;
    }

    public function getFilters()
    {
        return array(
            'truncate' => new \Twig_Filter_Method($this, 'truncate',
                array(
                    'is_safe'       => array('html'),
                )
            ),
            'highlight' => new \Twig_Filter_Method($this, 'highlight',
                array(
                    'is_safe'       => array('html'),
                )
            ),
            'autoLink' => new \Twig_Filter_Method($this, 'autoLink',
                array(
                    'is_safe'       => array('html'),
                )
            ),
            'autoLinkUrls' => new \Twig_Filter_Method($this, 'autoLinkUrls',
                array(
                    'is_safe'       => array('html'),
                )
            ),
            'autoLinkEmails' => new \Twig_Filter_Method($this, 'autoLinkEmails',
                array(
                    'is_safe'       => array('html'),
                )
            ),
            'stripLinks' => new \Twig_Filter_Method($this, 'stripLinks',
                array(
                    'is_safe'       => array('html'),
                )
            ),
        );
    }

    /**
     * Provides link_to function which wraps HtmlHelper::link().
     *
     * @param $var      String  String to be truncated.
     * @param $length   Integer Length to truncate.
     * @param $options  Array   Options
     *
     * Default options: 'ending' => '...', 'exact' => true, 'html' => false
     *
     * Set 'html' option to true if you want truncate
     * to handle HTML in the string correctly.
     *
     */
    public function truncate($var, $length = 100, array $options = array())
    {
        return $this->textHelper->truncate($var, $length, $options);
    }

    /**
     * TextHelper::highlight()
     *
     * Highlights $needle inside filtered $var
     *
     * @param $var
     * @param $needle
     * @param array $options
     *
     * Options:
     *  'format' => '<span class="highlight">\1</span>'
     *  'html' => true // Will ignore any HTML tags
     *
     * @return string
     */
    public function highlight($var, $needle, array $options = array())
    {
        return $this->textHelper->highlight($var, $needle, $options);
    }

    /**
     * Convert all links and email addresses to HTML links.
     *
     * ### Options
     *
     * - `escape` Control HTML escaping of input. Defaults to true.
     *
     * @param $var Text
     * @param array $options Array of HTML options
     * @return string The text with links
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::autoLink
     */
    public function autoLink($var, array $options = array())
    {
        return $this->textHelper->autoLink($var, $options);
    }

    /**
     * Adds links (<a href=....) to a given text, by finding text that begins with
     * strings like http:// and ftp://.
     *
     * ### Options
     *
     * - `escape` Control HTML escaping of input. Defaults to true.
     *
     * @param $var Text
     * @param array $options Array of options
     * @return string The text with links.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::autoLinkUrls
     */
    public function autoLinkUrls($var, array $options = array())
    {
        return $this->textHelper->autoLinkUrls($var, $options);
    }

    /**
     * Adds email links (<a href="mailto:....) to a given text.
     *
     * ### Options
     *
     * - `escape` Control HTML escaping of input. Defaults to true.
     *
     * @param $var Text
     * @param array $options Array of options
     * @return string Text with email links.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::autoLinkEmails
     */
    public function autoLinkEmails($var, array $options = array())
    {
        return $this->textHelper->autoLinkEmails($var, $options);
    }

    /**
     * @see String::stripLinks()
     *
     * @param $var Text
     * @return string The text without links.
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/text.html#TextHelper::stripLinks
     */
    public function stripLinks($var)
    {
        return $this->textHelper->stripLinks($var);
    }

    public function getName()
    {
        return 'TextHelper';
    }
}