<?php

namespace App;

class Menu
{
    protected $items = [];
    protected $type;
    protected $class;

    /**
     * Create a new menu instance.
     *
     * @return void
     */
    public function __construct($type = 'ul', $class = [])
    {
        $this->type = $type;
        $this->class = $class;
    }

    /**
     * Add an item in items array.
     *
     * @param string $url
     * @param string $text
     * @param array $class
     * @param string $title
     * @return array
     */
    public function add($url = '', $text = '', $class = [], $title = ''){
        array_push($this->items, ['url' => $url, 'icon' => null, 'text' => $text, 'class' => $class, 'title' => $title]);
    }

    /**
     * Add an item with icon in items array.
     *
     * @param string $url
     * @param array $icon
     * @param string $text
     * @param array $class
     * @param boolean $insideLink
     * @param string $title
     * @return array
     */
    public function addWithIcon($url = '', $icon = [], $text = '', $class = [], $insideLink = false, $title = ''){
        array_push($this->items, ['url' => $url, 'icon' => $icon, 'text' => $text, 'class' => $class, 'title' => $title, 'insideLink' => $insideLink]);
    }

    /**
     * Get the html code for a link.
     *
     * @param string $url
     * @param string $text
     * @param array $class
     * @param string $title
     * @return string
     */
    public function link($url = '', $text = '', $class = [], $title = '') {
        $item = ['url' => $url, 'text' => $text, 'title' => $title, 'class' => $class];
        return '<a href="' . 
            $item['url'] . '"' .
            (empty($item['title']) ? '' : ' title="' . $item['title'] . '" ') .
            $this->classToStr($item['class']). '>' .
            $item['text'] . '</a>';
    }

    /**
     * Get the html code for a class attribute.
     *
     * @param array $class
     * @return string
     */
    public function classToStr($class = []) {
        return empty($class) ? '' : ' class="' . implode(' ', $class) . '"';
    }
    
    /**
     * Get the html code for menu.
     *
     * @return string
     */
    public function render(){
        $html = '<' . $this->type . $this->classToStr($this->class) . '>';

        foreach ($this->items as $item){
            if(empty($item['url'])) {
                $title = empty($item['title']) ? '' : ' title = "' . $item['title'] . '"';
                if(isset($item['icon'])) {
                    $html .= '<li'. $title . '><i' . $this->classToStr($item['icon']) . '></i> ';
                    $html .= $item['text'] . '</li>';
                }else{
                    $html .= '<li' . $title . '><span' . $this->classToStr($item['class']) . '>';
                    $html .= $item['text'] . '</span></li>';
                }
            }else{
                if(isset($item['icon'])) {
                    $text = empty($item['text']) ? '' : ' ' . $item['text'];
                    $icon = '<i' . $this->classToStr($item['icon']) . '></i>';
                    if($item['insideLink']) {
                        $html .= '<li>' . $this->link($item['url'], $icon . $text, $item['class'], $item['title']);
                        $html .= '</li>';
                    }else{
                        $html .= '<li>' . $icon . ' ';
                        $html .= $this->link($item['url'], $item['text'], $item['class'], $item['title']) . '</li>';
                    }
                }else{
                    $html .= '<li>';
                    $html .= $this->link($item['url'], $item['text'], $item['class'], $item['title']) . '</li>';
                }
            }
        }

        $html .= '</' . $this->type . '>';

        return $html;
    }
}