<?php

namespace App;

class Filter
{
    private $text = '';
    private $filters = [];

    /**
     * Constructor for Filter class.
     * @param string $text
     * @param array $filters
    */
    public function __construct($text = '', $filters = ['addLaTeX', 'scapeHtmlSpecialChars', 'addLinks', 'addImages', 'addRichFormat', 'addParagraphs']) {
        $this->text = $text;
        $this->filters = $filters;
    }

    /**
     * Get html code after applying filters on text.
     *
     * @return string
     */
    public function render() {
        $content = $this->text;

        foreach ($this->filters as $filter) {
            $method = '_' . $filter;
            if (method_exists(new Filter(), $method)) {
                $content = call_user_func(Filter::class . '::'. $method, $content);
            }
        }
        return $content;
    }

    /**
     * Call htmlspecialchars().
     *
     * @param string $text
     * @return string
     */
    public function _scapeHtmlSpecialChars($text = ''){
        return htmlspecialchars($text);
    }

    /**
     * Remove line breaks in math enviroment.
     *
     * @param string $text
     * @return string
     */
    public function _addLaTeX($text = '') {
        //inline codes
        $codes = $this->findCode("\\(", "\\)", $text);
        foreach($codes as $code) {
            $cleanCode = str_replace("\r\n", '', $code);
            $text = str_replace($code, $cleanCode, $text);
        }

        //paragraph codes
        $codes = $this->findCode('$$', '$$', $text);
        foreach($codes as $code) {
            $cleanCode = str_replace("\r\n", '', $code);
            $text = str_replace($code, $cleanCode, $text);
        }

        return $text;
    }

