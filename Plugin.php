<?php

// namespace TypechoPlugin\AliveTitle;

use Typecho\Plugin as Typecho_Plugin;
use Typecho\Plugin\PluginInterface as Typecho_Plugin_Interface;
use Typecho\Widget\Helper\Form as Typecho_Widget_Helper_Form;


if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 让你的标题动起来!&nbsp;
 * 无其他依赖,采用原生 ES6 编写.
 * 
 * 
 * @package AliveTitle
 * @author 酢豚
 * @version 1.0.0
 * @link https://blueeyeswhitedragon.xyz/
 * 
 * 
 * 0.9.1 更新内容:修改了实现方法(JQ->ES6),更贴近描述
 * 
 * 0.9.2 更新内容:简化了代码逻辑
 * 
 * 0.9.3 更新内容:简化实现逻辑,去除伪静态(其实是不会写两大服务器的伪静态规则)
 * 
 * 0.9.4 更新内容:暂时使用对象传参,参数传参对于字符串 + 数字形式不太理想
 * 
 * 0.9.5 更新内容:重构,并引入了新的 bug
 * 
 * 1.0.0 更新内容:解决了 PJAX 的兼容问题
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
            _t('是否启用标题滚动'),
        );

        $scrollSpeed = new Typecho\Widget\Helper\Form\Element\Text(
            'scrollSpeed',
            NULL,
            '800',
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
            _t('关闭则下方相关选项全部失效')
        );

        $replaceScroll = new Typecho\Widget\Helper\Form\Element\Radio(
            'replaceScroll',
            [True => 'True', False => 'False'],
            False,
            _t('是否启用失焦滚动'),
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
            '|･ω･｀)你看不见我……你看不见我……你看不见我……',
            _t('失去焦点时'),
            _t('当前页面处于不可见时触发,可见时点击页面以外区域不触发')
        );

        $getFocus = new Typecho\Widget\Helper\Form\Element\Text(
            'getFocus',
            NULL,
            '_(:3」」还是被发现了',
            _t('重新获得焦点时'),
            _t('如不需要和上面保持一致即可')
        );

        $pjax = new Typecho\Widget\Helper\Form\Element\Radio(
            'pjax',
            [True => 'True', False => 'False'],
            False,
            _t('兼容 PJAX 和 AJAX'),
            _t('原理是注册一个点击监听,如果不需要不建议启用<br />
            或者找到自己主题提供的重载接口在 alivetitle.js 中添加并填入 `title = document.title;` 保持 False 即可生效<br />
            示例:&nbsp;<code>Aria.reloadAction = () => {<br />
                title = document.title;<br />
            }</code>')
        );

        $form->addInput($titleScroll); // 是否滚动
        $form->addInput($scrollSpeed); // 滚动速度
        $form->addInput($titleLength); // 标题长度
        $form->addInput($titleReplace); // 是否替换
        $form->addInput($replaceScroll); // 是否失焦滚动
        $form->addInput($replaceTimeout); // 替换延时
        $form->addInput($lostFocus); // 失焦文本
        $form->addInput($getFocus); // 重新获焦文本
        $form->addInput($pjax); // 重新获焦文本
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
        replaceScroll: $options->replaceScroll,// 是否替换
        replaceTimeout: $options->replaceTimeout,// 替换延时
        lostFocus: `$options->lostFocus`,
        getFocus: `$options->getFocus`,
        pjax_ajax: $options->pjax
    }
</script>
JS;
        echo PHP_EOL . '<script type="text/javascript" src="' . __TYPECHO_PLUGIN_DIR__ . '/AliveTitle/alivetitle.js" test="测试消息"></script>' . PHP_EOL;
    }
}
