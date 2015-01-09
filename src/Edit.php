<?php

namespace BonsaiEdit;

class Edit 
{
    
    /**
     * Access content tree as a list
     *
     * @param  boolean $withContent
     * @param  boolean $withTop
     * @return array
     */
    public function getTree($node, $withContent = false, $withTop = true, $preview = false)
    {
        $tree = $node->getTreeArray($withContent);

        return $this->parseTreeArray($tree, $withTop, $preview);
    }

    /**
     * Parse content tree array as list
     *
     * @param  mixed $content
     * @param  boolean $top
     * @return array
     */
    public function parseTreeArray($content, $top = true, $preview = false)
    {
        if (is_array($content['content'])) {
            $content['subcontent'] = '';
            foreach ($content['content'] as $entry) {
                $content['subcontent'] .= $this->parseTreeArray($entry, false, $preview);
            }

            $renderer = new \BonsaiEdit\Render\Renderer();
            $output = $renderer->renderContent('treebranch', json_decode(json_encode($content), true), $content, null);

        } else {
            $renderer = new \BonsaiEdit\Render\Renderer();
            $output = $renderer->renderContent('treeleaf', json_decode(json_encode($content), true), $content, null);
        }

        if ($top) {
            $output = $this->cleanseOutput($output);
        }

        return $output;
    }

    /**
     * Remove iframes and anchor tags from preview content
     *
     * @param  string $content
     * @return array
     */
    public static function cleanseOutput($content, $process = array('iframe', 'a'))
    {
        $content = str_replace("&", "&amp;", $content);
        $dom = new \DOMDocument();
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        if (in_array('iframe', $process)) {
            $results = $xpath->query("//iframe");
            foreach ($results as $result) {
                $result->parentNode->removeChild($result);
            }
        }

        if (in_array('a', $process)) {
            $results = $xpath->query("//a");
            foreach ($results as $result) {
                $children = $result->childNodes;
                foreach ($children as $child) {
                    $result->parentNode->appendChild($child);
                }
                $result->parentNode->removeChild($result);
            }
        }

        $output = $dom->saveHTML($dom->getElementsByTagName('ol')->item(0));
        return $output;
    }
}