    /**
     * Replace Rich Format special tags with html code.
     *
     * @param string $text
     * @return string
     */
    public function _addRichFormat($text = '') {
        //bold
        $codes = $this->findCode('[b]', '[/b]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[b]', '<strong>', $richCode);
            $richCode = str_replace('[/b]', '</strong>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //italic
        $codes = $this->findCode('[i]', '[/i]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[i]', '<em>', $richCode);
            $richCode = str_replace('[/i]', '</em>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //underline
        $codes = $this->findCode('[u]', '[/u]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[u]', '<u>', $richCode);
            $richCode = str_replace('[/u]', '</u>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //code
        $codes = $this->findCode('[code]', '[/code]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[code]', '<code>', $richCode);
            $richCode = str_replace('[/code]', '</code>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //unorded list
        $codes = $this->findCode('[ul]', '[/ul]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[ul]', '<ul>', $richCode);
            $richCode = str_replace('[/ul]', '</ul>', $richCode);
            $richCode = str_replace('[li]', '<li>', $richCode);
            $richCode = str_replace('[/li]', '</li>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //orded list
        $codes = $this->findCode('[ol]', '[/ol]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[ol]', '<ol>', $richCode);
            $richCode = str_replace('[/ol]', '</ol>', $richCode);
            $richCode = str_replace('[li]', '<li>', $richCode);
            $richCode = str_replace('[/li]', '</li>', $richCode);
            $text = str_replace($code, $richCode, $text);
        }

        //table
        $codes = $this->findCode('[table]', '[/table]', $text);
        foreach($codes as $code) {
            $richCode = str_replace("\r\n", '', $code);
            $richCode = str_replace('[table]', '<table>', $richCode);
            $richCode = str_replace('[/table]', '</table>', $richCode);
            $richCode = str_replace('[tr]', '<tr>', $richCode);
            $richCode = str_replace('[/tr]', '</tr>', $richCode);
            $richCode = str_replace('[th]', '<th>', $richCode);
            $richCode = str_replace('[/th]', '</th>', $richCode);
            $richCode = str_replace('[td]', '<td>', $richCode);
            $richCode = str_replace('[/td]', '</td>', $richCode);
            $b = $text;
            $text = str_replace($code, $richCode, $text);
        }

        return $text;
    }

    /**
     * Explode text at Rich Format special tags. 
     *
     * @param string $start
     * @param string $end
     * @param string $text
     * @return array
     */
    public function findCode($start = '', $end = '', $text = '') {
        $posStart = false;
        $posEnd = false;

        $posStart = strpos($text, $start);
        if ($posStart !== false) {
            $aux = substr($text, $posStart + strlen($start), strlen($text) - ($posStart + strlen($start)));
            $posEnd = strpos($aux, $end) + $posStart + strlen($start);
        }

        if ($posEnd - $posStart > 0) {
            $v = substr($text, $posStart, $posEnd - $posStart + strlen($end));
            $aux = substr($text, $posEnd + strlen($end), strlen($text) - ($posEnd + strlen($end)));
            return array_merge($this->findCode($start, $end, $aux), [$v]);
        }else{
            return [];
        }
    }

    /**
     * Replace line breaks with paragraph html tag.
     *
     * @param string $text
     * @return string
     */
    public function _addParagraphs($text = ''){
        $lines = explode("\r\n", $text);

        if (count($lines) < 2) {
            $text = '<p>' . implode('', $lines) . '</p>';
        }else{
            $text = '';
            foreach ($lines as $line) {
                if (empty($line)) {
                    $text .= "<br>\n";
                }else{
                    $regex = '/(<div>|<div .*>).*<\/div>/i';
                    preg_match($regex, $line, $matchesDiv);
                    $regex = '/(<ul>|<ul .*>).*<\/ul>/i';
                    preg_match($regex, $line, $matchesUl);
                    $regex = '/(<ol>|<ol .*>).*<\/ol>/i';
                    preg_match($regex, $line, $matchesOl);
                    $regex = '/(<table>|<table .*>).*<\/table>/i';
                    preg_match($regex, $line, $matchesTable);
                    if (empty($matchesDiv) && empty($matchesUl) && empty($matchesOl) && empty($matchesTable)){
                        $text .= '<p>' . $line . "</p>\n";
                    }else{
                        $text .= $line . "\n";
                    }
                }
            }
        }
        return $text;
    }

    /**
     * Replace image special tag with image html code.
     *
     * @param string $text
     * @return string
     */
    public function _addImages($text = '') {
        $regex = '/(\[img\]|\[img[\ a-z0-9=]+\]).+\[\/img\]/i';
        $lines = explode("\r\n", $text);
        $text = '';
        foreach($lines as $line) {
            if (empty($line)) {
                $text .= "\r\n";
            }else{
                preg_match_all($regex, $line, $matches);
                if (empty(array_unique($matches[0]))) {
                    $text .= $line . "\r\n";
                }else{
                    $newline = $line;
                    foreach (array_unique($matches[0]) as $image){
                        $src = $this->extractSrc($image);
                        $align = $this->extractAlign($image);
                        $height = $this->extractHeight($image);
                        $width = $this->extractWidth($image);
                        $style = $width > 0 ? 'width:' . $width . 'px;' : '';
                        $style = $height > 0 ? $style . 'height:' . $height . 'px;' : $style;
                        $style = empty($style) ? 'max-width:640px;max-height:360px;' : $style;
                        $start = strpos($line, '[img');
                        if (empty($align) || $start > 0) {
                            $img = '<img src="' . asset($src) . '" style="' . $style . '">';
                        }else{
                            $img = '<div style="text-align:' . $align . ';"><img src="' . asset($src) . '" style="' . $style . '"></div>';
                        }
                        $newline = str_replace($image, $img, $newline);
                    }
                    $text .= $newline . "\r\n";
                }
            }
        }
        return $text;
    }

    /**
     * Get source attribute for image special tag.
     *
     * @param string $image
     * @return string
     */
    protected function extractSrc($image = ''){
        $start = strpos($image, ']') + 1;
        $src = substr($image, $start, strlen($image) - ($start + 6));
        return $src;
    }

    /**
     * Get align attribute for image special tag.
     *
     * @param string $image
     * @return string
     */
    protected function extractAlign($image = ''){
        $regex = '/align=(left|center|right)/i';
        $a = '';
        preg_match_all($regex, $image, $matches);
        foreach (array_unique($matches[0]) as $align){
            $start = 6;
            $a = substr($align, $start, strlen($align) - $start);
        }
        return $a;
    }

    /**
     * Get height attribute for image special tag.
     *
     * @param string $image
     * @return string
     */
    protected function extractHeight($image = ''){
        $regex = '/height=[0-9]{1,4}/i';
        $h = 0;
        preg_match_all($regex, $image, $matches);
        foreach (array_unique($matches[0]) as $height){
            $start = 7;
            $h = substr($height, $start, strlen($height) - $start);
        }
        return $h;
    }

    /**
     * Get width attribute for image special tag.
     *
     * @param string $image
     * @return string
     */
    protected function extractWidth($image = ''){
        $regex = '/width=[0-9]{1,4}/i';
        $w = 0;
        preg_match_all($regex, $image, $matches);
        foreach (array_unique($matches[0]) as $width){
            $start = 6;
            $w = substr($width, $start, strlen($width) - $start);
        }
        return $w;
    }

    /**
     * Replace link text with link html code
     *
     * @param string $text
     * @return string
     */
    public function _addLinks($text = ''){
        //find and replace all links
        $text = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a href="$1">$1</a>', $text);
        //add "http://" if not set
        $text = preg_replace('/<a\s[^>]*href\s*=\s*"((?!https?:\/\/)[^"]*)"[^>]*>/i', '<a href="http://$1">', $text);
        
        return $text;
    }

    /**
     * Return a shortened text.
     *
     * @param string $text
     * @param integer $maxChars
     * @return string
     */
    public function short($text = '', $maxChars = 65){
        $newline = "\r\n";
        $lines = explode($newline, $text);
        $suffix = "…";
        $sd = '';
       
        if (strlen($lines[0]) < $maxChars) {
            $sd = $lines[0];
        }else{
            $sd = mb_substr($lines[0], 0, $maxChars, 'UTF-8');
        }

        $pos = strpos($sd, '[');

        if($pos !== false && $pos > 0) {
            $sd = substr($sd, 0, $pos);
        }

        if (!empty($sd)) {
            if($sd[strlen($sd)-1] == ".") {
                $sd[strlen($sd)-1] = "\0";
            }
            
            if (strlen($sd) > $maxChars) {
                $sd .= $suffix;
            }
        }
        if (empty($sd)) dd('filter', $text, $lines);
        if (empty($sd) && count($lines) > 1) {
            $lines = array_diff($lines, [$lines[0]]);
            $text = implode($newline, $lines);
            return $this->short($text, $maxChars);
        }else{
            return $sd;
        }
    }
}