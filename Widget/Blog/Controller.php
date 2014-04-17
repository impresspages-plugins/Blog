<?php
namespace Plugin\Blog\Widget\Blog;

class Controller extends \Ip\WidgetController
{
    public function getTitle()
    {
        return __('Blog', 'ipAdmin');
    }

    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        $result = '';
        $blogMenu = ipContent()->getPage('blog');
        if (!$blogMenu) {
            return '';
        }
        $pages = $blogMenu->getChildren();
        $block = 'main';
        //$language = ipContent()->getLanguageByCode($blogMenu->getLanguageCode());
        foreach ($pages as $page) {


            $revision = \Ip\Internal\Revision::getPublishedRevision($page->getId());
            if (!$revision) {
                continue;
            }
            $widgets = \Ip\Internal\Content\Model::getBlockWidgetRecords($block, $revision['revisionId'], 0);
            if (empty($widgets)) {
                continue;
            }

            $result .= '<div class="ipPluginBlogDate">'.date('\<\s\p\a\n\>j\<\/\s\p\a\n\>\<\b\r\/\>M\<\b\r\/\>Y', strtotime($page->getCreatedAt())).'</div>';

            $author = '<div class="ipPluginBlogAuthor">ImpressPages Team</div>';
            foreach ($widgets as $key => $widget) {
                if ($key == 0) {
                    if ($widget['name'] == 'Heading') {
                        $widget['data']['level'] = 2;
                        $widget['data']['link'] = $page->getLink();
                        $result .= \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData('Heading', $widget['data']);
                        $result .= $author;
                        continue;
                    } else {
                        $result .= \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData('Heading', array(
                            'heading' => $page->getMetaTitle() ? $page->getMetaTitle() : $page->getTitle(),
                            'level' => 2,
                            'link' => $page->getLink()
                        ));
                        $result .= $author;
                    }

                }

                if ($widget['name'] == 'LeadBreak') {
                    break;
                }

                $result .= \Ip\Internal\Content\Model::generateWidgetPreview($widget['id'], false);
            }

            $result .= \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData('Text', array(
                'text' => '<p><a href="'.escAttr($page->getLink()).'">' . __('Read more...', 'Blog') . '</a></p>'
            ));
            $result .= \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData('Divider', array(), 'gray');
        }
        return $result;
    }
}
