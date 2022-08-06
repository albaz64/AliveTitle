<?php

// namespace TypechoPlugin\AliveTitle;

use Typecho\Plugin as Typecho_Plugin;
use Typecho\Plugin\PluginInterface as Typecho_Plugin_Interface;
use Typecho\Widget\Helper\Form as Typecho_Widget_Helper_Form;


if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 让你的标题动起来.
 * 无任何依赖,采用ES6编写.
 * 
 * 
 * @package AliveTitle
 * @author 酢豚
 * @version 0.9.4
 * @link https://blueeyeswhitedragon.xyz/
 * 
 * 
 * 0.9.1 更新内容:修改了实现方法(JQ->ES6),更贴近描述
 * 
 * 0.9.2 更新内容:简化了代码逻辑,去掉了华而不实的功能
 * 
 * 0.9.3 更新内容:简化实现逻辑,去除伪静态(外链引用改为嵌入,这也是大部分插件的做法)
 * 
 * 0.9.4 更新内容:暂时使用变量赋值,传参方式不太优雅
 * 
 * 0.9.5 更新内容:重构
 */
class AliveTitle_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array('AliveTitle_Plugin', 'aliveTitle');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {

        $titleScroll = new Typecho\Widget\Helper\Form\Element\Radio(
            'titleScroll',
            [True => 'True', False => 'False'],
            True,
            _t('是否启用滚动'),
        );

        $scrollSpeed = new Typecho\Widget\Helper\Form\Element\Text(
            'scrollSpeed',
            NULL,
            '1200',
            _t('标题滚动的速度'),
            _t('即多少毫秒滚动一个字,不建议过快')
        );

        $titleLength = new Typecho\Widget\Helper\Form\Element\Text(
            'titleLength',
            NULL,
            '12',
            _t('滚动长度阈值(闭区间)'),
            _t('取值建议在 6 ~ 15 之间')
        );

        $titleReplace = new Typecho\Widget\Helper\Form\Element\Radio(
            'titleReplace',
            [True => 'True', False => 'False'],
            True,
            _t('是否启用失焦替换'),
        );

        $replaceTimeout = new Typecho\Widget\Helper\Form\Element\Text(
            'replaceTimeout',
            NULL,
            '1200',
            _t('延时恢复标题'),
            _t('立即恢复填 0,单位毫秒')
        );

        $lostFocus = new Typecho\Widget\Helper\Form\Element\Text(
            'lostFocus',
            NULL,
            '你一定又在看别的女人罢!',
            _t('失去焦点时'),
            _t('当前页面处于不可见时触发,可见时点击页面以外区域不触发')
        );

        $getFocus = new Typecho\Widget\Helper\Form\Element\Text(
            'getFocus',
            NULL,
            '我也不差啊,再看看我',
            _t('重新获得焦点时'),
            _t('如不需要和上面保持一致即可')
        );

        $form->addInput($titleScroll); // 是否滚动
        $form->addInput($scrollSpeed); // 滚动速度
        $form->addInput($titleLength); // 标题长度
        $form->addInput($titleReplace); // 是否替换
        $form->addInput($replaceTimeout); // 替换延时
        $form->addInput($lostFocus); // 失焦
        $form->addInput($getFocus); // 重新获焦
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function aliveTitle()
    {
        $options = Utils\Helper::options()->plugin('AliveTitle');
        echo <<<JS
<script>
    let alivetitle = {
        scroll: $options->titleScroll,
        scroolSpeed: $options->scrollSpeed,// 滚动速度
        titleLength: $options->titleLength,
        replace: $options->titleReplace,
        replaceTimeout: $options->replaceTimeout,// 替换延时
        lostFocus: `$options->lostFocus`,
        getFocus: `$options->getFocus`
    }
</script>
JS;
        echo '<script type="text/javascript" src="' . __TYPECHO_PLUGIN_DIR__ . '/AliveTitle/alivetitle.js"></script>' . PHP_EOL;
    }
}
